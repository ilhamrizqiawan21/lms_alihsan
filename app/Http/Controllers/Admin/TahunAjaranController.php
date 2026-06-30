<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderBy('tahun', 'desc')->get();
        return view('admin.tahun-ajaran.index', compact('tahunAjaran'));
    }
    //Menyimpan Tahun Ajaran Baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|string|max:20|unique:tahun_ajaran,tahun',
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_active')) {
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        TahunAjaran::create($validated);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }
    //Edit Tahun Ajaran
    public function update(Request $request, TahunAjaran $tahunAjaran)
    {
        $validated = $request->validate([
            'tahun' => 'required|string|max:20|unique:tahun_ajaran,tahun,' . $tahunAjaran->id,
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_active')) {
            TahunAjaran::where('is_active', true)->where('id', '!=', $tahunAjaran->id)
                ->update(['is_active' => false]);
        }

        $tahunAjaran->update($validated);

        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }
    //Hapus Tahun Ajaran
    public function destroy(TahunAjaran $tahunAjaran)
    {
        $tahunAjaran->delete();
        return redirect()->route('admin.tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
    //Set Tahun Ajaran Aktif
    public function setAktif(TahunAjaran $tahunAjaran)
    {
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        $tahunAjaran->update(['is_active' => true]);

        return back()->with('success', "Tahun ajaran {$tahunAjaran->tahun} sekarang aktif.");
    }
}
