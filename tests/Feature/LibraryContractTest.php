<?php

namespace Tests\Feature;

use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Models\User;
use App\Domain\Library\Models\BookLoan;
use App\Domain\Library\Models\LibraryBook;
use App\Domain\Staff\Models\Staff;
use App\Domain\Students\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryContractTest extends TestCase
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

    public function test_list_library_books_structure(): void
    {
        LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);

        $response = $this->getJson('/api/v1/library-books', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'isbn',
                        'category',
                        'total_copies',
                        'available_copies',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_library_book(): void
    {
        $response = $this->postJson('/api/v1/library-books', [
            'title' => 'Maths Textbook',
            'author' => 'John',
            'isbn' => '12345',
            'total_copies' => 5,
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'author',
                'isbn',
                'category',
                'total_copies',
                'available_copies',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_show_library_book(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);

        $response = $this->getJson("/api/v1/library-books/{$book->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'author',
                'isbn',
                'category',
                'total_copies',
                'available_copies',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_update_library_book(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);

        $response = $this->putJson("/api/v1/library-books/{$book->id}", [
            'title' => 'Advanced Maths',
            'author' => 'John',
            'isbn' => '12345',
            'total_copies' => 10,
            'available_copies' => 8,
        ], $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'author',
                'isbn',
                'category',
                'total_copies',
                'available_copies',
                'created_at',
                'updated_at',
            ])
            ->assertJsonFragment(['title' => 'Advanced Maths']);
    }

    public function test_delete_library_book(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);

        $response = $this->deleteJson("/api/v1/library-books/{$book->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('library_books', ['id' => $book->id]);
    }

    public function test_list_book_loans_structure(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);
        BookLoan::create([
            'book_id' => $book->id, 'student_id' => $this->student->id,
            'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'borrowed',
        ]);

        $response = $this->getJson('/api/v1/book-loans', $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'book_id',
                        'student_id',
                        'staff_id',
                        'borrow_date',
                        'due_date',
                        'return_date',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_create_book_loan_decreases_available_copies(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);

        $response = $this->postJson('/api/v1/book-loans', [
            'book_id' => $book->id,
            'student_id' => $this->student->id,
            'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
        ], $this->authHeaders());

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'book_id',
                'student_id',
                'staff_id',
                'borrow_date',
                'due_date',
                'return_date',
                'status',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('library_books', [
            'id' => $book->id,
            'available_copies' => 4,
        ]);
    }

    public function test_show_book_loan(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);
        $loan = BookLoan::create([
            'book_id' => $book->id, 'student_id' => $this->student->id,
            'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'borrowed',
        ]);

        $response = $this->getJson("/api/v1/book-loans/{$loan->id}", $this->authHeaders());

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'book_id',
                'student_id',
                'staff_id',
                'borrow_date',
                'due_date',
                'return_date',
                'status',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_return_book_loan(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 3,
        ]);
        $loan = BookLoan::create([
            'book_id' => $book->id, 'student_id' => $this->student->id,
            'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'borrowed',
        ]);

        $response = $this->putJson("/api/v1/book-loans/{$loan->id}", [
            'book_id' => $book->id,
            'student_id' => $this->student->id,
            'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'return_date' => now()->format('Y-m-d'),
            'status' => 'returned',
        ], $this->authHeaders());

        $response->assertStatus(200);
    }

    public function test_delete_book_loan(): void
    {
        $book = LibraryBook::create([
            'title' => 'Maths Textbook', 'author' => 'John', 'isbn' => '12345',
            'total_copies' => 5, 'available_copies' => 5,
        ]);
        $loan = BookLoan::create([
            'book_id' => $book->id, 'student_id' => $this->student->id,
            'staff_id' => $this->staff->id,
            'borrow_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'borrowed',
        ]);

        $response = $this->deleteJson("/api/v1/book-loans/{$loan->id}", [], $this->authHeaders());

        $response->assertStatus(200);
        $this->assertDatabaseMissing('book_loans', ['id' => $loan->id]);
    }

    public function test_library_book_validation(): void
    {
        $response = $this->postJson('/api/v1/library-books', [
            'author' => 'John',
        ], $this->authHeaders());

        $response->assertStatus(422);
    }
}
