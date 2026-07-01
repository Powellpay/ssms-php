<?php

namespace Tests\Feature\Api;

use App\Domain\Library\Models\BookLoan;
use App\Domain\Library\Models\LibraryBook;
use App\Domain\Auth\Models\Role;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use App\Domain\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    private string $token;
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

        $this->student = Student::create([
            'admission_no' => 'S26-0001', 'first_name' => 'Faith', 'last_name' => 'Achieng',
            'gender' => 'Female', 'admission_date' => '2026-02-03',
        ]);
        $this->staff = Staff::create([
            'staff_no' => 'STF-001', 'first_name' => 'John', 'last_name' => 'Doe',
            'gender' => 'Male', 'email' => 'staff@test.com', 'designation' => 'Librarian', 'status' => 'active',
        ]);
    }

    private function authHeaders(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_create_book(): void
    {
        $response = $this->postJson('/api/library-books', [
            'title' => 'Maths Textbook',
            'author' => 'John',
            'isbn' => '12345',
            'total_copies' => 5,
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_books(): void
    {
        LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345', 'total_copies' => 5,
        ]);

        $response = $this->getJson('/api/library-books', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_borrow_book(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345', 'total_copies' => 5,
        ]);

        $response = $this->postJson('/api/book-loans', [
            'book_id' => $book->id,
            'student_id' => $this->student->id,
            'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ], $this->authHeaders());

        $response->assertStatus(201);
    }

    public function test_can_list_loans(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345', 'total_copies' => 5,
        ]);

        BookLoan::create([
            'book_id' => $book->id, 'student_id' => $this->student->id, 'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'), 'due_date' => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response = $this->getJson('/api/book-loans', $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_can_update_book(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345', 'total_copies' => 5, 'available_copies' => 5,
        ]);

        $response = $this->putJson("/api/library-books/{$book->id}", [
            'title' => 'Maths Textbook',
            'author' => 'John',
            'isbn' => '12345',
            'total_copies' => 5,
            'available_copies' => 3,
        ], $this->authHeaders());

        $response->assertStatus(200);
    }
}
