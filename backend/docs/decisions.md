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
