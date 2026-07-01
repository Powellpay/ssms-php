# SSMS API — School Management System

**Aligned to Uganda's Competency-Based Curriculum (NCDC Lower Secondary)**  
Laravel 12 + Sanctum + Spatie Permission

## Quick Start

```bash
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate --seed
php artisan serve
```

**Login:** `admin` / `ChangeMe123!` at `POST /api/auth/login`

## Test Suite: 168 passing tests

```bash
php artisan test
```

## 13 Modules | 31 Tables | 174 API Routes

| Module | Key Entities |
|--------|-------------|
| Users & Roles | `roles`, `users` |
| Academic Structure | `academic_years`, `terms`, `class_levels`, `streams` |
| Staff | `staff` |
| Students & Guardians | `students`, `guardians`, `student_guardians`, `enrollments` |
| Curriculum | `subjects`, `class_subjects`, `subject_teachers`, `curriculum_themes`, `learning_outcomes`, `generic_skills` |
| Assessment & Grading | `assessment_types`, `grading_scale`, `skill_rating_scale`, `assessment_records`, `generic_skill_ratings`, `subject_term_results` |
| Report Cards | `report_cards` |
| Attendance | `attendance` |
| Timetable | `timetable` |
| Finance | `fee_structures`, `invoices`, `payments` |
| Discipline | `discipline_records` |
| Library | `library_books`, `book_loans` |
| Announcements | `announcements` |

## Architecture

SOLID Repository + Service pattern: Controller → Service Interface → Service (business logic) → Repository Interface → Repository (Eloquent) → Model. All bindings in `bootstrap/providers.php`.

Full documentation: `DOCUMENTATION.md`
