# SSMS API — School Management System

**Aligned to Uganda's Competency-Based Curriculum (NCDC Lower Secondary)**  
Laravel 12 + Sanctum + Spatie Permission  
Domain-driven folder structure

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

## 13 Domain Modules | 31 Tables | 174 API Routes

```
app/Domain/
  Auth/             Roles, Users, AuthController
  Academic/         AcademicYear, Term, ClassLevel, Stream
  Staff/            Staff
  Students/         Student, Guardian, StudentGuardian, Enrollment
  Curriculum/       Subject, ClassSubject, SubjectTeacher, themes, outcomes, skills
  Assessment/       AssessmentType, GradingScale, SkillRatingScale, records, results
  Reports/          ReportCard
  Attendance/       Attendance
  Timetable/        Timetable
  Finance/          FeeStructure, Invoice, Payment
  Discipline/       DisciplineRecord
  Library/          LibraryBook, BookLoan
  Announcements/    Announcement
```

## Architecture

SOLID Repository + Service pattern per domain module. Each module owns its Models, Controllers, Services, Repositories, Requests, Resources, routes, and Provider.

```
Controller → ServiceInterface → Service → RepositoryInterface → Repository → Model
```

All bindings in `bootstrap/providers.php` (auto-generated via `scripts/gen-providers.php`).

## Scaffold a New Module

```bash
php artisan make:module ModuleName
# Creates app/Domain/{ModuleName}/ with full 12-file SOLID scaffold
```

Full documentation: `DOCUMENTATION.md`
