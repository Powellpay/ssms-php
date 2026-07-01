<?php

namespace Tests\Feature\Api;

use App\Models\Role;
use App\Models\SkillRatingScale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillRatingScaleTest extends TestCase
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

    public function test_can_create_skill_rating(): void
    {
        $response = $this->postJson('/api/skill-rating-scale', [
            'rating_code' => 'BEG',
            'rating_label' => 'Beginning',
            'rating_value' => 1,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['rating_code' => 'BEG']);
    }

    public function test_can_list_skill_ratings(): void
    {
        SkillRatingScale::insert([
            ['rating_code' => 'BEG', 'rating_label' => 'Beginning', 'rating_value' => 1],
            ['rating_code' => 'DEV', 'rating_label' => 'Developing', 'rating_value' => 2],
        ]);

        $response = $this->getJson('/api/skill-rating-scale', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_update_skill_rating(): void
    {
        $rating = SkillRatingScale::create([
            'rating_code' => 'BEG', 'rating_label' => 'Beginning', 'rating_value' => 1,
        ]);

        $response = $this->putJson("/api/skill-rating-scale/{$rating->id}", [
            'rating_code' => 'BEG',
            'rating_label' => 'Beginner',
            'rating_value' => 1,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['rating_label' => 'Beginner']);
    }

    public function test_can_delete_skill_rating(): void
    {
        $rating = SkillRatingScale::create([
            'rating_code' => 'EXP', 'rating_label' => 'Expert', 'rating_value' => 4,
        ]);

        $response = $this->deleteJson("/api/skill-rating-scale/{$rating->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('skill_rating_scale', ['id' => $rating->id]);
    }

    public function test_create_requires_unique_code(): void
    {
        SkillRatingScale::create([
            'rating_code' => 'BEG', 'rating_label' => 'Beginning', 'rating_value' => 1,
        ]);

        $response = $this->postJson('/api/skill-rating-scale', [
            'rating_code' => 'BEG',
            'rating_label' => 'Duplicate',
            'rating_value' => 1,
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating_code']);
    }
}
