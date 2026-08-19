<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PengumumanController extends Controller
{
    public function index()
    {
        $query = Pengumuman::with(['creator', 'kelasMapel.kelas', 'kelasMapel.mataPelajaran'])->orderByDesc('created_at');
        if (Auth::user()->isGuru()) {
            $guruKelasIds = KelasMapel::where('guru_id', Auth::id())->pluck('kelas_id')->unique()->values();
            $query->where(fn($q) => $q->whereIn('target',['semua','guru'])->orWhere('created_by',Auth::id())->orWhere(fn($q) => $q->where('target','kelas_mapel')->where(fn($q) => $q->whereIn('kelas_mapel_id',KelasMapel::where('guru_id',Auth::id())->select('id'))->orWhereIn('target_kelas',$guruKelasIds))));
        }
        if (Auth::user()->role?->nama_role === 'kepala_sekolah') $query->where(fn($q) => $q->whereIn('target',['semua','guru'])->orWhere('created_by',Auth::id()));
        $pengumuman = $query->paginate(15)->withQueryString();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasMapel = KelasMapel::with(['kelas','mataPelajaran'])->when(Auth::user()->isGuru(),fn($q)=>$q->where('guru_id',Auth::id()))->orderBy('kelas_id')->get();
        $targetKelasOptions = Auth::user()->isGuru() ? $kelasMapel->pluck('kelas')->filter()->unique('id')->sortBy(fn(Kelas $k)=>$k->tingkat.' '.$k->nama_kelas)->values() : $kelas;
        return Inertia::render('Admin/Pengumuman/Index', compact('pengumuman','kelas','kelasMapel','targetKelasOptions') + ['routePrefix'=>$this->routePrefix()]);
    }

    public function show(Pengumuman $pengumuman)
    {
        $role = Auth::user()->role?->nama_role; abort_unless($this->canView($pengumuman,$role),403);
        $pengumuman->loadMissing(['creator','kelasMapel.kelas','kelasMapel.mataPelajaran']);
        $targetKelasLabels = Kelas::whereIn('id',$pengumuman->targetKelasIds())->orderBy('tingkat')->orderBy('nama_kelas')->get()->map(fn(Kelas $k)=>trim($k->tingkat.' '.$k->nama_kelas));
        return Inertia::render('Admin/Pengumuman/Show', compact('pengumuman','targetKelasLabels') + ['routePrefix'=>$this->routePrefix()]);
    }

    public function store(Request $request)
    {
        $role=Auth::user()->role?->nama_role; $allowedTargets=match($role){'guru'=>['kelas_mapel'],'admin','kepala_sekolah'=>['semua','guru','siswa','kelas_mapel'],default=>[]}; abort_unless($allowedTargets!==[],403);
        $validated=$request->validate(['judul'=>'required|string|max:200','isi'=>'required|string','target'=>['required',Rule::in($allowedTargets)],'target_kelas_ids'=>'nullable|required_if:target,kelas_mapel|array','target_kelas_ids.*'=>'integer|exists:kelas,id']);
        $validated=$this->prepareTarget($validated); $validated['created_by']=Auth::id(); Pengumuman::create($validated);
        return redirect()->route($this->routePrefix().'.index')->with('success','Pengumuman berhasil dipublikasikan.');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $role=Auth::user()->role?->nama_role; abort_unless($role==='admin'||(int)$pengumuman->created_by===(int)Auth::id(),403); $allowedTargets=match($role){'guru'=>['kelas_mapel'],'admin','kepala_sekolah'=>['semua','guru','siswa','kelas_mapel'],default=>[]};
        $validated=$request->validate(['judul'=>'required|string|max:200','isi'=>'required|string','target'=>['required',Rule::in($allowedTargets)],'target_kelas_ids'=>'nullable|required_if:target,kelas_mapel|array','target_kelas_ids.*'=>'integer|exists:kelas,id']);
        $pengumuman->update($this->prepareTarget($validated)); return redirect()->route($this->routePrefix().'.index')->with('success','Pengumuman berhasil diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $role=Auth::user()->role?->nama_role; abort_unless($role==='admin'||(int)$pengumuman->created_by===(int)Auth::id(),403); $pengumuman->delete(); return redirect()->route($this->routePrefix().'.index')->with('success','Pengumuman berhasil dihapus.');
    }

    private function prepareTarget(array $validated): array
    {
        if ($validated['target']==='kelas_mapel') {
            $ids=collect($validated['target_kelas_ids']??[])->map(fn($id)=>(int)$id)->unique()->values(); if($ids->isEmpty()) throw ValidationException::withMessages(['target_kelas_ids'=>'Pilih minimal satu kelas tujuan.']);
            if(Auth::user()->isGuru()){ $allowed=KelasMapel::where('guru_id',Auth::id())->whereIn('kelas_id',$ids)->pluck('kelas_id')->unique(); abort_unless($ids->diff($allowed)->isEmpty(),403); }
            $validated['target_kelas']=$ids->map(fn($id)=>(string)$id)->values()->toJson(); $validated['kelas_mapel_id']=KelasMapel::whereIn('kelas_id',$ids)->value('id');
        } else { $validated['target_kelas']=null; $validated['kelas_mapel_id']=null; }
        unset($validated['target_kelas_ids']); return $validated;
    }

    private function routePrefix(): string { return match(Auth::user()->role?->nama_role){'guru'=>'guru.pengumuman','kepala_sekolah'=>'kepsek.pengumuman',default=>'admin.pengumuman'}; }
    private function canView(Pengumuman $pengumuman, ?string $role): bool
    {
        if($role==='admin') return true; if($role==='kepala_sekolah') return in_array($pengumuman->target,['semua','guru'],true)||(int)$pengumuman->created_by===(int)Auth::id(); if($role==='guru'){if(in_array($pengumuman->target,['semua','guru'],true)||(int)$pengumuman->created_by===(int)Auth::id())return true;$ids=$pengumuman->targetKelasIds();return $pengumuman->target==='kelas_mapel'&&KelasMapel::whereIn('kelas_id',$ids)->where('guru_id',Auth::id())->exists();} return false;
    }
}
