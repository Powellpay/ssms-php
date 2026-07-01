# SSMS API — School Management System

**Aligned to Uganda's Competency-Based Curriculum (NCDC Lower Secondary)**  
Built with Laravel 12 + Sanctum + Spatie Permission

## Quick Start

```bash
# Configure environment
cp .env.example .env
php artisan key:generate

# Database (SQLite for dev)
touch database/database.sqlite

# Migrate + seed
php artisan migrate --seed

# Start dev server
php artisan serve
```

**Default login:** `admin` / `ChangeMe123!`

## Test Suite

```bash
php artisan test
# 168 tests, 304 assertions, ~15s
```

---

## Table of Contents

1. [Architecture](#1-architecture)
2. [Modules](#2-modules)
3. [Entity Reference](#3-entity-reference)
4. [API Endpoints](#4-api-endpoints)
5. [Architecture Decisions](#5-architecture-decisions)
6. [Testing Strategy](#6-testing-strategy)
7. [File Structure](#7-file-structure)

---

## 1. Architecture

### Domain-Driven SOLID Pattern

Code is organized into **domain modules** under `app/Domain/{Module}/`. Each module is self-contained with its own Models, Controllers, Services, Repositories, Requests, Resources, routes, and Provider.

Every entity follows a strict 12-file structure within its domain module:

```
Migration    → database/migrations/
Model        → app/Domain/{Module}/Models/
Repository   → app/Domain/{Module}/Repositories/Contracts/ (Interface)
                app/Domain/{Module}/Repositories/Eloquent/ (Implementation)
Service      → app/Domain/{Module}/Services/Contracts/ (Interface)
                app/Domain/{Module}/Services/ (Implementation)
Request      → app/Domain/{Module}/Requests/
Resource     → app/Domain/{Module}/Resources/
Collection   → app/Domain/{Module}/Resources/
Controller   → app/Domain/{Module}/Controllers/
Routes       → app/Domain/{Module}/routes/{entity}_index.php
Provider     → app/Domain/{Module}/Providers/ + registered in bootstrap/providers.php
```

### Dependency Flow

```
Controller → ServiceInterface → Service (business logic)
                                  ↓
                             RepositoryInterface → Repository (data access)
                                                    ↓
                                                 Eloquent Model
```

### Provider Registration

All entity bindings registered in `bootstrap/providers.php` only (auto-generated via `scripts/gen-providers.php`).  
`AppServiceProvider.php` is never modified for entity bindings.

---

## 2. Modules

| # | Module | Tables | Description |
|---|--------|--------|-------------|
| 1 | **Users & Roles** | `roles`, `users` | Authentication, role-based access (8 roles) |
| 2 | **Academic Structure** | `academic_years`, `terms`, `class_levels`, `streams` | School calendar, class hierarchy (S1-S4) |
| 3 | **Staff** | `staff` | Teacher & non-teaching staff records |
| 4 | **Students & Guardians** | `students`, `guardians`, `student_guardians`, `enrollments` | Admissions, bio-data, guardian contacts, yearly promotion |
| 5 | **Curriculum** | `subjects`, `class_subjects`, `subject_teachers`, `curriculum_themes`, `learning_outcomes`, `generic_skills` | NCDC themes/outcomes, subject allocation |
| 6 | **Assessment & Grading** | `assessment_types`, `grading_scale`, `skill_rating_scale`, `assessment_records`, `generic_skill_ratings`, `subject_term_results` | CA & exam scores, A-E grading, generic skill ratings |
| 7 | **Report Cards** | `report_cards` | Term report compilation |
| 8 | **Attendance** | `attendance` | Daily register, term summaries |
| 9 | **Timetable** | `timetable` | Weekly period allocation |
| 10 | **Finance** | `fee_structures`, `invoices`, `payments` | Fee billing, payment tracking |
| 11 | **Discipline** | `discipline_records` | Conduct/incident logs |
| 12 | **Library** | `library_books`, `book_loans` | Catalogue, borrowing |
| 13 | **Communication** | `announcements` | School-wide notices |

---

## 3. Entity Reference

### Module 1: Users & Roles

| Entity | Table | Fields |
|--------|-------|--------|
| Role | `roles` | id, role_name (unique), description |
| User | `users` | id, role_id (FK), username (unique), name, email (unique), password, phone, status, last_login |

- **8 seeded roles:** Administrator, Head Teacher, Director of Studies, Teacher, Bursar, Librarian, Parent, Student
- Passwords hashed with `Hash::make()` (bcrypt)
- API authentication via Sanctum tokens

### Module 2: Academic Structure

| Entity | Table | Fields |
|--------|-------|--------|
| AcademicYear | `academic_years` | year_name (unique), start_date, end_date, is_current |
| Term | `terms` | academic_year_id (FK), term_name, start_date, end_date, is_current |
| ClassLevel | `class_levels` | level_name (unique), numeric_level |
| Stream | `streams` | class_level_id (FK), academic_year_id (FK), stream_name, class_teacher_id (FK) |

- Only one academic year can be `is_current = true` at a time
- Only one term per academic year can be `is_current = true`
- unique constraint: (class_level_id, academic_year_id, stream_name)

### Module 3: Staff

| Entity | Table | Fields |
|--------|-------|--------|
| Staff | `staff` | user_id (FK), staff_no (unique), first_name, last_name, gender, dob, phone, email, address, designation, date_joined, status |

### Module 4: Students & Guardians

| Entity | Table | Fields |
|--------|-------|--------|
| Student | `students` | user_id (FK), admission_no (unique), lin (UNEB), first_name, last_name, gender, dob, admission_date, status |
| Guardian | `guardians` | first_name, last_name, relationship, phone, email |
| StudentGuardian | `student_guardians` | student_id (FK), guardian_id (FK), is_primary_contact |
| Enrollment | `enrollments` | student_id (FK), stream_id (FK), academic_year_id (FK), enrollment_date, status |

- unique constraint: (student_id, academic_year_id) — one enrollment per year

### Module 5: Curriculum

| Entity | Table | Fields |
|--------|-------|--------|
| Subject | `subjects` | subject_code (unique), subject_name, category (Core/Elective/Pre-Vocational) |
| ClassSubject | `class_subjects` | class_level_id (FK), subject_id (FK), is_compulsory |
| SubjectTeacher | `subject_teachers` | subject_id (FK), stream_id (FK), staff_id (FK), academic_year_id (FK) |
| CurriculumTheme | `curriculum_themes` | subject_id (FK), class_level_id (FK), theme_code, theme_name |
| LearningOutcome | `learning_outcomes` | theme_id (FK), outcome_code, description |
| GenericSkill | `generic_skills` | skill_name (unique), description |

### Module 6: Assessment

| Entity | Table | Fields |
|--------|-------|--------|
| AssessmentType | `assessment_types` | type_name, category (Formative/Summative), weight_percentage |
| GradingScale | `grading_scale` | grade (A-E unique), descriptor, min_score, max_score |
| SkillRatingScale | `skill_rating_scale` | rating_code (unique), rating_label, rating_value |
| AssessmentRecord | `assessment_records` | student_id (FK), subject_id (FK), theme_id (FK), learning_outcome_id (FK), assessment_type_id (FK), term_id (FK), score, max_score, date_recorded |
| GenericSkillRating | `generic_skill_ratings` | student_id (FK), term_id (FK), generic_skill_id (FK), rating_id (FK) |
| SubjectTermResult | `subject_term_results` | student_id (FK), subject_id (FK), term_id (FK), ca_score, eot_score, final_score, final_grade |

- Grading scale A-E: A(80-100), B(70-79.99), C(55-69.99), D(40-54.99), E(0-39.99)
- final_score = ca_score + eot_score
- Skill rating: Beginning(1), Developing(2), Proficient(3), Mastery(4)

### Module 7: Report Cards

| Entity | Table | Fields |
|--------|-------|--------|
| ReportCard | `report_cards` | student_id (FK), term_id (FK), stream_id (FK), days_present, days_absent, class_teacher_comment, head_teacher_comment, next_term_begins, date_issued |

### Module 8: Attendance

| Entity | Table | Fields |
|--------|-------|--------|
| Attendance | `attendance` | student_id (FK), term_id (FK), attendance_date, status (Present/Absent/Late/Excused), recorded_by (FK) |

- unique constraint: (student_id, attendance_date)

### Module 9: Timetable

| Entity | Table | Fields |
|--------|-------|--------|
| Timetable | `timetable` | stream_id (FK), subject_id (FK), staff_id (FK), academic_year_id (FK), day_of_week, period_no, start_time, end_time |

### Module 10: Finance

| Entity | Table | Fields |
|--------|-------|--------|
| FeeStructure | `fee_structures` | class_level_id (FK), term_id (FK), fee_category, amount |
| Invoice | `invoices` | student_id (FK), term_id (FK), total_amount, amount_paid, balance (virtual), issue_date, status |
| Payment | `payments` | invoice_id (FK), student_id (FK), amount, payment_method, reference_no, payment_date, received_by (FK) |

- balance is a virtual/calculated column: total_amount - amount_paid

### Module 11: Discipline

| Entity | Table | Fields |
|--------|-------|--------|
| DisciplineRecord | `discipline_records` | student_id (FK), term_id (FK), incident_date, description, action_taken, recorded_by (FK) |

### Module 12: Library

| Entity | Table | Fields |
|--------|-------|--------|
| LibraryBook | `library_books` | title, author, isbn, category, total_copies, available_copies |
| BookLoan | `book_loans` | book_id (FK), student_id (FK), staff_id (FK), borrow_date, due_date, return_date, status |

### Module 13: Communication

| Entity | Table | Fields |
|--------|-------|--------|
| Announcement | `announcements` | title, message, target_role, created_by (FK) |

---

## 4. API Endpoints

**Base URL:** `/api/`  
**Auth:** Bearer token via Sanctum (`auth:sanctum` middleware)  
**Default auth endpoints:** `POST /api/auth/register`, `POST /api/auth/login`, `POST /api/auth/logout`, `GET /api/auth/me`

All resource endpoints follow RESTful conventions:

| Method | Endpoint | Controller Action |
|--------|----------|-------------------|
| GET | `/[resource]` | index (list) |
| POST | `/[resource]` | store (create) |
| GET | `/[resource]/{id}` | show |
| PUT/PATCH | `/[resource]/{id}` | update |
| DELETE | `/[resource]/{id}` | destroy |

### Resources (174 routes)

| Resource | Route Prefix |
|----------|-------------|
| Auth | `auth/` (register, login, logout, me, forgot-password, reset-password) |
| Roles | `roles/` |
| Users | `users/` |
| Academic Years | `academic-years/` (extra: current, set-current) |
| Terms | `terms/` |
| Class Levels | `class-levels/` |
| Streams | `streams/` |
| Staff | `staff/` |
| Students | `students/` (extra: admission/{no}, status/{status}, {id}/enrollment) |
| Guardians | `guardians/` |
| Enrollments | `enrollments/` |
| Subjects | `subjects/` |
| Class Subjects | `class-subjects/` |
| Subject Teachers | `subject-teachers/` |
| Curriculum Themes | `curriculum-themes/` |
| Learning Outcomes | `learning-outcomes/` |
| Generic Skills | `generic-skills/` |
| Assessment Types | `assessment-types/` |
| Grading Scale | `grading-scale/` |
| Skill Rating Scale | `skill-rating-scale/` |
| Assessment Records | `assessment-records/` |
| Generic Skill Ratings | `generic-skill-ratings/` |
| Subject Term Results | `subject-term-results/` |
| Report Cards | `report-cards/` |
| Attendance | `attendance/` |
| Timetable | `timetable/` |
| Fee Structures | `fee-structures/` |
| Invoices | `invoices/` |
| Payments | `payments/` |
| Discipline Records | `discipline-records/` |
| Library Books | `library-books/` |
| Book Loans | `book-loans/` |
| Announcements | `announcements/` |

### Seed Data

| Data | Count |
|------|-------|
| Roles | 8 |
| Admin user | 1 (admin / ChangeMe123!) |
| Academic years | 1 (2026) |
| Terms | 3 (Term 1-3) |
| Class levels | 4 (S1-S4) |
| Staff | 3 |
| Streams | 5 |
| Subjects | 13 |
| Students | 2 |
| Subject term results | 11 (Faith Achieng, Term 1) |
| Report card | 1 (Faith Achieng) |

---

## 5. Architecture Decisions

### ADR-001: Repository + Service Pattern with SOLID
**Date:** 2026-07-01

**Context:** Need a consistent architecture that enforces separation of concerns, testability, and maintainability across all entities.

**Decision:** Every entity follows a strict 12-file pattern (Migration, Model, Repository Interface + Implementation, Service Interface + Implementation, Request, Resource, Collection, Controller, Routes, Provider). Bindings registered in `bootstrap/providers.php` via dedicated service providers.

### ADR-002: API Token Authentication (Sanctum)
**Date:** 2026-07-01

**Context:** The frontend will be a separate React SPA requiring stateless API authentication.

**Decision:** Use Laravel Sanctum for token-based API auth. Users authenticate via `POST /api/auth/login` and receive a plain-text token. All subsequent requests include `Authorization: Bearer {token}`. Tokens are deletable via logout.

### ADR-003: SQLite for Development, MySQL for Production
**Date:** 2026-07-01

**Context:** The reference schema targets MySQL, but local development benefits from SQLite's zero-config setup.

**Decision:** Default to SQLite (`:memory:` for tests, file-based for dev). MySQL configuration available in `.env` for production.

### ADR-004: Form Request Validation
**Date:** 2026-07-01

**Context:** Consistent validation across all API endpoints.

**Decision:** Every entity has a dedicated Form Request class with validation rules covering type constraints, uniqueness, foreign key existence, and status enums.

### ADR-005: Testing Strategy
**Date:** 2026-07-01

**Context:** Need fast, reliable tests that can run during development without external dependencies.

**Decision:** 
- Unit tests (using Mockery) for service-layer business logic (password hashing, grade computation, authentication)
- Feature tests (using RefreshDatabase with SQLite :memory:) for full HTTP request/response cycle
- All tests run via `php artisan test` — no external DB or services required

---

## 6. Testing Strategy

### Test Types

| Type | Location | Approach |
|------|----------|----------|
| Unit | `tests/Unit/Services/` | Mock repositories with Mockery, test service logic in isolation |
| Feature | `tests/Feature/Api/` | RefreshDatabase + SQLite :memory:, full HTTP stack via `$this->postJson()` |

### Coverage

| Module | Unit Tests | Feature Tests | Total |
|--------|-----------|---------------|-------|
| Users & Roles | UserServiceTest (12) | AuthTest (11), RoleTest (7) | 30 |
| Academic | AcademicYearServiceTest (3) | AcademicYearTest (8), ClassLevelTest (6), TermTest (6) | 23 |
| Staff | — | StaffTest (8) | 8 |
| Students | — | StudentTest (11), GuardianTest (5), EnrollmentTest (5) | 21 |
| Curriculum | — | SubjectTest (7), SubjectTeacherTest (3), CurriculumThemeTest (3), LearningOutcomeTest (2), GenericSkillTest (3) | 18 |
| Assessment | GradingScaleServiceTest (6), SubjectTermResultServiceTest (3) | AssessmentTypeTest (6), GradingScaleTest (5), AssessmentRecordTest (5), GenericSkillRatingTest (3), SkillRatingScaleTest (5), SubjectTermResultTest (4) | 37 |
| Reports | — | ReportCardTest (4) | 4 |
| Attendance | — | AttendanceTest (7) | 7 |
| Timetable | — | TimetableTest (5) | 5 |
| Finance | — | FinanceTest (6) | 6 |
| Discipline | — | DisciplineRecordTest (4) | 4 |
| Library | — | LibraryTest (6) | 6 |
| Communication | — | AnnouncementTest (3) | 3 |
| **Total** | **24** | **144** | **168** |

### Running Tests

```bash
# All tests
php artisan test

# Specific test file
php artisan test --filter=AuthTest

# Specific test method
php artisan test --filter="AuthTest::test_user_can_login"

# With coverage (requires Xdebug/PCOV)
php artisan test --coverage
```

---

## 7. File Structure

```
sms/
├── frontend/                    # (empty — ready for React/TS)
├── backend/
│   ├── AGENTS.md                # AI agent orchestration (gitignored)
│   ├── scripts/
│   │   ├── vera-fast.php        # php -l on changed PHP files
│   │   ├── vera-extended.php    # Fast + migrate pretend + filtered tests
│   │   ├── refactor-domains.php # Migrate flat structure → domains
│   │   ├── update-namespaces.php# Update namespace references
│   │   └── gen-providers.php    # Auto-generate bootstrap/providers.php
│   ├── docs/
│   │   ├── entities.md          # Entity documentation (append-only)
│   │   ├── decisions.md         # Architecture Decision Records
│   │   ├── tests.md             # Test results documentation
│   │   └── modular-proposal.md  # Domain folder proposal
│   ├── app/
│   │   ├── Domain/              # 13 domain modules
│   │   │   ├── Auth/            # Roles, Users
│   │   │   ├── Academic/        # Years, Terms, Classes, Streams
│   │   │   ├── Staff/           # Staff
│   │   │   ├── Students/        # Students, Guardians, Enrollments
│   │   │   ├── Curriculum/      # Subjects, Themes, Outcomes, Skills
│   │   │   ├── Assessment/      # Types, Grading, Records, Results
│   │   │   ├── Reports/         # Report Cards
│   │   │   ├── Attendance/      # Attendance
│   │   │   ├── Timetable/       # Timetable
│   │   │   ├── Finance/         # Fees, Invoices, Payments
│   │   │   ├── Discipline/      # Discipline Records
│   │   │   ├── Library/         # Books, Loans
│   │   │   └── Announcements/   # Announcements
│   │   │   └── Shared/          # Shared base classes
│   │   ├── Console/Commands/    # make:module command
│   │   └── Providers/           # AppServiceProvider only
│   ├── routes/
│   │   └── api.php              # Auto-discovers domain routes via glob
│   ├── bootstrap/providers.php  # Auto-generated by gen-providers.php
│   ├── database/
│   │   ├── migrations/          # 10 migration files (31 tables)
│   │   └── seeders/             # DatabaseSeeder with sample data
│   └── tests/
│       ├── Unit/Services/       # 4 unit test files (24 tests)
│       └── Feature/Api/         # 20 feature test files (144 tests)
```

---

## Module Scaffolding

Create a new domain module with all SOLID boilerplate:

```bash
php artisan make:module ModuleName
```

This generates `app/Domain/{ModuleName}/` with:
- Model, Controller, Service (+Interface), Repository (+Interface)
- Form Request, Resource, Collection
- Service Provider, route file

After scaffolding:
1. `php scripts/gen-providers.php` — registers the provider
2. `composer dump-autoload`
3. Add your migration and fill in the business logic

---

## Scripts Reference

| Script | Purpose |
|--------|---------|
| `scripts/vera-fast.php` | php -l on changed PHP files |
| `scripts/vera-extended.php` | Fast + migrate pretend + filtered tests |
| `scripts/refactor-domains.php` | Flat→domain migration |
| `scripts/update-namespaces.php` | Bulk namespace update |
| `scripts/gen-providers.php` | Auto-generate bootstrap/providers.php |
| `scripts/fix-namespaces.php` | Fix namespace double-prefix issues |

---

## Dependencies

| Package | Version | Purpose |
|---------|---------|---------|
| `laravel/framework` | ^12.0 | Core framework |
| `laravel/sanctum` | ^4.0 | API token authentication |
| `spatie/laravel-permission` | ^6.24 | Role-based access control |
| `phpunit/phpunit` | ^11.5 (dev) | Testing |

---

## Uganda CBC Context

This system is designed for Uganda's **Competency-Based Curriculum (CBC)** rolled out by NCDC for Lower Secondary (S1-S4):

- **Subjects** organized into **themes/topics** with specific **learning outcomes (competencies)**
- **Assessment split:** Formative (CA, 20%) + Summative (End of Term/Cycle, 80%)
- **Letter grades A-E:** A(Exceptional), B(Outstanding), C(Satisfactory), D(Basic), E(Elementary)
- **Generic skills:** Communication, Cooperation & Self-Directed Learning, Critical Thinking & Problem Solving, Creativity & Innovation
- **No single mandated report card:** schools configure grading bands and skill ratings per their policy
