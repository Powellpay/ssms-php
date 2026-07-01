<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private Role $role;
    private array $userData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = Role::create(['role_name' => 'Administrator', 'description' => 'Full access']);
        $password = 'Password123!';
        $this->userData = [
            'role_id' => $this->role->id,
            'username' => 'testadmin',
            'name' => 'Test Admin',
            'email' => 'test@school.ug',
            'password' => bcrypt($password),
            'status' => 'active',
        ];
    }

    private function loginResponse(): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/auth/login', [
            'email' => 'test@school.ug',
            'password' => 'Password123!',
        ]);
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'role_id' => $this->role->id,
            'username' => 'newuser',
            'name' => 'New User',
            'email' => 'new@school.ug',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token']);

        $this->assertDatabaseHas('users', ['email' => 'new@school.ug']);
    }

    public function test_user_can_login(): void
    {
        User::create($this->userData);

        $response = $this->loginResponse();

        $response->assertStatus(200)
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::create($this->userData);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@school.ug',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Invalid credentials']);
    }

    public function test_login_fails_with_nonexistent_user(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nobody@school.ug',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_authenticated_user_can_access_me(): void
    {
        User::create($this->userData);
        $loginResponse = $this->loginResponse();

        $token = $loginResponse->json('token');

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonFragment(['email' => 'test@school.ug']);
    }

    public function test_unauthenticated_user_cannot_access_me(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    public function test_user_can_logout(): void
    {
        User::create($this->userData);
        $loginResponse = $this->loginResponse();

        $token = $loginResponse->json('token');

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }

    public function test_token_is_invalidated_after_logout(): void
    {
        User::create($this->userData);
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@school.ug',
            'password' => 'Password123!',
        ]);

        $token = $loginResponse->json('token');
        $this->assertNotNull($token);

        $this->assertEquals(1, \Laravel\Sanctum\PersonalAccessToken::count());

        $logoutResponse = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/auth/logout');
        $logoutResponse->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);

        $this->assertEquals(0, \Laravel\Sanctum\PersonalAccessToken::count());
    }

    public function test_register_requires_valid_role(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'role_id' => 99999,
            'username' => 'newuser',
            'name' => 'New User',
            'email' => 'new@school.ug',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role_id']);
    }

    public function test_register_requires_unique_email(): void
    {
        User::create($this->userData);

        $response = $this->postJson('/api/auth/register', [
            'role_id' => $this->role->id,
            'username' => 'another',
            'name' => 'Another',
            'email' => 'test@school.ug',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
