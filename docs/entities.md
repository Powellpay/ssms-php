# School Management System — Entity Documentation

## Role - 2026-07-01

### Fields
- id: int - Primary key
- role_name: string - Unique role name (Administrator, Head Teacher, Director of Studies, Teacher, Bursar, Librarian, Parent, Student)
- description: string - Optional role description

### API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/roles | List all |
| POST | /api/roles | Create |
| GET | /api/roles/{id} | Get one |
| PUT | /api/roles/{id} | Update |
| DELETE | /api/roles/{id} | Delete |

---

## User - 2026-07-01 (updated 2026-07-29)

### Fields
- id: int - Primary key
- role_id: int - FK to roles
- username: string - Unique username
- name: string - Display name
- email: string - Unique email
- password: string - Bcrypt hashed
- phone: string - Contact number
- status: string - active/inactive
- avatar: string (nullable) - Avatar image path
- email_verified_at: datetime (nullable) - Email verification timestamp
- last_login: datetime - Last login timestamp

### API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/auth/register | Register new user |
| POST | /api/auth/login | Login (returns token) |
| POST | /api/auth/logout | Revoke token |
| GET | /api/auth/me | Current user profile |
| GET | /api/auth/profile | Get profile (same as me) |
| POST | /api/auth/profile | Update profile (name, email, phone, avatar, password) |
| GET | /api/users | List all users |
| POST | /api/users | Create user |
| GET | /api/users/{id} | Get one |
| PUT | /api/users/{id} | Update |
| DELETE | /api/users/{id} | Delete |

---

## AcademicYear - 2026-07-01

### Fields
- id: int - Primary key
- year_name: string - e.g. '2026'
- start_date: date - Year start
- end_date: date - Year end
- is_current: boolean - Only one active at a time

### API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/academic-years | List all |
| POST | /api/academic-years | Create |
| GET | /api/academic-years/current | Get current year |
| POST | /api/academic-years/{id}/set-current | Set as current |
| GET | /api/academic-years/{id} | Get one |
| PUT | /api/academic-years/{id} | Update |
| DELETE | /api/academic-years/{id} | Delete |

---

## Term - 2026-07-01

### Fields
- id: int - Primary key
- academic_year_id: int - FK to academic_years
- term_name: string - Term 1/2/3
- start_date: date
- end_date: date
- next_term_begins: date
- is_current: boolean

---

## ClassLevel - 2026-07-01

### Fields
- id: int - Primary key
- level_name: string - S1, S2, S3, S4
- numeric_level: int - 1, 2, 3, 4
- description: string

---

## Stream - 2026-07-01

### Fields
- id: int - Primary key
- class_level_id: int - FK
- academic_year_id: int - FK
- stream_name: string - A, B, East, etc.
- class_teacher_id: int - FK to staff

---

## Staff - 2026-07-01

### Fields
- id: int - Primary key
- user_id: int - FK to users (nullable)
- staff_no: string - Unique staff number
- first_name: string
- last_name: string
- gender: string - Male/Female
- dob: date
- phone: string
- email: string
- designation: string - Teacher, Head Teacher, etc.
- status: string - active/on leave/left

---

## Student - 2026-07-01 (updated 2026-07-29)

### API Endpoints (updated)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/students | List all |
| POST | /api/students | Create |
| GET | /api/students/{id} | Get one |
| PUT | /api/students/{id} | Update |
| DELETE | /api/students/{id} | Delete |
| POST | /api/students/import | Import from CSV (chunks to queue, returns immediately) |
| GET | /api/students/import/template | Download CSV template |

### Import Flow
1. User uploads CSV via `POST /api/students/import` with `stream_id` + `academic_year_id`
2. Backend validates header, chunks rows into batches of 100
3. Each chunk dispatched to `ImportStudentsJob` on the `database` queue (5-min timeout per chunk)
4. Each row within a chunk runs in its own DB transaction
5. Response returns immediately: `{success: true, message, batches, total}`
6. Queue worker processes in background (`php artisan queue:work`)

### Fields
- id: int - Primary key
- user_id: int - FK to users (nullable)
- admission_no: string - Unique admission number
- lin: string - UNEB Learner ID
- first_name: string
- last_name: string
- gender: string - Male/Female
- dob: date
- admission_date: date
- status: string - active/transferred/graduated/dropped

---

## Guardian - 2026-07-01

### Fields
- id: int - Primary key
- first_name: string
- last_name: string
- relationship: string - Father, Mother, Guardian
- phone: string
- email: string
- occupation: string

---

## Enrollment - 2026-07-01

### Fields
- id: int - Primary key
- student_id: int - FK
- stream_id: int - FK
- academic_year_id: int - FK
- enrollment_date: date
- status: string - active/promoted/repeated/transferred/left

---

## Subject - 2026-07-01

### Fields
- id: int - Primary key
- subject_code: string - Unique code (MTC, ENG, etc.)
- subject_name: string
- category: string - Core/Elective/Pre-Vocational

---

## ClassSubject - 2026-07-01

### Fields
- id: int - Primary key
- class_level_id: int - FK
- subject_id: int - FK
- is_compulsory: boolean

---

## SubjectTeacher - 2026-07-01

### Fields
- id: int - Primary key
- subject_id: int - FK
- stream_id: int - FK
- staff_id: int - FK
- academic_year_id: int - FK

---

## CurriculumTheme - 2026-07-01

### Fields
- id: int - Primary key
- subject_id: int - FK
- class_level_id: int - FK
- theme_code: string - e.g. 'MTC-S1-T1'
- theme_name: string - e.g. 'Numerical Concepts 1'
- description: text

---

## LearningOutcome - 2026-07-01

### Fields
- id: int - Primary key
- theme_id: int - FK to curriculum_themes
- outcome_code: string - e.g. 'MTC-S1-T1-LO1'
- description: text

---

## GenericSkill - 2026-07-01

### Fields
- id: int - Primary key
- skill_name: string - Unique
- description: string

---

## AssessmentType - 2026-07-01

### Fields
- id: int - Primary key
- type_name: string
- category: string - Formative/Summative
- weight_percentage: decimal(5,2)

---

## GradingScale - 2026-07-01

### Fields
- id: int - Primary key
- grade: string - A, B, C, D, E
- descriptor: string - Exceptional, Outstanding, etc.
- min_score: decimal(5,2)
- max_score: decimal(5,2)

---

## SkillRatingScale - 2026-07-01

### Fields
- id: int - Primary key
- rating_code: string - BEG, DEV, PRO, MAS
- rating_label: string - Beginning, Developing, Proficient, Mastery
- rating_value: tinyint - 1, 2, 3, 4

---

## AssessmentRecord - 2026-07-01

### Fields
- id: int - Primary key
- student_id: int - FK
- subject_id: int - FK
- theme_id: int - FK (nullable)
- learning_outcome_id: int - FK (nullable)
- assessment_type_id: int - FK
- term_id: int - FK
- score: decimal(5,2)
- max_score: decimal(5,2)
- date_recorded: date

---

## GenericSkillRating - 2026-07-01

### Fields
- id: int - Primary key
- student_id: int - FK
- term_id: int - FK
- generic_skill_id: int - FK
- rating_id: int - FK to skill_rating_scale
- remarks: string

---

## SubjectTermResult - 2026-07-01

### Fields
- id: int - Primary key
- student_id: int - FK
- subject_id: int - FK
- term_id: int - FK
- ca_score: decimal(5,2) - CA score (out of 20)
- eot_score: decimal(5,2) - End of term exam (out of 80)
- final_score: decimal(5,2) - ca_score + eot_score
- final_grade: string - A-E
- subject_teacher_comment: string

---

## ReportCard - 2026-07-01 (updated 2026-07-29)

### Fields
- id: int - Primary key
- student_id: int - FK
- term_id: int - FK
- stream_id: int - FK
- days_present: int
- days_absent: int
- class_teacher_comment: text
- head_teacher_comment: text
- next_term_begins: date
- date_issued: date

### API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/report-cards | List all |
| POST | /api/report-cards | Create |
| POST | /api/report-cards/generate | Generate from assessment records + attendance |
| GET | /api/report-cards/{id} | Get one |
| PUT | /api/report-cards/{id} | Update |
| DELETE | /api/report-cards/{id} | Delete |
| GET | /api/report-cards/{id}/pdf | Download report card as PDF |

---

## Attendance - 2026-07-01 (updated 2026-07-29)

### Fields
- id: int - Primary key
- school_id: int - FK to schools
- student_id: int - FK
- term_id: int - FK
- attendance_date: date
- status: string - Present/Absent/Late/Excused
- recorded_by: int - FK to staff (nullable)

### API Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/attendance | List all |
| POST | /api/attendance | Create single record |
| GET | /api/attendance/{id} | Get one |
| PUT | /api/attendance/{id} | Update |
| DELETE | /api/attendance/{id} | Delete |
| POST | /api/attendance/register | Bulk register attendance for a term/date |
| GET | /api/attendance/register | Get register for a term/date (optionally filtered by stream) |

### Files Generated/Updated
- [x] Collection: `app/Domain/Attendance/Resources/AttendanceCollection.php`
- [x] Controller: `app/Domain/Attendance/Controllers/AttendanceController.php`
- [x] Model: `app/Domain/Attendance/Models/Attendance.php`
- [x] Provider: `app/Domain/Attendance/Providers/AttendanceServiceProvider.php`
- [x] Repository: `app/Domain/Attendance/Repositories/Eloquent/AttendanceRepository.php`
- [x] Repository Interface: `app/Domain/Attendance/Repositories/Contracts/AttendanceRepositoryInterface.php`
- [x] Request: `app/Domain/Attendance/Requests/AttendanceRequest.php`
- [x] Request (Register): `app/Domain/Attendance/Requests/AttendanceRegisterRequest.php`
- [x] Resource: `app/Domain/Attendance/Resources/AttendanceResource.php`
- [x] Routes: `app/Domain/Attendance/routes/attendance/_index.php`
- [x] Service: `app/Domain/Attendance/Services/AttendanceService.php`
- [x] Service Interface: `app/Domain/Attendance/Services/Contracts/AttendanceServiceInterface.php`

---

## ImportStudentsJob - 2026-07-29

### Fields (constructor)
- rows: array - Chunk of CSV data rows
- header: array - CSV header columns
- schoolId: int - School for multi-tenancy
- defaultStreamId: int|null - Optional stream for enrollment
- academicYearId: int|null - Optional academic year for enrollment

### Description
Queue job that processes a chunk of student data from CSV import. Each row is validated (required fields, gender), a Student record is created, and an optional Enrollment record is created. Runs each row in its own DB transaction so partial failures don't block the batch. Errors are logged via `Log::warning`.

### Files Generated
- [x] Job: `app/Domain/Students/Jobs/ImportStudentsJob.php`

---

## StudentImportController - Updated 2026-07-29

### Changes
- `import()` now parses CSV into chunks of 100 rows and dispatches `ImportStudentsJob` to the queue for each chunk
- Returns immediate response with batch count and total rows
- `downloadTemplate()` now sends `Cache-Control: no-cache, no-store, must-revalidate` and `Pragma: no-cache` headers to fix browser download caching issues

### Files Updated
- [x] Controller: `app/Domain/Students/Controllers/StudentImportController.php`

## Timetable - 2026-07-01

### Fields
- id: int - Primary key
- stream_id: int - FK
- subject_id: int - FK
- staff_id: int - FK
- academic_year_id: int - FK
- day_of_week: string - Monday-Friday
- period_no: tinyint
- start_time: time
- end_time: time

---

## FeeStructure - 2026-07-01

### Fields
- id: int - Primary key
- class_level_id: int - FK
- term_id: int - FK
- fee_category: string - Tuition, Lunch, etc.
- amount: decimal(12,2)

---

## Invoice - 2026-07-01

### Fields
- id: int - Primary key
- student_id: int - FK
- term_id: int - FK
- total_amount: decimal(12,2)
- amount_paid: decimal(12,2)
- balance: decimal(12,2) - Virtual: total_amount - amount_paid
- issue_date: date
- due_date: date
- status: string - unpaid/partial/paid

---

## Payment - 2026-07-01

### Fields
- id: int - Primary key
- invoice_id: int - FK
- student_id: int - FK
- amount: decimal(12,2)
- payment_method: string - Cash/Mobile Money/Bank Transfer/Cheque
- reference_no: string
- payment_date: date
- received_by: int - FK to staff

---

## DisciplineRecord - 2026-07-01

### Fields
- id: int - Primary key
- student_id: int - FK
- term_id: int - FK
- incident_date: date
- description: text
- action_taken: string

---

## LibraryBook - 2026-07-01

### Fields
- id: int - Primary key
- title: string
- author: string
- isbn: string
- category: string
- total_copies: int
- available_copies: int

---

## BookLoan - 2026-07-01

### Fields
- id: int - Primary key
- book_id: int - FK
- student_id: int - FK (nullable)
- staff_id: int - FK (nullable)
- borrow_date: date
- due_date: date
- return_date: date (nullable)
- status: string - borrowed/returned/overdue

---

## Announcement - 2026-07-01

### Fields
- id: int - Primary key
- title: string
- message: text
- target_role: string - 'all' or specific role name
- created_by: int - FK to users
