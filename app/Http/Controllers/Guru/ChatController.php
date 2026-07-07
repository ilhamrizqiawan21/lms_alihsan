<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\KelasMapel;
use App\Models\Notifikasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $kelasMapel = KelasMapel::with(['kelas', 'mataPelajaran'])
            ->where('guru_id', Auth::id())
            ->aktif()
            ->get();

        return view('guru.chat.index', compact('kelasMapel'));
    }
    //Pengaturan chat sesuai dengan guru mata pelajaran dan kelas yang diampu
    public function chat(KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $messages = ChatMessage::with('user')
            ->where('kelas_mapel_id', $kelasMapel->id)
            ->orderBy('created_at', 'asc')
            ->get();

        ChatMessage::where('kelas_mapel_id', $kelasMapel->id)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_read' => true]);

        return view('guru.chat.show', compact('kelasMapel', 'messages'));
    }
    //Kirim pesan
    public function send(Request $request, KelasMapel $kelasMapel)
    {
        $this->authorize('mengajar', $kelasMapel);

        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $chat = ChatMessage::create([
            'user_id' => Auth::id(),
            'kelas_mapel_id' => $kelasMapel->id,
            'message' => $validated['message'],
        ]);

        $siswas = Siswa::where('kelas_id', $kelasMapel->kelas_id)
            ->where('status', 'aktif')
            ->get();

        foreach ($siswas as $siswa) {
            Notifikasi::create([
                'user_id' => $siswa->user_id,
                'tipe' => 'chat_baru',
                'judul' => 'Pesan baru dari guru',
                'pesan' => 'Pesan: ' . $validated['message'],
                'link' => route('siswa.chat.show', $kelasMapel),
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $chat->load('user')]);
        }

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
