<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PengumumanController extends Controller
{
    public function index()
    {
        $query = Pengumuman::with(['creator','kelasMapel.kelas','kelasMapel.mataPelajaran'])->orderByDesc('created_at');
        if (Auth::user()->isGuru()) {
            $guruKelasIds=KelasMapel::where('guru_id',Auth::id())->pluck('kelas_id')->unique()->values();
            $query->where(function($q)use($guruKelasIds){$q->whereIn('target',['semua','guru'])->orWhere('created_by',Auth::id())->orWhere(function($q)use($guruKelasIds){$q->where('target','kelas_mapel')->where(function($q)use($guruKelasIds){$q->whereIn('kelas_mapel_id',KelasMapel::where('guru_id',Auth::id())->select('id'));foreach($guruKelasIds as $id)$q->orWhere('target_kelas','like','%\"'.$id.'\"%');});});});
        }
        if(Auth::user()->role?->nama_role==='kepala_sekolah')$query->where(fn($q)=>$q->whereIn('target',['semua','guru'])->orWhere('created_by',Auth::id()));
        $pengumuman=$query->paginate(15)->withQueryString();
        $pengumuman->through(function (Pengumuman $item) {
            $prefix = $this->routePrefix();
            $item->can_edit = Auth::user()->isAdmin() || (Auth::user()->isGuru() && (int) $item->created_by === (int) Auth::id());
            $item->can_delete = $item->can_edit;
            $item->update_url = route($prefix.'.store');
            $item->delete_url = route($prefix.'.destroy', $item);
            $item->show_url = route($prefix.'.show', $item);
            $item->target_kelas_ids = $item->targetKelasIds();
            return $item;
        });
        $kelas=Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $kelasMapel=KelasMapel::with(['kelas','mataPelajaran'])->when(Auth::user()->isGuru(),fn($q)=>$q->where('guru_id',Auth::id()))->orderBy('kelas_id')->get();
        $targetKelasOptions=Auth::user()->isGuru()?$kelasMapel->pluck('kelas')->filter()->unique('id')->sortBy(fn(Kelas $k)=>$k->tingkat.' '.$k->nama_kelas)->values():$kelas;
        if(Auth::user()->role?->nama_role==='admin')return Inertia::render('Admin/Pengumuman/Index',compact('pengumuman','kelas','kelasMapel','targetKelasOptions')+['routePrefix'=>$this->routePrefix(),'storeUrl'=>route($this->routePrefix().'.store')]);
        if(Auth::user()->role?->nama_role==='kepala_sekolah')return Inertia::render('Kepsek/Pengumuman/Index',compact('pengumuman')+['routePrefix'=>$this->routePrefix()]);
        return view('admin.pengumuman.index',compact('pengumuman','kelas','kelasMapel','targetKelasOptions')+['routePrefix'=>$this->routePrefix()]);
    }

    public function show(Pengumuman $pengumuman)
    {
        $role=Auth::user()->role?->nama_role;abort_unless($this->canView($pengumuman,$role),403);$pengumuman->loadMissing(['creator','kelasMapel.kelas','kelasMapel.mataPelajaran']);$targetKelasLabels=Kelas::whereIn('id',$pengumuman->targetKelasIds())->orderBy('tingkat')->orderBy('nama_kelas')->get()->map(fn(Kelas $k)=>trim($k->tingkat.' '.$k->nama_kelas));
        if($role==='admin')return Inertia::render('Admin/Pengumuman/Show',compact('pengumuman','targetKelasLabels')+['routePrefix'=>$this->routePrefix()]);
        if($role==='kepala_sekolah')return Inertia::render('Kepsek/Pengumuman/Show',compact('pengumuman','targetKelasLabels')+['routePrefix'=>$this->routePrefix()]);
        return view('admin.pengumuman.show',compact('pengumuman','targetKelasLabels')+['routePrefix'=>$this->routePrefix()]);
    }

    public function store(Request $request)
    {
        if ($request->input('action') === 'update') {
            return $this->updateFromStore($request);
        }

        $role=Auth::user()->role?->nama_role;$allowed=match($role){'guru'=>['kelas_mapel'],'admin','kepala_sekolah'=>['semua','guru','siswa','kelas_mapel'],default=>[]};abort_unless($allowed!==[],403);abort_if($role==='kepala_sekolah',403);$v=$request->validate(['judul'=>'required|string|max:200','isi'=>'required|string','target'=>['required',Rule::in($allowed)],'target_kelas_ids'=>'nullable|required_if:target,kelas_mapel|array','target_kelas_ids.*'=>'integer|exists:kelas,id']);$v=$this->prepareTarget($v);$v['created_by']=Auth::id();$pengumuman = Pengumuman::create($v);
        $this->notifyRecipients($pengumuman);
        return redirect()->route($this->routePrefix().'.index')->with('success','Pengumuman berhasil dipublikasikan.');
    }

    public function destroy(Pengumuman $pengumuman){$role=Auth::user()->role?->nama_role;abort_unless($role==='admin'||(int)$pengumuman->created_by===(int)Auth::id(),403);abort_if($role==='kepala_sekolah',403);$pengumuman->delete();return redirect()->route($this->routePrefix().'.index')->with('success','Pengumuman berhasil dihapus.');}

    private function updateFromStore(Request $request)
    {
        $pengumuman = Pengumuman::findOrFail((int) $request->input('pengumuman_id'));
        $role = Auth::user()->role?->nama_role;
        abort_unless($role === 'admin' || ($role === 'guru' && (int) $pengumuman->created_by === (int) Auth::id()), 403);
        abort_if($role === 'kepala_sekolah', 403);

        $allowed = $role === 'guru' ? ['kelas_mapel'] : ['semua','guru','siswa','kelas_mapel'];
        $v = $request->validate([
            'pengumuman_id' => 'required|integer|exists:pengumuman,id',
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'target' => ['required', Rule::in($allowed)],
            'target_kelas_ids' => 'nullable|required_if:target,kelas_mapel|array',
            'target_kelas_ids.*' => 'integer|exists:kelas,id',
        ]);
        $v = $this->prepareTarget($v);
        unset($v['pengumuman_id']);
        $pengumuman->update($v);

        return redirect()->route($this->routePrefix().'.index')->with('success','Pengumuman berhasil diperbarui.');
    }

    private function prepareTarget(array $v):array{if($v['target']==='kelas_mapel'){$ids=collect($v['target_kelas_ids']??[])->map(fn($id)=>(int)$id)->unique()->values();if($ids->isEmpty())throw ValidationException::withMessages(['target_kelas_ids'=>'Pilih minimal satu kelas tujuan.']);if(Auth::user()->isGuru()){$allowed=KelasMapel::where('guru_id',Auth::id())->whereIn('kelas_id',$ids)->pluck('kelas_id')->unique();abort_unless($ids->diff($allowed)->isEmpty(),403);}$v['target_kelas']=$ids->map(fn($id)=>(string)$id)->values()->toJson();$v['kelas_mapel_id']=KelasMapel::whereIn('kelas_id',$ids)->value('id');}else{$v['target_kelas']=null;$v['kelas_mapel_id']=null;}unset($v['target_kelas_ids']);return $v;}
    private function routePrefix():string{return match(Auth::user()->role?->nama_role){'guru'=>'guru.pengumuman','kepala_sekolah'=>'kepsek.pengumuman',default=>'admin.pengumuman'};}
    private function canView(Pengumuman $p,?string $role):bool{if($role==='admin')return true;if($role==='kepala_sekolah')return in_array($p->target,['semua','guru'],true)||(int)$p->created_by===(int)Auth::id();if($role==='guru'){if(in_array($p->target,['semua','guru'],true)||(int)$p->created_by===(int)Auth::id())return true;$ids=$p->targetKelasIds();return $p->target==='kelas_mapel'&&KelasMapel::whereIn('kelas_id',$ids)->where('guru_id',Auth::id())->exists();}return false;}

    private function notifyRecipients(Pengumuman $pengumuman): void
    {
        $query = User::query()->where('is_active', true)->where('id', '!=', Auth::id());
        $target = $pengumuman->target;

        if ($target === 'guru') {
            $query->whereHas('role', fn ($q) => $q->where('nama_role', 'guru'));
        } elseif ($target === 'siswa') {
            $query->whereHas('role', fn ($q) => $q->where('nama_role', 'siswa'));
        } elseif ($target === 'kelas_mapel') {
            $kelasIds = $pengumuman->targetKelasIds();
            $query->whereHas('role', fn ($q) => $q->where('nama_role', 'siswa'))
                ->whereHas('siswa', fn ($q) => $q->whereIn('kelas_id', $kelasIds)->where('status', 'aktif'));
        } else {
            $query->whereHas('role', fn ($q) => $q->whereIn('nama_role', ['guru', 'siswa', 'kepala_sekolah']));
        }

        $users = $query->with('role')->get(['id', 'role_id']);
        foreach ($users as $user) {
            Notifikasi::create([
                'user_id' => $user->id,
                'tipe' => 'pengumuman_baru',
                'judul' => 'Pengumuman baru',
                'pesan' => $pengumuman->judul,
                'link' => $this->notificationLinkForUser($user, $pengumuman),
            ]);
        }
    }

    private function notificationLinkForUser(User $user, Pengumuman $pengumuman): string
    {
        return match ($user->role?->nama_role) {
            'siswa' => route('siswa.pengumuman.show', $pengumuman),
            'guru' => route('guru.pengumuman.show', $pengumuman),
            'kepala_sekolah' => route('kepsek.pengumuman.show', $pengumuman),
            default => route('admin.pengumuman.show', $pengumuman),
        };
    }
}
