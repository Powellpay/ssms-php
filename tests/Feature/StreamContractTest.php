<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Models\Stream;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamContractTest extends TestCase
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

    private function resourceKeys(): array
    {
        return [
            'id', 'class_level_id', 'academic_year_id', 'stream_name',
            'class_teacher_id', 'created_at', 'updated_at',
        ];
    }

    private function createYear(): AcademicYear
    {
        return AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
    }

    private function createLevel(): ClassLevel
    {
        return ClassLevel::create(['level_name' => 'S1', 'numeric_level' => 1]);
    }

    public function test_list_streams_structure(): void
    {
        $year = $this->createYear();
        $level = $this->createLevel();
        Stream::create([
            'class_level_id' => $level->id, 'academic_year_id' => $year->id, 'stream_name' => 'East',
        ]);
        Stream::create([
            'class_level_id' => $level->id, 'academic_year_id' => $year->id, 'stream_name' => 'West',
        ]);

        $response = $this->getJson('/api/v1/streams', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['*' => $this->resourceKeys()]]);

        $json = $response->json();
        $this->assertCount(2, $json['data']);

        foreach ($json['data'] as $stream) {
            $this->assertIsInt($stream['id']);
            $this->assertIsInt($stream['class_level_id']);
            $this->assertIsInt($stream['academic_year_id']);
            $this->assertIsString($stream['stream_name']);
        }
    }

    public function test_create_stream(): void
    {
        $year = $this->createYear();
        $level = $this->createLevel();

        $response = $this->postJson('/api/v1/streams', [
            'class_level_id' => $level->id,
            'academic_year_id' => $year->id,
            'stream_name' => 'North',
        ], $this->authHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure($this->resourceKeys());
        $this->assertEquals('North', $response->json('stream_name'));
        $this->assertDatabaseHas('streams', ['stream_name' => 'North']);
    }

    public function test_create_stream_validation(): void
    {
        $this->postJson('/api/v1/streams', [], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['class_level_id', 'academic_year_id', 'stream_name']);
    }

    public function test_show_stream(): void
    {
        $year = $this->createYear();
        $level = $this->createLevel();
        $stream = Stream::create([
            'class_level_id' => $level->id, 'academic_year_id' => $year->id, 'stream_name' => 'South',
        ]);

        $response = $this->getJson("/api/v1/streams/{$stream->id}", $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure($this->resourceKeys());
        $this->assertEquals('South', $response->json('stream_name'));
    }

    public function test_update_stream(): void
    {
        $year = $this->createYear();
        $level = $this->createLevel();
        $stream = Stream::create([
            'class_level_id' => $level->id, 'academic_year_id' => $year->id, 'stream_name' => 'East',
        ]);

        $response = $this->putJson("/api/v1/streams/{$stream->id}", [
            'class_level_id' => $level->id,
            'academic_year_id' => $year->id,
            'stream_name' => 'West',
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertEquals('West', $response->json('stream_name'));
        $this->assertDatabaseHas('streams', ['id' => $stream->id, 'stream_name' => 'West']);
    }

    public function test_delete_stream(): void
    {
        $year = $this->createYear();
        $level = $this->createLevel();
        $stream = Stream::create([
            'class_level_id' => $level->id, 'academic_year_id' => $year->id, 'stream_name' => 'East',
        ]);

        $response = $this->deleteJson("/api/v1/streams/{$stream->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('streams', ['id' => $stream->id]);
    }

    public function test_auth_required_for_stream_endpoints(): void
    {
        $this->getJson('/api/v1/streams')->assertStatus(401);
        $this->getJson('/api/v1/streams/1')->assertStatus(401);
        $this->postJson('/api/v1/streams', [])->assertStatus(401);
        $this->putJson('/api/v1/streams/1', [])->assertStatus(401);
        $this->deleteJson('/api/v1/streams/1')->assertStatus(401);
    }
}
