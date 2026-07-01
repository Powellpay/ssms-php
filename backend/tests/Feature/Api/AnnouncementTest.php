<?php

namespace Tests\Feature\Api;

use App\Models\Announcement;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $role = Role::create(['role_name' => 'Administrator']);
        $user = User::create([
            'role_id' => $role->id,
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        $this->token = $user->createToken('test-token')->plainTextToken;
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_announcement(): void
    {
        $response = $this->postJson('/api/announcements', [
            'title' => 'Holiday',
            'message' => 'School closed',
            'target_role' => 'all',
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_announcements(): void
    {
        Announcement::create([
            'title' => 'Holiday', 'message' => 'School closed', 'target_role' => 'all',
        ]);

        $response = $this->getJson('/api/announcements', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_delete_announcement(): void
    {
        $announcement = Announcement::create([
            'title' => 'Holiday', 'message' => 'School closed', 'target_role' => 'all',
        ]);

        $response = $this->deleteJson("/api/announcements/{$announcement->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }
}
