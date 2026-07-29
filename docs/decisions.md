# Architecture Decision Records

## ADR-001: Repository + Service Pattern with SOLID
**Date:** 2026-07-01

### Context
Need a consistent architecture that enforces separation of concerns, testability, and maintainability across all entities.

### Decision
Every entity follows a strict 12-file pattern:
- Migration, Model, Repository Interface + Implementation, Service Interface + Implementation, Request, Resource, Collection, Controller, Routes, Provider

Bindings registered in `bootstrap/providers.php` via dedicated service providers.

### Consequences
- Consistent structure across all entities
- Easy to add/modify entities without affecting others
- More files per entity, but clear responsibilities

---

## ADR-002: API Token Authentication (Sanctum)
**Date:** 2026-07-01

### Context
The frontend will be a separate React SPA requiring stateless API authentication.

### Decision
Use Laravel Sanctum for token-based API auth. Users authenticate via `POST /api/auth/login` and receive a plain-text token. All subsequent requests include `Authorization: Bearer {token}`. Tokens are deleted on logout.

### Consequences
- Stateless authentication suitable for SPA/API consumption
- No session files needed on the backend
- Token revocation via simple DB delete

---

## ADR-003: SQLite for Development, MySQL for Production
**Date:** 2026-07-01

### Context
The reference schema targets MySQL, but local development benefits from SQLite's zero-config setup.

### Decision
Default to SQLite (`:memory:` for tests, file-based for dev). MySQL configuration available in `.env` for production.

### Consequences
- Zero-config local setup
- Tests run in-memory (fast, no cleanup)
- Schema must avoid MySQL-specific features (no ENUM, no FULLTEXT, etc.)

---

## ADR-004: Form Request Validation
**Date:** 2026-07-01

### Context
Consistent validation across all API endpoints is essential for data integrity.

### Decision
Every entity has a dedicated Form Request class with validation rules covering type constraints, uniqueness, foreign key existence, and status enums. Custom unique rules handle composite constraints (e.g., student+academic_year for enrollments).

### Consequences
- Validation logic lives in one place per entity
- Controllers stay clean
- Reuse rules for both create and update (ignore current ID)

---

## ADR-005: Testing Strategy
**Date:** 2026-07-01

### Context
Need fast, reliable tests that can run during development without external dependencies.

### Decision
- Unit tests (using Mockery) for service-layer business logic: password hashing, grade computation, authentication, set-current logic
- Feature tests (using RefreshDatabase with SQLite :memory:) for full HTTP request/response cycle
- All tests run via `php artisan test` — no external DB or services required
- Test files mirror the module structure: `tests/Unit/Services/`, `tests/Feature/Api/`

### Consequences
- 168 tests covering all 13 modules
- ~15s total execution time
- Tests function as living documentation

---

## ADR-006: Database Schema from Uganda CBC Requirements
**Date:** 2026-07-01

### Context
The system must align with Uganda's Competency-Based Curriculum (CBC) for Lower Secondary (S1-S4), including themes, learning outcomes, A-E grading, generic skills, and configurable report cards.

### Decision
Database schema designed from `ssms_uganda_database.sql` (the reference implementation) with 31 tables across 13 modules. Key design choices:
- Grades, skill ratings, assessment types in editable lookup tables
- Composite unique constraints enforce business rules (one enrollment per student per year, one attendance per student per date)
- Virtual column for invoice balance (computed, not stored)

### Consequences
- School can configure grading bands and skill rating scales without code changes
- Report card layout not hard-coded — school adapts per NCDC/UNEB guidance
- Schema matches the reference PHP starter, easing transition

---

## ADR-007: 33 Entity Service Providers
**Date:** 2026-07-01

### Context
Each entity needs its Repository and Service interfaces bound to implementations for dependency injection.

### Decision
Each entity gets its own dedicated ServiceProvider that binds Interface → Implementation. All providers registered in `bootstrap/providers.php`. `AppServiceProvider` is never modified for entity bindings.

### Consequences
- Clear provider-per-entity ownership
- Easy to disable/replace an entity's bindings
- 34 providers registered, zero conflicts

---

## ADR-008: PDF Report Card Generation with dompdf
**Date:** 2026-07-29

### Context
Need to generate printable report cards with subject results, skill ratings, attendance summary, and teacher comments. Output must be a downloadable PDF.

### Decision
Use `barryvdh/laravel-dompdf` for PDF generation. A dedicated `ReportCardPdfBuilder` service constructs the view data (loads subject results, skill ratings, grading scale, attendance). A Blade template (`reports/report-card`) renders the PDF with inline CSS, DejaVu Sans font, and the SSMS green (#1f6f43) design system. PDF download available via `GET /api/report-cards/{id}/pdf`.

### Consequences
- No external PDF service dependency
- Template-driven — easy to customize layout
- Grading computed dynamically from GradingScale lookup table

---

## ADR-009: Profile Management - Separate Controller
**Date:** 2026-07-29

### Context
Profile management (avatar upload, password change, email verification) is distinct from authentication (login/logout). Mixing concerns violates SRP.

### Decision
Create a dedicated `ProfileController` and `ProfileRequest` for authenticated user profile operations:
- `GET /auth/profile` — returns current user with avatar URL
- `POST /auth/profile` — updates name, email, phone, password (optional), avatar (image upload)
- Avatar stored in `public/avatars/` on the `public` disk; old avatar deleted on replacement
- Password hashed only when provided (not empty)

### Consequences
- AuthController stays focused on login/register/logout/verification
- Profile updates handled separately with image upload support
- Frontend can send FormData for avatar upload

---

## ADR-011: Queue-Based CSV Import with Chunking
**Date:** 2026-07-29

### Context
Student CSV imports with hundreds or thousands of rows were processed synchronously in a single DB transaction. Large imports caused request timeouts and blocked the HTTP response. A single failure rolled back the entire import.

### Decision
- The `import()` method parses the CSV, validates the header, chunks data rows into batches of 100, and dispatches one `ImportStudentsJob` per chunk to the `database` queue
- Each row within a job runs in its own DB transaction — a single bad row does not block the rest of the chunk
- The controller returns immediately with `{success: true, message, batches, total}`
- Each `ImportStudentsJob` has a 5-minute timeout per chunk

### Consequences
- Import response is immediate, no request timeouts
- Failed rows are isolated — good rows still import
- Errors are logged per chunk via `Log::warning`
- Requires a queue worker running (`php artisan queue:work`)

---

## ADR-012: Bulk Attendance Register with Upsert Pattern
**Date:** 2026-07-29

### Context
Teachers need to mark attendance for an entire class on a given date. The existing single-record endpoint was impractical for daily class-wide attendance. The register view also needed a way to show the attendance status for all enrolled students in a stream on a given date.

### Decision
- `POST /api/attendance/register` accepts a bulk payload with `{term_id, attendance_date, records: [{student_id, status}]}`
- For each record, existing attendance for that student+date is deleted (soft upsert) before inserting the new record
- `recorded_by` is set from `$request->user()->id`
- `GET /api/attendance/register` with `term_id` and `attendance_date` query params returns the full register — optionally filtered by `stream_id` via active enrollments
- Request validation via dedicated `AttendanceRegisterRequest`

### Consequences
- One API call replaces N individual calls for a class
- Clear register view for teachers to see who was marked and who is missing
- Upsert pattern avoids composite unique constraint issues
- Dedicated FormRequest keeps validation consistent

---

## ADR-010: Marks → Report Card Pipeline
**Date:** 2026-07-29

### Context
Report cards should be auto-populated from existing assessment records (SubjectTermResult, GenericSkillRating, Attendance) rather than requiring manual data entry.

### Decision
- `ReportCardService::generateReportCard()` aggregates subject results, skill ratings, and attendance for a given student+term
- Exposed via `POST /api/report-cards/generate` which creates a new ReportCard with auto-populated attendance counts
- `ReportCardPdfBuilder` builds the PDF using the same aggregated data + grading scale lookup

### Consequences
- One-click report card generation from existing assessment data
- Attendance counts auto-populated from attendance records
- Consistent data between assessment records and report card output
