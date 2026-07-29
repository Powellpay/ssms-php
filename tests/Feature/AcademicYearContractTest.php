<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearContractTest extends TestCase
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
        return ['id', 'year_name', 'start_date', 'end_date', 'is_current', 'created_at', 'updated_at'];
    }

    public function test_list_academic_years_structure(): void
    {
        AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        AcademicYear::create([
            'year_name' => '2027', 'start_date' => '2027-02-01', 'end_date' => '2027-12-15', 'is_current' => false,
        ]);

        $response = $this->getJson('/api/v1/academic-years', $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['*' => $this->resourceKeys()]]);

        $json = $response->json();
        $this->assertCount(2, $json['data']);

        foreach ($json['data'] as $year) {
            $this->assertIsInt($year['id']);
            $this->assertIsString($year['year_name']);
            $this->assertIsBool($year['is_current']);
        }
    }

    public function test_create_academic_year(): void
    {
        $response = $this->postJson('/api/v1/academic-years', [
            'year_name' => '2027',
            'start_date' => '2027-02-01',
            'end_date' => '2027-12-15',
            'is_current' => false,
        ], $this->authHeaders());

        $response->assertStatus(201);
        $response->assertJsonStructure($this->resourceKeys());

        $json = $response->json();
        $this->assertEquals('2027', $json['year_name']);
        $this->assertFalse($json['is_current']);
        $this->assertDatabaseHas('academic_years', ['year_name' => '2027']);
    }

    public function test_create_academic_year_validation(): void
    {
        $this->postJson('/api/v1/academic-years', [], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['year_name', 'start_date', 'end_date']);
    }

    public function test_create_academic_year_date_validation(): void
    {
        $this->postJson('/api/v1/academic-years', [
            'year_name' => '2027',
            'start_date' => '2027-12-01',
            'end_date' => '2027-01-01',
        ], $this->authHeaders())
            ->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_show_academic_year(): void
    {
        $year = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);

        $response = $this->getJson("/api/v1/academic-years/{$year->id}", $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure($this->resourceKeys());
        $this->assertEquals('2026', $response->json('year_name'));
    }

    public function test_update_academic_year(): void
    {
        $year = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => false,
        ]);

        $response = $this->putJson("/api/v1/academic-years/{$year->id}", [
            'year_name' => '2026',
            'start_date' => '2026-02-01',
            'end_date' => '2026-12-01',
            'is_current' => true,
        ], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertTrue($response->json('is_current'));
        $this->assertEquals('2026-02-01', AcademicYear::find($year->id)->start_date->format('Y-m-d'));
    }

    public function test_delete_academic_year(): void
    {
        $year = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => false,
        ]);

        $response = $this->deleteJson("/api/v1/academic-years/{$year->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
        $this->assertDatabaseMissing('academic_years', ['id' => $year->id]);
    }

    public function test_set_current_academic_year(): void
    {
        $year1 = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $year2 = AcademicYear::create([
            'year_name' => '2027', 'start_date' => '2027-02-01', 'end_date' => '2027-12-15', 'is_current' => false,
        ]);

        $response = $this->postJson("/api/v1/academic-years/{$year2->id}/set-current", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseHas('academic_years', ['id' => $year1->id, 'is_current' => 0]);
        $this->assertDatabaseHas('academic_years', ['id' => $year2->id, 'is_current' => 1]);
    }

    public function test_get_current_academic_year(): void
    {
        AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);

        $response = $this->getJson('/api/v1/academic-years/current', $this->authHeaders());

        $response->assertStatus(200);
        $this->assertEquals('2026', $response->json('year_name'));
        $this->assertTrue($response->json('is_current'));
    }

    public function test_auth_required_for_academic_year_endpoints(): void
    {
        $this->getJson('/api/v1/academic-years')->assertStatus(401);
        $this->getJson('/api/v1/academic-years/1')->assertStatus(401);
        $this->postJson('/api/v1/academic-years', [])->assertStatus(401);
        $this->putJson('/api/v1/academic-years/1', [])->assertStatus(401);
        $this->deleteJson('/api/v1/academic-years/1')->assertStatus(401);
    }
}
