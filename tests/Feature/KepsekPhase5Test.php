<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KepsekPhase5Test extends TestCase
{
    use RefreshDatabase;

    private function kepsek(): User
    {
        $role = Role::create(['id' => 1, 'nama_role' => 'kepala_sekolah']);

        return User::create([
            'username' => 'kepsek-phase5',
            'email' => 'kepsek-phase5@example.test',
            'password' => Hash::make('secret'),
            'is_password_default' => false,
            'nama_lengkap' => 'Kepala Sekolah Phase 5',
            'nip_nis' => null,
            'jenis_kelamin' => null,
            'foto' => null,
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    public function test_kepsek_phase5_monitoring_pages_are_available(): void
    {
        $user = $this->kepsek();

        $pages = [
            '/kepsek/dashboard' => 'Kepsek/Dashboard',
            '/kepsek/statistik' => 'Kepsek/Statistik/Index',
            '/kepsek/kalender' => 'Kepsek/Kalender/Index',
            '/kepsek/pengumuman' => 'Kepsek/Pengumuman/Index',
            '/kepsek/laporan/absensi' => 'Kepsek/Laporan/Absensi',
            '/kepsek/laporan/nilai' => 'Kepsek/Laporan/Nilai',
            '/kepsek/laporan/rekap-absensi' => 'Kepsek/Laporan/RekapAbsensi',
            '/kepsek/laporan/rekap-tugas' => 'Kepsek/Laporan/RekapTugas',
            '/kepsek/laporan/rekap-sikap' => 'Kepsek/Laporan/RekapSikap',
            '/kepsek/laporan/wali-kelas' => 'Kepsek/Laporan/WaliKelas',
        ];

        foreach ($pages as $url => $component) {
            $this->actingAs($user)
                ->get($url)
                ->assertOk()
                ->assertInertia(fn ($page) => $page->component($component));
        }
    }

    public function test_kepsek_cannot_mutate_calendar_or_announcements(): void
    {
        $user = $this->kepsek();

        $this->actingAs($user)
            ->post('/kepsek/kalender', [
                'title' => 'Tidak boleh',
                'event_date' => '2026-08-19',
                'scope' => 'school',
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->post('/kepsek/pengumuman', [
                'judul' => 'Tidak boleh',
                'isi' => 'Tidak boleh membuat pengumuman.',
                'target' => 'semua',
            ])
            ->assertForbidden();
    }
}
