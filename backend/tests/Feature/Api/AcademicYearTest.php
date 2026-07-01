<?php

namespace Tests\Feature\Api;

use App\Models\AcademicYear;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearTest extends TestCase
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

    public function test_can_list_academic_years(): void
    {
        AcademicYear::create([
            'year_name' => '2026',
            'start_date' => '2026-02-03',
            'end_date' => '2026-12-04',
            'is_current' => true,
        ]);

        $response = $this->getJson('/api/academic-years', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_create_academic_year(): void
    {
        $response = $this->postJson('/api/academic-years', [
            'year_name' => '2027',
            'start_date' => '2027-02-01',
            'end_date' => '2027-12-15',
            'is_current' => false,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonFragment(['year_name' => '2027']);
    }

    public function test_can_set_current_year(): void
    {
        $year1 = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $year2 = AcademicYear::create([
            'year_name' => '2027', 'start_date' => '2027-02-01', 'end_date' => '2027-12-15', 'is_current' => false,
        ]);

        $response = $this->postJson("/api/academic-years/{$year2->id}/set-current", [], $this->authHeaders());

        $response->assertStatus(200);

        $this->assertDatabaseHas('academic_years', ['id' => $year1->id, 'is_current' => 0]);
        $this->assertDatabaseHas('academic_years', ['id' => $year2->id, 'is_current' => 1]);
    }

    public function test_can_show_academic_year(): void
    {
        $year = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);

        $response = $this->getJson("/api/academic-years/{$year->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['year_name' => '2026']);
    }

    public function test_can_update_academic_year(): void
    {
        $year = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => false,
        ]);

        $response = $this->putJson("/api/academic-years/{$year->id}", [
            'year_name' => '2026',
            'start_date' => '2026-02-01',
            'end_date' => '2026-12-01',
            'is_current' => false,
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertEquals('2026-02-01', AcademicYear::find($year->id)->start_date->format('Y-m-d'));
    }

    public function test_can_delete_academic_year(): void
    {
        $year = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => false,
        ]);

        $response = $this->deleteJson("/api/academic-years/{$year->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('academic_years', ['id' => $year->id]);
    }

    public function test_validation_requires_unique_year_name(): void
    {
        AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04',
        ]);

        $response = $this->postJson('/api/academic-years', [
            'year_name' => '2026',
            'start_date' => '2026-02-01',
            'end_date' => '2026-12-01',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['year_name']);
    }

    public function test_create_requires_valid_dates(): void
    {
        $response = $this->postJson('/api/academic-years', [
            'year_name' => '2027',
            'start_date' => '2027-12-01',
            'end_date' => '2027-01-01',
        ], $this->authHeaders());

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }
}
