<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CoreAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seedRoles();

        Route::middleware(['auth', 'role:admin'])
            ->get('/__tests__/admin-only', fn () => response('ok'))
            ->name('tests.admin-only');
    }

    public function test_login_by_username_redirects_to_role_dashboard(): void
    {
        $user = $this->makeUser('admin', 'admin-test');

        $response = $this->post(route('login.post'), [
            'username' => $user->username,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_remain_authenticated(): void
    {
        $user = $this->makeUser('admin', 'inactive-admin', false);

        $response = $this->post(route('login.post'), [
            'username' => $user->username,
            'password' => 'secret-password',
        ]);

        $response->assertSessionHas('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
        $this->assertGuest();
    }

    public function test_role_middleware_rejects_authenticated_user_with_wrong_role(): void
    {
        $user = $this->makeUser('guru', 'guru-test');

        $response = $this->actingAs($user)->get('/__tests__/admin-only');

        $response->assertForbidden();
    }

    public function test_role_middleware_accepts_authenticated_user_with_matching_role(): void
    {
        $user = $this->makeUser('admin', 'admin-access');

        $response = $this->actingAs($user)->get('/__tests__/admin-only');

        $response->assertOk()->assertSee('ok');
    }

    public function test_external_intended_url_is_not_used_after_login(): void
    {
        $user = $this->makeUser('admin', 'redirect-admin');

        $this->withSession(['url.intended' => 'https://attacker.example/admin/panel']);

        $response = $this->post(route('login.post'), [
            'username' => $user->username,
            'password' => 'secret-password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    private function seedRoles(): void
    {
        Role::create(['id' => 1, 'nama_role' => 'admin']);
        Role::create(['id' => 2, 'nama_role' => 'guru']);
        Role::create(['id' => 3, 'nama_role' => 'siswa']);
        Role::create(['id' => 4, 'nama_role' => 'kepala_sekolah']);
    }

    private function makeUser(string $role, string $username, bool $active = true): User
    {
        $roleId = Role::where('nama_role', $role)->value('id');

        return User::create([
            'username' => $username,
            'email' => $username . '@example.test',
            'password' => Hash::make('secret-password'),
            'is_password_default' => false,
            'nama_lengkap' => strtoupper($username),
            'nip_nis' => null,
            'jenis_kelamin' => null,
            'foto' => null,
            'role_id' => $roleId,
            'is_active' => $active,
        ]);
    }
}
