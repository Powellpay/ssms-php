<?php

namespace Tests\Feature\Api;

use App\Domain\Assessment\Models\GradingScale;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingScaleTest extends TestCase
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

    public function test_can_create_grading_scale(): void
    {
        $response = $this->postJson('/api/grading-scale', [
            'grade' => 'A',
            'descriptor' => 'Exceptional',
            'min_score' => 80,
            'max_score' => 100,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['grade' => 'A']);
    }

    public function test_can_list_grading_scales(): void
    {
        GradingScale::insert([
            ['grade' => 'A', 'descriptor' => 'Exceptional', 'min_score' => 80, 'max_score' => 100, 'remarks' => ''],
            ['grade' => 'B', 'descriptor' => 'Outstanding', 'min_score' => 70, 'max_score' => 79.99, 'remarks' => ''],
        ]);

        $response = $this->getJson('/api/grading-scale', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_update_grading_scale(): void
    {
        $grade = GradingScale::create([
            'grade' => 'C', 'descriptor' => 'Satisfactory', 'min_score' => 55, 'max_score' => 69.99,
        ]);

        $response = $this->putJson("/api/grading-scale/{$grade->id}", [
            'grade' => 'C',
            'descriptor' => 'Good',
            'min_score' => 55,
            'max_score' => 69.99,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['descriptor' => 'Good']);
    }

    public function test_can_delete_grading_scale(): void
    {
        $grade = GradingScale::create([
            'grade' => 'E', 'descriptor' => 'Elementary', 'min_score' => 0, 'max_score' => 39.99,
        ]);

        $response = $this->deleteJson("/api/grading-scale/{$grade->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('grading_scale', ['id' => $grade->id]);
    }

    public function test_requires_min_less_than_max(): void
    {
        $response = $this->postJson('/api/grading-scale', [
            'grade' => 'A',
            'descriptor' => 'Exceptional',
            'min_score' => 90,
            'max_score' => 80,
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_score']);
    }
}
