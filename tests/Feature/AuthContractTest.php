<?php

namespace Tests\Feature;

use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthContractTest extends TestCase
{
    use RefreshDatabase;

    private Role $role;
    private string $token;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = Role::create(['role_name' => 'Administrator', 'description' => 'Full access']);
        $this->user = User::create([
            'role_id' => $this->role->id,
            'username' => 'testadmin',
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('Password123!'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $this->token = $this->user->createToken('auth-token')->plainTextToken;
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . $this->token];
    }

    private function userResourceKeys(): array
    {
        return [
            'id', 'role_id', 'role_slug', 'username', 'name', 'email',
            'phone', 'avatar', 'status', 'email_verified_at', 'last_login',
            'school_name', 'is_school_admin', 'modules', 'created_at', 'updated_at',
        ];
    }

    public function test_register_returns_expected_structure(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'New User',
            'email' => 'new@school.ug',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'school_name' => 'Test School',
            'username' => 'newuser',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success', 'code', 'message',
            'user' => $this->userResourceKeys(),
            'token',
            'school' => ['id', 'name', 'email'],
        ]);

        $json = $response->json();
        $this->assertTrue($json['success']);
        $this->assertEquals('REGISTRATION_SUCCESS', $json['code']);
        $this->assertNull($json['token']);
        $this->assertEquals('Test School', $json['school']['name']);
        $this->assertIsInt($json['school']['id']);
        $this->assertEquals('new@school.ug', $json['user']['email']);
        $this->assertEquals('newuser', $json['user']['username']);
        $this->assertIsInt($json['user']['id']);
        $this->assertIsString($json['user']['name']);
        $this->assertIsBool($json['user']['is_school_admin']);
        $this->assertIsArray($json['user']['modules']);
        $this->assertDatabaseHas('users', ['email' => 'new@school.ug']);
        $this->assertDatabaseHas('schools', ['name' => 'Test School']);
    }

    public function test_login_returns_expected_structure(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success', 'code',
            'user' => $this->userResourceKeys(),
            'token',
        ]);

        $json = $response->json();
        $this->assertTrue($json['success']);
        $this->assertEquals('LOGIN_SUCCESS', $json['code']);
        $this->assertIsString($json['token']);
        $this->assertNotEmpty($json['token']);
        $this->assertEquals('admin@test.com', $json['user']['email']);
    }

    public function test_login_with_unverified_email(): void
    {
        $unverified = User::create([
            'role_id' => $this->role->id,
            'username' => 'unverified',
            'name' => 'Unverified User',
            'email' => 'unverified@test.com',
            'password' => bcrypt('Password123!'),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'unverified@test.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'code', 'message', 'user_id']);

        $json = $response->json();
        $this->assertFalse($json['success']);
        $this->assertEquals('EMAIL_NOT_VERIFIED', $json['code']);
        $this->assertIsInt($json['user_id']);
    }

    public function test_login_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure(['success', 'code', 'message']);

        $json = $response->json();
        $this->assertFalse($json['success']);
        $this->assertEquals('INVALID_CREDENTIALS', $json['code']);
    }

    public function test_login_nonexistent_user(): void
    {
        $this->postJson('/api/v1/auth/login', [
            'email' => 'nobody@school.ug',
            'password' => 'Password123!',
        ])
            ->assertStatus(401)
            ->assertJson(['code' => 'INVALID_CREDENTIALS']);
    }

    public function test_me_returns_user_resource(): void
    {
        $token = $this->user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200);

        $json = $response->json();
        $this->assertArrayHasKey('data', $json);
        $userData = $json['data'];
        $this->assertEquals($this->user->id, $userData['id']);
        $this->assertEquals($this->user->email, $userData['email']);
        $this->assertEquals($this->user->username, $userData['username']);
        $this->assertEquals('active', $userData['status']);
        $this->assertIsBool($userData['is_school_admin']);
        $this->assertIsArray($userData['modules']);
    }

    public function test_logout(): void
    {
        $token = $this->user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertEquals('Logged out successfully', $response->json('message'));
    }

    public function test_token_is_invalidated_after_logout(): void
    {
        $token = $this->user->createToken('auth-token')->plainTextToken;

        $this->withHeaders(['Authorization' => "Bearer $token"])
            ->postJson('/api/v1/auth/logout');

        $this->assertEquals(0, \Laravel\Sanctum\PersonalAccessToken::count());
    }

    public function test_auth_required_for_me(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_auth_required_for_logout(): void
    {
        $this->postJson('/api/v1/auth/logout')->assertStatus(401);
    }

    public function test_login_validation_errors(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_register_requires_unique_email(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'name' => 'Another',
            'email' => 'admin@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'username' => 'another',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_auto_creates_school(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'School Founder',
            'email' => 'founder@school.ug',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'school_name' => 'My Academy',
            'username' => 'founder',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('schools', ['name' => 'My Academy']);
        $this->assertEquals('My Academy', $response->json('school.name'));
    }
}
