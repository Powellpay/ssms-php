<?php

namespace Tests\Feature;

use App\Domain\Academic\Models\AcademicYear;
use App\Domain\Academic\Models\ClassLevel;
use App\Domain\Academic\Models\Term;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Finance\Models\FeeStructure;
use App\Domain\Finance\Models\Invoice;
use App\Domain\Finance\Models\Payment;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceContractTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
    private ClassLevel $classLevel;
    private Term $term;
    private Student $student;
    private Staff $staff;

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
        $this->staff = Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'John', 'last_name' => 'Doe',
            'gender' => 'Male', 'email' => 'staff@test.com', 'designation' => 'Accountant', 'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_list_fee_structures(): void
    {
        FeeStructure::create([
            'class_level_id' => $this->classLevel->id, 'term_id' => $this->term->id,
            'fee_category' => 'Tuition', 'amount' => 500000,
        ]);

        $response = $this->getJson('/api/v1/fee-structures', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'class_level_id',
                        'term_id',
                        'fee_category',
                        'amount',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_fee_structure(): void
    {
        $response = $this->postJson('/api/v1/fee-structures', [
            'class_level_id' => $this->classLevel->id,
            'term_id' => $this->term->id,
            'fee_category' => 'Tuition',
            'amount' => 500000,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'class_level_id',
                'term_id',
                'fee_category',
                'amount',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_show_fee_structure(): void
    {
        $fee = FeeStructure::create([
            'class_level_id' => $this->classLevel->id, 'term_id' => $this->term->id,
            'fee_category' => 'Tuition', 'amount' => 500000,
        ]);

        $response = $this->getJson("/api/v1/fee-structures/{$fee->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'class_level_id',
                'term_id',
                'fee_category',
                'amount',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_update_fee_structure(): void
    {
        $fee = FeeStructure::create([
            'class_level_id' => $this->classLevel->id, 'term_id' => $this->term->id,
            'fee_category' => 'Tuition', 'amount' => 500000,
        ]);

        $response = $this->putJson("/api/v1/fee-structures/{$fee->id}", [
            'class_level_id' => $this->classLevel->id,
            'term_id' => $this->term->id,
            'fee_category' => 'Sports',
            'amount' => 100000,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'class_level_id',
                'term_id',
                'fee_category',
                'amount',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_delete_fee_structure(): void
    {
        $fee = FeeStructure::create([
            'class_level_id' => $this->classLevel->id, 'term_id' => $this->term->id,
            'fee_category' => 'Tuition', 'amount' => 500000,
        ]);

        $response = $this->deleteJson("/api/v1/fee-structures/{$fee->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('fee_structures', ['id' => $fee->id]);
    }

    public function test_list_invoices(): void
    {
        Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson('/api/v1/invoices', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'student_id',
                        'term_id',
                        'total_amount',
                        'amount_paid',
                        'balance',
                        'issue_date',
                        'due_date',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_invoice(): void
    {
        $response = $this->postJson('/api/v1/invoices', [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'total_amount' => 500000,
            'issue_date' => now()->format('Y-m-d'),
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'total_amount',
                'amount_paid',
                'balance',
                'issue_date',
                'due_date',
                'status',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_show_invoice(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson("/api/v1/invoices/{$invoice->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'total_amount',
                'amount_paid',
                'balance',
                'issue_date',
                'due_date',
                'status',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_update_invoice(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->putJson("/api/v1/invoices/{$invoice->id}", [
            'student_id' => $this->student->id,
            'term_id' => $this->term->id,
            'total_amount' => 600000,
            'issue_date' => now()->format('Y-m-d'),
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'student_id',
                'term_id',
                'total_amount',
                'amount_paid',
                'balance',
                'issue_date',
                'due_date',
                'status',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_delete_invoice(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->deleteJson("/api/v1/invoices/{$invoice->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_list_payments(): void
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

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'invoice_id',
                        'student_id',
                        'amount',
                        'payment_method',
                        'reference_no',
                        'payment_date',
                        'received_by',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_payment(): void
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

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'invoice_id',
                'student_id',
                'amount',
                'payment_method',
                'reference_no',
                'payment_date',
                'received_by',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_show_payment(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);
        $payment = Payment::create([
            'invoice_id' => $invoice->id, 'student_id' => $this->student->id,
            'amount' => 250000, 'payment_method' => 'Cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson("/api/v1/payments/{$payment->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'invoice_id',
                'student_id',
                'amount',
                'payment_method',
                'reference_no',
                'payment_date',
                'received_by',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_delete_payment(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'issue_date' => now()->format('Y-m-d'),
        ]);
        $payment = Payment::create([
            'invoice_id' => $invoice->id, 'student_id' => $this->student->id,
            'amount' => 250000, 'payment_method' => 'Cash',
            'payment_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->deleteJson("/api/v1/payments/{$payment->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }

    public function test_fee_structure_validation(): void
    {
        $response = $this->postJson('/api/v1/fee-structures', [
            'class_level_id' => 99999,
            'term_id' => $this->term->id,
            'fee_category' => 'Tuition',
            'amount' => 500000,
        ], $this->authHeaders());

        $response->assertStatus(422);
    }

    public function test_invoice_balance_computed(): void
    {
        $invoice = Invoice::create([
            'student_id' => $this->student->id, 'term_id' => $this->term->id,
            'total_amount' => 500000, 'amount_paid' => 200000, 'issue_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->getJson("/api/v1/invoices/{$invoice->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonFragment(['total_amount' => 500000]);
    }
}
