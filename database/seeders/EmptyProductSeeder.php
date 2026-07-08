<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use App\Models\Role;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmptyProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->call(SchoolSettingSeeder::class);
        $this->seedAdmin();
        $this->seedAcademicDefaults();
    }

    private function seedRoles(): void
    {
        $roles = [
            ['id' => 1, 'nama_role' => 'admin'],
            ['id' => 2, 'nama_role' => 'guru'],
            ['id' => 3, 'nama_role' => 'siswa'],
            ['id' => 4, 'nama_role' => 'kepala_sekolah'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['id' => $role['id']],
                ['nama_role' => $role['nama_role']]
            );
        }
    }

    private function seedAdmin(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@demo.test',
                'nama_lengkap' => 'Administrator',
                'nip_nis' => 'ADM-DEFAULT-001',
                'jenis_kelamin' => null,
                'password' => Hash::make('password'),
                'role_id' => 1,
                'is_active' => true,
            ]
        );
    }

    private function seedAcademicDefaults(): void
    {
        TahunAjaran::where('is_active', true)->update(['is_active' => false]);

        $tahunAjaran = TahunAjaran::updateOrCreate(
            ['tahun' => '2026/2027'],
            ['is_active' => true]
        );

        Pengaturan::setValue('tahun_ajaran_aktif', (string) $tahunAjaran->id);
        Pengaturan::setValue('semester_aktif', '1');
    }
}
