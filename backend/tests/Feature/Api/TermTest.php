<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\Role;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TermTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private AcademicYear $academicYear;

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

        $this->academicYear = AcademicYear::create([
            'year_name' => '2026',
            'start_date' => '2026-02-03',
            'end_date' => '2026-12-04',
            'is_current' => true,
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_list_terms(): void
    {
        Term::create([
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 1',
            'start_date' => '2026-02-03',
            'end_date' => '2026-05-02',
        ]);
        Term::create([
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 2',
            'start_date' => '2026-05-19',
            'end_date' => '2026-08-29',
        ]);

        $response = $this->getJson('/api/terms', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_term(): void
    {
        $response = $this->postJson('/api/terms', [
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 3',
            'start_date' => '2026-09-15',
            'end_date' => '2026-12-04',
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['term_name' => 'Term 3']);
    }

    public function test_can_show_term(): void
    {
        $term = Term::create([
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 1',
            'start_date' => '2026-02-03',
            'end_date' => '2026-05-02',
        ]);

        $response = $this->getJson("/api/terms/{$term->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['term_name' => 'Term 1']);
    }

    public function test_can_update_term(): void
    {
        $term = Term::create([
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 1',
            'start_date' => '2026-02-03',
            'end_date' => '2026-05-02',
        ]);

        $response = $this->putJson("/api/terms/{$term->id}", [
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 1',
            'start_date' => '2026-02-03',
            'end_date' => '2026-05-15',
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertEquals('2026-05-15', Term::find($term->id)->end_date->format('Y-m-d'));
    }

    public function test_can_delete_term(): void
    {
        $term = Term::create([
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 1',
            'start_date' => '2026-02-03',
            'end_date' => '2026-05-02',
        ]);

        $response = $this->deleteJson("/api/terms/{$term->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('terms', ['id' => $term->id]);
    }

    public function test_create_requires_valid_dates(): void
    {
        $response = $this->postJson('/api/terms', [
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Invalid Term',
            'start_date' => '2026-12-01',
            'end_date' => '2026-01-01',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }
}
