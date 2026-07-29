<?php

namespace Tests\Feature;

use App\Domain\Announcements\Models\Announcement;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementContractTest extends TestCase
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

    public function test_list_announcements_structure(): void
    {
        Announcement::create([
            'title' => 'Midterm Break', 'message' => 'School closed for midterm.',
            'target_role' => 'all',
        ]);

        $response = $this->getJson('/api/v1/announcements', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'message',
                        'target_role',
                        'created_by',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_announcement(): void
    {
        $response = $this->postJson('/api/v1/announcements', [
            'title' => 'Sports Day',
            'message' => 'Sports day is on Friday.',
            'target_role' => 'Student',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'message',
                'target_role',
                'created_by',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment(['title' => 'Sports Day']);
    }

    public function test_show_announcement(): void
    {
        $announcement = Announcement::create([
            'title' => 'Staff Meeting', 'message' => 'Meeting at 3pm.',
            'target_role' => 'Teacher',
        ]);

        $response = $this->getJson("/api/v1/announcements/{$announcement->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'message',
                'target_role',
                'created_by',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_update_announcement(): void
    {
        $announcement = Announcement::create([
            'title' => 'Old Title', 'message' => 'Old message.',
            'target_role' => 'all',
        ]);

        $response = $this->putJson("/api/v1/announcements/{$announcement->id}", [
            'title' => 'Updated Title',
            'message' => 'Updated message.',
            'target_role' => 'all',
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'message',
                'target_role',
                'created_by',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_delete_announcement(): void
    {
        $announcement = Announcement::create([
            'title' => 'Delete Test', 'message' => 'To be deleted.',
            'target_role' => 'all',
        ]);

        $response = $this->deleteJson("/api/v1/announcements/{$announcement->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('announcements', ['id' => $announcement->id]);
    }

    public function test_announcement_validation_missing_title(): void
    {
        $response = $this->postJson('/api/v1/announcements', [
            'message' => 'No title provided.',
            'target_role' => 'all',
        ], $this->authHeaders());

        $response->assertStatus(422);
    }
}
