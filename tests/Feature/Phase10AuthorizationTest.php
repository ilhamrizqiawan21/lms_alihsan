<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\KelasMapel;
use App\Models\MataPelajaran;
use App\Models\Role;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('pdo_sqlite')]
class Phase10AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_cannot_delete_another_gurus_task_by_id(): void
    {
        [, $guruA, $guruB, $kelas, $tahunAjaran] = $this->fixture();
        $mapel = MataPelajaran::create(['kode' => 'MTK', 'nama_mapel' => 'Matematika', 'urutan' => 1]);

        $kelasMapel = KelasMapel::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guruB->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => '1',
            'pertemuan_per_minggu' => 2,
        ]);

        $tugas = Tugas::create([
            'kelas_mapel_id' => $kelasMapel->id,
            'judul' => 'Tugas Guru B',
            'deskripsi' => 'Data yang harus tetap dimiliki Guru B.',
            'batas_waktu' => now()->addDay(),
            'kategori_nilai' => 'NH',
        ]);

        $this->actingAs($guruA)
            ->delete(route('guru.tugas.destroy', $tugas))
            ->assertForbidden();

        $this->assertDatabaseHas('tugas', ['id' => $tugas->id]);
    }

    public function test_owner_guru_can_delete_own_task(): void
    {
        [, $guruA, , $kelas, $tahunAjaran] = $this->fixture();
        $mapel = MataPelajaran::create(['kode' => 'IPA', 'nama_mapel' => 'IPA', 'urutan' => 2]);

        $kelasMapel = KelasMapel::create([
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'guru_id' => $guruA->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'semester' => '1',
            'pertemuan_per_minggu' => 2,
        ]);

        $tugas = Tugas::create([
            'kelas_mapel_id' => $kelasMapel->id,
            'judul' => 'Tugas Guru A',
            'deskripsi' => 'Boleh dihapus pemiliknya.',
            'batas_waktu' => now()->addDay(),
            'kategori_nilai' => 'NH',
        ]);

        $this->actingAs($guruA)
            ->delete(route('guru.tugas.destroy', $tugas))
            ->assertRedirect();

        $this->assertDatabaseMissing('tugas', ['id' => $tugas->id]);
    }

    public function test_admin_student_password_reset_uses_generated_temporary_password(): void
    {
        [$admin, , , $kelas, ] = $this->fixture();
        $studentUser = $this->createUser('siswa-reset', 'Siswa Reset', 'siswa');
        $siswa = Siswa::create([
            'user_id' => $studentUser->id,
            'nis' => '9001',
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.kelas-siswa.reset-password', $siswa))
            ->assertRedirect()
            ->assertSessionHas('student_password');

        $payload = session('student_password');
        $studentUser->refresh();

        $this->assertIsArray($payload);
        $this->assertNotSame(User::DEFAULT_PASSWORD, $payload['password']);
        $this->assertTrue(Hash::check($payload['password'], $studentUser->password));
        $this->assertTrue((bool) $studentUser->is_password_default);
    }

    public function test_student_cannot_use_admin_student_reset_endpoint(): void
    {
        [, , , $kelas, ] = $this->fixture();
        $studentUser = $this->createUser('siswa-attacker', 'Siswa Attacker', 'siswa');
        $targetUser = $this->createUser('siswa-target', 'Siswa Target', 'siswa');
        $target = Siswa::create([
            'user_id' => $targetUser->id,
            'nis' => '9002',
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
        ]);

        $this->actingAs($studentUser)
            ->post(route('admin.kelas-siswa.reset-password', $target))
            ->assertForbidden();
    }

    private function fixture(): array
    {
        Role::create(['nama_role' => 'admin']);
        Role::create(['nama_role' => 'guru']);
        Role::create(['nama_role' => 'siswa']);
        Role::create(['nama_role' => 'kepala_sekolah']);

        $admin = $this->createUser('admin-phase10', 'Admin Phase 10', 'admin');
        $guruA = $this->createUser('guru-a-phase10', 'Guru A', 'guru');
        $guruB = $this->createUser('guru-b-phase10', 'Guru B', 'guru');
        $kelas = Kelas::create(['tingkat' => 'VII', 'nama_kelas' => 'A']);
        $tahunAjaran = TahunAjaran::create(['tahun' => '2026/2027', 'is_active' => true]);

        return [$admin, $guruA, $guruB, $kelas, $tahunAjaran];
    }

    private function createUser(string $username, string $namaLengkap, string $roleName): User
    {
        $role = Role::where('nama_role', $roleName)->firstOrFail();

        return User::create([
            'username' => $username,
            'email' => "{$username}@test.local",
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'nama_lengkap' => $namaLengkap,
            'is_active' => true,
        ]);
    }
}
