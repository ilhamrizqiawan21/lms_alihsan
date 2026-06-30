<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'nama_lengkap' => 'Administrator',
            'email' => 'admin@alihsan.sch.id',
            'password' => Hash::make('admin123'),
            'role_id' => 1,
            'is_active' => true,
        ]);

        // User guru contoh
        User::create([
            'username' => 'guru',
            'nama_lengkap' => 'Guru Contoh, S.Pd.',
            'email' => 'guru@alihsan.sch.id',
            'password' => Hash::make('guru123'),
            'role_id' => 2,
            'is_active' => true,
        ]);

        // User siswa contoh
        User::create([
            'username' => 'siswa',
            'nama_lengkap' => 'Siswa Contoh',
            'email' => 'siswa@alihsan.sch.id',
            'password' => Hash::make('siswa123'),
            'role_id' => 3,
            'is_active' => true,
        ]);

        // User kepala sekolah contoh
        User::create([
            'username' => 'kepsek',
            'nama_lengkap' => 'Kepala Sekolah, S.Pd., M.Pd.',
            'email' => 'kepsek@alihsan.sch.id',
            'password' => Hash::make('kepsek123'),
            'role_id' => 4,
            'is_active' => true,
        ]);
    }
}
