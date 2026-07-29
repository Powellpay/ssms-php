<?php

namespace Tests\Feature\Api;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Finance\Models\FeeStructure;
use App\Domain\Finance\Models\Invoice;
use App\Domain\Finance\Models\Payment;
use App\Domain\Auth\Models\Role;
use App\Domain\Students\Models\Student;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private ClassLevel $classLevel;
    private Term $term;
    private Student $student;

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

        $academicYear = AcademicYear::create([
            'year_name' => '2026', 'start_date' => '2026-02-03', 'end_date' => '2026-12-04', 'is_current' => true,
        ]);
        $this->term = Term::create([
            'academic_year_id' => $academicYear->id, 'term_name' => 'Term 1',
            'start_date' => '2026-02-03', 'end_date' => '2026-05-08', 'is_current' => true,
        ]);
        $this->classLevel = ClassLevel::create(['level_name' => 'Senior 1', 'numeric_level' => 1]);
        $this->student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_fee_structure(): void
    {
        $response = $this->postJson('/api/v1/fee-structures', [
            'class_level_id' => $this->classLevel->id,
            'term_id' => $this->term->id,
            'fee_category' => 'Tuition',
            'amount' => 500000,
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_fee_structures(): void
    {
        FeeStructure::create([
            'class_level_id' => $this->classLevel->id, 'term_id' => $this->term->id,
            'fee_category' => 'Tuition', 'amount' => 500000,
        ]);

        $response = $this->getJson('/api/v1/fee-structures', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_create_invoice(): void
    {
        $response = $this->postJson('/api/v1/invoices', [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'total_amount' => 500000,
            'issue_date' => now()->format('Y-m-d'),
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_invoices(): void
    {
        Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson('/api/v1/invoices', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_record_payment(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->postJson('/api/v1/payments', [
            'invoice_id' => $invoice->id,
            'student_id' => $this->student->id,
            'amount' => 250000,
            'payment_method' => 'Cash',
            'payment_date' => now()->format('Y-m-d'),
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_payments(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        Payment::create([
            'invoice_id' => $invoice->id, 'student_id' => $this->student->id,
            'amount' => 250000, 'payment_method' => 'Cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson('/api/v1/payments', $this->authHeaders());

        $response->assertStatus(200);
    }
}
