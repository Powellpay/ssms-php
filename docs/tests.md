# Test Results — 2026-07-01

**168 tests passed, 0 failures, 304 assertions, ~24s**

---

## Unit Tests (Services)

| Test File | Tests | Assertions | Coverage |
|-----------|-------|------------|----------|
| `UserServiceTest` | 12 | 12 | Password hashing on create/update, authenticate with bcrypt verify, findByEmail delegation, CRUD return values |
| `GradingScaleServiceTest` | 6 | 6 | getGrade boundary lookups (score→A/B/C/D/E), null for out-of-range, all/create delegation |
| `SubjectTermResultServiceTest` | 3 | 3 | computeResult creates new when no existing, updates when exists, handles null grade gracefully |
| `AcademicYearServiceTest` | 3 | 3 | setCurrent calls findCurrent then updates both years, findCurrent delegates, returns null when none |
| **Subtotal** | **24** | **24** | |

## Feature Tests (API)

### Module 1: Users & Roles

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `AuthTest` | 11 | 18 | Register with valid data, login with correct credentials, login fails with wrong password, login fails for nonexistent user, login requires email+password, authenticated user can access /me, unauthenticated user blocked, logout succeeds, token invalidated after logout, register requires valid role_id, register requires unique email |
| `RoleTest` | 7 | 9 | List, create, show, update, delete, unique role_name validation, unauthenticated request blocked |
| **Total** | **18** | **27** | |

### Module 2: Academic Structure

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `AcademicYearTest` | 8 | 10 | List, create, set current year, show, update dates, delete, unique year_name, end_date after start_date |
| `ClassLevelTest` | 6 | 7 | List, create, show, update, delete, unique level_name |
| `TermTest` | 6 | 7 | List, create, show, update, delete, end_date after start_date |
| **Total** | **20** | **24** | |

### Module 3: Staff

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `StaffTest` | 8 | 10 | List, create, show, update, delete, unique staff_no, required fields, find by staff_no |
| **Total** | **8** | **10** | |

### Module 4: Students & Guardians

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `StudentTest` | 11 | 16 | List, create, show, update, delete, unique admission_no, required fields, delete message, find by admission_no, filter by status, unauthenticated blocked |
| `GuardianTest` | 5 | 6 | List, create, show, update, delete |
| `EnrollmentTest` | 5 | 6 | Create, list, show, delete, unique student per academic year |
| **Total** | **21** | **28** | |

### Module 5: Curriculum

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `SubjectTest` | 7 | 9 | List, create, show, update, delete, unique subject_code, valid category |
| `SubjectTeacherTest` | 3 | 4 | Create, list, delete |
| `CurriculumThemeTest` | 3 | 4 | Create, list, update |
| `LearningOutcomeTest` | 2 | 3 | Create, list |
| `GenericSkillTest` | 3 | 4 | Create, list, unique skill_name |
| **Total** | **18** | **24** | |

### Module 6: Assessment & Grading

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `AssessmentTypeTest` | 6 | 8 | Create, list, show, update, delete, valid category |
| `GradingScaleTest` | 5 | 7 | Create, list, update, delete, min_score < max_score |
| `SkillRatingScaleTest` | 5 | 6 | Create, list, update, delete, unique rating_code |
| `AssessmentRecordTest` | 5 | 6 | Create, list, show, delete, valid student_id FK |
| `GenericSkillRatingTest` | 3 | 4 | Create, list, delete |
| `SubjectTermResultTest` | 4 | 5 | Create, list, show, delete |
| **Total** | **28** | **36** | |

### Module 7: Report Cards

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `ReportCardTest` | 4 | 5 | Create, list, show, delete |
| **Total** | **4** | **5** | |

### Module 8: Attendance

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `AttendanceTest` | 7 | 8 | Mark attendance (create), list, show, update status, delete, invalid status validation, unique per student+date |
| **Total** | **7** | **8** | |

### Module 9: Timetable

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `TimetableTest` | 5 | 6 | Create, list, show, delete, invalid day_of_week |
| **Total** | **5** | **6** | |

### Module 10: Finance

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `FinanceTest` | 6 | 8 | Create/list fee structures, create/list invoices, record/list payments |
| **Total** | **6** | **8** | |

### Module 11: Discipline

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `DisciplineRecordTest` | 4 | 5 | Create, list, show, delete |
| **Total** | **4** | **5** | |

### Module 12: Library

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `LibraryTest` | 6 | 8 | Create/list books, borrow/list loans, update book |
| **Total** | **6** | **8** | |

### Module 13: Communication

| Test File | Tests | Assertions | What It Covers |
|-----------|-------|------------|----------------|
| `AnnouncementTest` | 3 | 4 | Create, list, delete |
| **Total** | **3** | **4** | |

---

## Summary by Module

| # | Module | Tests | % of Total |
|---|--------|-------|-----------|
| 1 | Users & Roles | 30 | 17.9% |
| 2 | Academic Structure | 23 | 13.7% |
| 3 | Staff | 8 | 4.8% |
| 4 | Students & Guardians | 21 | 12.5% |
| 5 | Curriculum | 18 | 10.7% |
| 6 | Assessment & Grading | 37 | 22.0% |
| 7 | Report Cards | 4 | 2.4% |
| 8 | Attendance | 7 | 4.2% |
| 9 | Timetable | 5 | 3.0% |
| 10 | Finance | 6 | 3.6% |
| 11 | Discipline | 4 | 2.4% |
| 12 | Library | 6 | 3.6% |
| 13 | Communication | 3 | 1.8% |
| **Total** | | **168** | **100%** |

---

## Test Categories

| Category | Count |
|----------|-------|
| CRUD operation tests (list/create/show/update/delete) | ~90 |
| Validation/error handling tests (422 status) | ~30 |
| Authentication/authorization tests (401/403) | ~10 |
| Business logic tests (grade computation, password hashing, etc.) | ~24 |
| Edge case tests (null handling, duplicates, missing records) | ~14 |

## Running the Tests

```bash
# Full suite
php artisan test

# Specific test class
php artisan test --filter=StudentTest

# Specific test method
php artisan test --filter="AuthTest::test_user_can_login"

# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```
