<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TermContractTest extends TestCase
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

    private function resourceKeys(): array
    {
        return [
            'id', 'academic_year_id', 'term_name', 'start_date', 'end_date',
            'next_term_begins', 'is_current', 'created_at', 'updated_at',
        ];
    }

    public function test_list_terms_structure(): void
    {
        Term::create([
            'academic_year_id' => $this->academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-02',
        ]);
        Term::create([
            'academic_year_id' => $this->academicYear->id, 'term_name' => 'Term 2',
            'start_date' => '2026-05-19', 'end_date' => '2026-08-29',
        ]);

        $response = $this->getJson('/api/v1/terms', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['*' => $this->resourceKeys()]]);

        $json = $response->json();
        $this->assertCount(2, $json['data']);

        foreach ($json['data'] as $term) {
            $this->assertIsInt($term['id']);
            $this->assertIsInt($term['academic_year_id']);
            $this->assertIsString($term['term_name']);
            $this->assertIsBool($term['is_current']);
        }
    }

    public function test_create_term(): void
    {
        $response = $this->postJson('/api/v1/terms', [
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 1',
            'start_date' => '2026-02-03',
            'end_date' => '2026-05-02',
        ], $this->authHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure($this->resourceKeys());
        $this->assertEquals('Term 1', $response->json('term_name'));
        $this->assertDatabaseHas('terms', ['term_name' => 'Term 1']);
    }

    public function test_create_term_validation(): void
    {
        $this->postJson('/api/v1/terms', [], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['academic_year_id', 'term_name', 'start_date', 'end_date']);
    }

    public function test_create_term_date_validation(): void
    {
        $this->postJson('/api/v1/terms', [
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Invalid',
            'start_date' => '2026-12-01',
            'end_date' => '2026-01-01',
        ], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_show_term(): void
    {
        $term = Term::create([
            'academic_year_id' => $this->academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-02',
        ]);

        $response = $this->getJson("/api/v1/terms/{$term->id}", $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure($this->resourceKeys());
        $this->assertEquals('Term 1', $response->json('term_name'));
    }

    public function test_update_term(): void
    {
        $term = Term::create([
            'academic_year_id' => $this->academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-02',
        ]);

        $response = $this->putJson("/api/v1/terms/{$term->id}", [
            'academic_year_id' => $this->academicYear->id,
            'term_name' => 'Term 1',
            'start_date' => '2026-02-03',
            'end_date' => '2026-05-15',
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertEquals('2026-05-15', Term::find($term->id)->end_date->format('Y-m-d'));
    }

    public function test_delete_term(): void
    {
        $term = Term::create([
            'academic_year_id' => $this->academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-02',
        ]);

        $response = $this->deleteJson("/api/v1/terms/{$term->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('terms', ['id' => $term->id]);
    }

    public function test_auth_required_for_term_endpoints(): void
    {
        $this->getJson('/api/v1/terms')->assertStatus(401);
        $this->getJson('/api/v1/terms/1')->assertStatus(401);
        $this->postJson('/api/v1/terms', [])->assertStatus(401);
        $this->putJson('/api/v1/terms/1', [])->assertStatus(401);
        $this->deleteJson('/api/v1/terms/1')->assertStatus(401);
    }
}
