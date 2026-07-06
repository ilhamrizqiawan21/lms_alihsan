<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan.');
        }

        $kelasMapel = KelasMapel::with(['mataPelajaran', 'guru'])
            ->where('kelas_id', $siswa->kelas_id)
            ->whereHas('tahunAjaran', fn($q) => $q->where('is_active', true))
            ->get();

        return view('siswa.chat.index', compact('kelasMapel'));
    }
    //Tampilan chat yang difilter sesuai dengan guru dan mapel yang dipilih oleh siswa, dan menampilkan pesan yang sudah dibaca dan belum dibaca
    public function show(KelasMapel $kelasMapel)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        $this->ensureKelasMapelAktifUntukSiswa($kelasMapel, $siswa);

        $messages = ChatMessage::with('user')
            ->where('kelas_mapel_id', $kelasMapel->id)
            ->orderBy('created_at', 'asc')
            ->get();

        ChatMessage::where('kelas_mapel_id', $kelasMapel->id)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return view('siswa.chat.show', compact('kelasMapel', 'messages'));
    }
    //Kirim Pesan
    public function send(Request $request, KelasMapel $kelasMapel)
    {
        $user = Auth::user();
        $siswa = $user->siswa;

        $this->ensureKelasMapelAktifUntukSiswa($kelasMapel, $siswa);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $chat = ChatMessage::create([
            'user_id' => Auth::id(),
            'kelas_mapel_id' => $kelasMapel->id,
            'message' => $validated['message'],
        ]);

        // Kirim notifikasi ke guru
        if ($kelasMapel->guru_id) {
            Notifikasi::create([
                'user_id' => $kelasMapel->guru_id,
                'tipe' => 'chat_baru',
                'judul' => 'Pesan baru dari siswa',
                'pesan' => $user->nama_lengkap . ': ' . Str::limit($validated['message'], 100),
                'link' => route('guru.chat.show', $kelasMapel),
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $chat->load('user')]);
        }

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    private function ensureKelasMapelAktifUntukSiswa(KelasMapel $kelasMapel, ?Siswa $siswa): void
    {
        $kelasMapelAktif = $kelasMapel->exists
            ? $kelasMapel->tahunAjaran()->where('is_active', true)->exists()
            : true;

        abort_unless(
            $siswa
            && (int) $siswa->kelas_id === (int) $kelasMapel->kelas_id
            && $kelasMapelAktif,
            403
        );
    }
}
