# SSMS Intern Backlog — GitHub Issues

This is a ready-to-copy backlog of 18 issues for the School Management
System (SSMS) project, based on `ssms_uganda_database.sql`,
`SSMS_Design_Guide.md`, and the `php-starter` codebase already provided.

**How to use this:**
- Copy each "Title" into a new GitHub issue title, and the "Description"
  block into the issue body.
- Add the suggested labels (create them in the repo first: `setup`,
  `module`, `core`, `report`, `polish`, `qa`).
- Add each issue to your Project board and order them roughly as listed —
  issues 2–7 can run in parallel once Issue 1 is done; Issues 8–10 depend
  on 5–7; Issues 11–16 are mostly independent and good for splitting across
  pairs; Issues 17–18 should be last.
- Each issue lists the database tables and starter files it touches, so
  interns know exactly where to look in `ssms_uganda_database.sql` and
  `php-starter/`.

---

## Issue 1: Project setup & codebase walkthrough
**Labels:** `setup`

**Description:**
Get the SSMS starter running locally and understand the codebase before
building new modules.

**Tasks:**
- [ ] Import `ssms_uganda_database.sql` into MySQL/MariaDB (WAMP/XAMPP is fine).
- [ ] Configure `php-starter/config/database.php` for your local setup.
- [ ] Run the app (`php -S localhost:8000` from inside `php-starter/`) and
      log in as `admin` / `ChangeMe123!`.
- [ ] Open the sample report card: `modules/reports/student_report.php?student_id=1&term_id=1`.
- [ ] Read `SSMS_Design_Guide.md` end-to-end as a team.
- [ ] Walk through `includes/header.php`, `includes/footer.php`,
      `includes/auth.php`, `config/database.php`, and `assets/css/style.css`
      — these are reused by every module you build.
- [ ] Push the starter code to a shared GitHub repo and agree on a branch/PR
      workflow (one branch per issue, PR reviewed by another intern before
      merging).

**Acceptance criteria:**
Everyone on the team can log in locally, browse the student list, and view
the sample report card. The starter code is in a shared repo with a working
branch/PR workflow.

---

## Issue 2: User & role management (Module 1)
**Labels:** `module`

**Tables:** `roles`, `users`
**Builds on:** `includes/auth.php`, `modules/auth/login.php`

**Description:**
The login system exists, but there's no UI to manage user accounts yet.
Build an admin-only screen to manage logins for staff, parents, and students.

**Tasks:**
- [ ] `modules/users/list.php` — list all users with their role and active status.
- [ ] `modules/users/add.php` — create a new user (username, password —
      hashed with `password_hash()`, role from `roles` table).
- [ ] `modules/users/edit.php` — edit a user's role, reset their password,
      activate/deactivate the account.
- [ ] Restrict all of the above to the `Administrator` role using
      `require_role(['Administrator'])`.
- [ ] When creating a staff or student user, link the new `users.id` back
      to the corresponding `staff.user_id` / `students.user_id`.

**Acceptance criteria:**
An Administrator can create, edit, and deactivate accounts for any role.
A non-admin who tries to access these pages gets a 403.

---

## Issue 3: Academic structure management (Module 2)
**Labels:** `module`

**Tables:** `academic_years`, `terms`, `class_levels`, `streams`

**Description:**
Build the screens that let an admin set up the school calendar and class
structure — this underpins almost everything else (enrollments, results,
timetables).

**Tasks:**
- [ ] `modules/academics/years.php` — CRUD for academic years; only one year
      can have `is_current = 1` at a time (enforce this in code).
- [ ] `modules/academics/terms.php` — CRUD for terms linked to an academic
      year; only one term per year can be `is_current = 1`.
- [ ] `modules/academics/classes.php` — CRUD for class levels (S1–S4).
- [ ] `modules/academics/streams.php` — CRUD for streams, scoped to a class
      level + academic year, with a class teacher picked from `staff`.

**Acceptance criteria:**
An admin can create a new academic year with three terms, mark the current
year/term, and create class levels and streams for it. The dashboard
(`index.php`) reflects the new current term.

---

## Issue 4: Staff management (Module 3)
**Labels:** `module`

**Table:** `staff`

**Description:**
Build CRUD for teaching and non-teaching staff records, which feed into
class-teacher and subject-teacher assignments later.

**Tasks:**
- [ ] `modules/staff/list.php` — list staff with designation and status.
- [ ] `modules/staff/add.php` — register a new staff member, optionally
      creating a linked login account (reuse logic from Issue 2).
- [ ] `modules/staff/view.php` / `edit.php` — view/edit a staff profile.

**Acceptance criteria:**
A new teacher can be registered and then selected as a class teacher
(Issue 3) and subject teacher (Issue 6).

---

## Issue 5: Student admissions — edit, guardians & promotion (Module 4)
**Labels:** `module`

**Tables:** `students`, `guardians`, `student_guardians`, `enrollments`
**Builds on:** `modules/students/list.php`, `add.php`, `view.php`

**Description:**
The starter has student list/add/view. Extend it with editing, full
guardian management, and yearly promotion/transfer.

**Tasks:**
- [ ] `modules/students/edit.php` — edit a learner's bio-data.
- [ ] Guardian management on `view.php` — add/edit/remove guardians and link
      them via `student_guardians` (a learner can have more than one
      guardian; one marked `is_primary_contact`).
- [ ] `modules/students/promote.php` — enroll a learner into a new academic
      year/stream (status: promoted / repeated / transferred / left),
      respecting the `uq_enrollment (student_id, academic_year_id)` constraint.
- [ ] Add class/stream/status filters to `list.php`.

**Acceptance criteria:**
A learner's details and guardians can be edited from their profile, and a
learner can be promoted into the next academic year without breaking the
unique enrollment constraint.

---

## Issue 6: Subjects & curriculum allocation (Module 5a)
**Labels:** `module`

**Tables:** `subjects`, `class_subjects`, `subject_teachers`

**Description:**
Build the screens that configure which subjects each class level studies,
and which teacher takes each subject for each stream.

**Tasks:**
- [ ] `modules/curriculum/subjects.php` — CRUD for subjects (code, name,
      category: Core/Elective/Pre-Vocational).
- [ ] `modules/curriculum/class_subjects.php` — map subjects to class levels
      and mark compulsory vs elective.
- [ ] `modules/curriculum/subject_teachers.php` — allocate a teacher to a
      subject + stream for the current academic year.

**Acceptance criteria:**
For S1–S4, each class level has a correct subject combination, and every
subject/stream pair has an assigned teacher.

---

## Issue 7: Curriculum themes, learning outcomes & generic skills (Module 5b)
**Labels:** `module`

**Tables:** `curriculum_themes`, `learning_outcomes`, `generic_skills`

**Description:**
This is where the NCDC "theme → learning outcome" structure is managed —
the foundation for competency-based assessment entry in Issue 8.

**Tasks:**
- [ ] `modules/curriculum/themes.php` — CRUD for themes/topics per subject +
      class level.
- [ ] `modules/curriculum/outcomes.php` — CRUD for learning outcomes under a
      theme.
- [ ] `modules/curriculum/generic_skills.php` — manage the list of generic
      skills (4 are seeded; allow editing wording to match your school's
      report card).

**Acceptance criteria:**
At least one subject per class level has its real NCDC themes and learning
outcomes entered from the official syllabus (not just the S1 Maths sample
that's seeded).

---

## Issue 8: Assessment entry — CA & exam scores (Module 6a)
**Labels:** `module`, `core`

**Tables:** `assessment_types`, `assessment_records`, `subject_term_results`
**Depends on:** Issues 6, 7

**Description:**
This is the data-entry screen teachers use all term. It feeds directly into
the report card (Issue 10).

**Tasks:**
- [ ] `modules/assessment/record.php` — a teacher picks subject + stream +
      term + assessment type, then enters a score per learner into
      `assessment_records` (optionally tagged to a `theme_id` /
      `learning_outcome_id`).
- [ ] `modules/assessment/aggregate.php` — an "end of term" page that
      computes, per learner per subject:
      - `ca_score` = formative scores scaled to **20**
      - `eot_score` = summative exam score scaled to **80**
      - `final_score` = `ca_score + eot_score`
      - `final_grade` = looked up from `grading_scale` by `final_score`
      and writes/updates the matching row in `subject_term_results`.
- [ ] Allow the subject teacher to enter a `subject_teacher_comment`.

**Acceptance criteria:**
A teacher can record several CA scores and an end-of-term exam score for a
whole stream, run the aggregation, and see correct `subject_term_results`
rows with grades matching the `grading_scale` bands.

---

## Issue 9: Generic skills rating entry (Module 6b)
**Labels:** `module`

**Tables:** `generic_skills`, `skill_rating_scale`, `generic_skill_ratings`
**Depends on:** Issue 7

**Description:**
Build the screen a class/form teacher uses to rate every learner in their
stream on the four generic skills each term.

**Tasks:**
- [ ] `modules/assessment/skills.php` — teacher picks stream + term, then
      for every learner sees a row with a dropdown (from
      `skill_rating_scale`) for each generic skill, plus an optional remark.
- [ ] Save as an "upsert" — respect the `uq_skill_rating (student_id,
      term_id, generic_skill_id)` constraint (update if a rating already
      exists for that learner/term/skill).

**Acceptance criteria:**
A class teacher can rate every learner in their stream on all four generic
skills for a term from a single screen, and re-saving updates existing
ratings rather than erroring on the unique constraint.

---

## Issue 10: New Curriculum report card — polish & extend (Module 7)
**Labels:** `module`, `core`, `report`

**Files:** `modules/reports/index.php`, `modules/reports/student_report.php`
(already built and working with sample data for student 1, term 1)
**Depends on:** Issues 8, 9, 11

**Description:**
The report card page already renders subject results, generic skills,
attendance and comments. This issue is about making it production-ready for
a real school.

**Tasks:**
- [ ] Replace the "Your School Name Here" placeholder with the school's real
      name, address, and logo (consider a small `config/school.php` settings
      file rather than hard-coding).
- [ ] `modules/reports/comments.php` — form for the class teacher and head
      teacher to enter `report_cards.class_teacher_comment` /
      `head_teacher_comment` / `next_term_begins` / `date_issued`.
- [ ] `modules/reports/class_reports.php` — bulk-generate/print report cards
      for an entire stream at once.
- [ ] Once Issue 11 (Attendance) is done, auto-populate
      `report_cards.days_present` / `days_absent` from the `attendance`
      table instead of manual entry.
- [ ] Sit with a teacher/head teacher and compare this layout against the
      school's actual printed report card; adjust per the "Before Going
      Live" checklist in `SSMS_Design_Guide.md` §6 (grading bands, skill
      rating wording, ranking decision).
- [ ] Test the report for the second seeded learner (Daniel Wasswa,
      `student_id=2`) once results/skills data exists for them.

**Acceptance criteria:**
A head teacher can enter comments for a learner, and print a polished,
school-branded report card for any learner/term, with attendance pulled
automatically from the attendance register.

---

## Issue 11: Attendance register (Module 8)
**Labels:** `module`

**Table:** `attendance`

**Description:**
Build the daily attendance register and a term summary that feeds the
report card.

**Tasks:**
- [ ] `modules/attendance/register.php` — class teacher marks each learner
      in their stream as `Present` / `Absent` / `Late` / `Excused` for a
      given date (respect `uq_attendance (student_id, attendance_date)`).
- [ ] `modules/attendance/summary.php` — per-term summary per learner
      (counts of each status), used by Issue 10 to populate
      `report_cards.days_present` / `days_absent`.

**Acceptance criteria:**
Daily attendance can be recorded for a stream, and the term summary numbers
match what appears on the report card.

---

## Issue 12: Timetable management (Module 9)
**Labels:** `module`

**Table:** `timetable`

**Description:**
Build a simple weekly timetable builder and viewer.

**Tasks:**
- [ ] `modules/timetable/manage.php` — admin/DOS builds a weekly timetable
      per stream (day, period/time slot, subject, teacher).
- [ ] `modules/timetable/view.php` — view the timetable by stream (for
      students) or by teacher (for staff).

**Acceptance criteria:**
Each stream has a full weekly timetable that students and teachers can both
view, filtered appropriately.

---

## Issue 13: Finance — fee structures, invoicing & payments (Module 10)
**Labels:** `module`

**Tables:** `fee_structures`, `invoices`, `payments`

**Description:**
Build the fee billing and payment-tracking workflow for the bursar.

**Tasks:**
- [ ] `modules/finance/fee_structures.php` — CRUD fee amounts per class
      level + term.
- [ ] `modules/finance/invoices.php` — generate term invoices for a stream
      from `fee_structures` (bulk action); `balance` is a generated column —
      don't write to it directly.
- [ ] `modules/finance/payments.php` — record a payment against an invoice
      (cash/mobile money/bank, reference number).
- [ ] `modules/finance/student_statement.php` — per-learner statement
      showing invoices, payments, and running balance.

**Acceptance criteria:**
Generating term invoices for a stream produces correct amounts per learner,
and recording a payment correctly reduces that invoice's balance.

---

## Issue 14: Discipline records (Module 11)
**Labels:** `module`

**Table:** `discipline_records`

**Description:**
Build a simple conduct/incident log per learner.

**Tasks:**
- [ ] `modules/discipline/list.php` / `add.php` — log incidents (date,
      description, action taken, recorded by staff member).
- [ ] Show a learner's discipline history on `modules/students/view.php`.

**Acceptance criteria:**
A staff member can log a discipline incident for a learner, and it appears
on that learner's profile.

---

## Issue 15: Library management (Module 12)
**Labels:** `module`

**Tables:** `library_books`, `book_loans`

**Description:**
Build a basic library catalogue and borrowing system.

**Tasks:**
- [ ] `modules/library/books.php` — CRUD book catalogue (title, author,
      ISBN, total/available copies).
- [ ] `modules/library/loans.php` — issue a book to a learner/staff member,
      record due date, mark as returned, and flag overdue loans.

**Acceptance criteria:**
A book can be added to the catalogue, issued, returned, and overdue loans
are clearly flagged in the UI.

---

## Issue 16: Announcements (Module 13)
**Labels:** `module`

**Table:** `announcements`

**Description:**
Build school-wide or role-targeted notices, surfaced on the dashboard.

**Tasks:**
- [ ] `modules/announcements/list.php` — list active announcements.
- [ ] `modules/announcements/add.php` — staff/admin post an announcement,
      optionally targeted to a specific role.
- [ ] Show the latest 3–5 relevant announcements on `index.php`.

**Acceptance criteria:**
An announcement posted by an admin and targeted at "Parent" shows on the
dashboard for parent accounts, and a general announcement shows for everyone.

---

## Issue 17: Navigation, dashboard & role-based access polish
**Labels:** `polish`
**Depends on:** Issues 2–16 (do this once most modules exist)

**Description:**
Tie all the modules together into one coherent app with proper role-based
access.

**Tasks:**
- [ ] Update `includes/header.php` navigation to link to every completed
      module, grouped sensibly (Academics, Students, Assessment, Reports,
      Finance, etc.).
- [ ] Update `index.php` dashboard with widgets/quick links: counts per
      module, current term, recent announcements, overdue fees, low
      attendance alerts.
- [ ] Go through every module and apply `require_role()` consistently based
      on the 8 roles seeded in the `roles` table (Administrator, Head
      Teacher, Director of Studies, Teacher, Bursar, Librarian, Parent,
      Student).

**Acceptance criteria:**
Each role sees a relevant, uncluttered nav/dashboard, and attempting to open
a module outside one's role shows a clear "no permission" message rather
than an error.

---

## Issue 18: Testing, QA & deployment guide
**Labels:** `qa`
**Depends on:** Issue 17

**Description:**
Final hardening pass before handing the system to a real school.

**Tasks:**
- [ ] Write a test plan covering CRUD for every module plus the report card,
      including edge cases (learner with no results yet, no guardians, no
      attendance recorded, etc. — the report card should degrade gracefully,
      as it already does for empty sections).
- [ ] Do a full bug-fix pass across all modules based on the test plan.
- [ ] Write `DEPLOYMENT.md`: WAMP/XAMPP setup, importing
      `ssms_uganda_database.sql`, configuring `config/database.php`,
      changing the default admin password, and walking through the "Before
      Going Live" checklist in `SSMS_Design_Guide.md` §6 with the school's
      Head Teacher / Director of Studies.

**Acceptance criteria:**
All modules pass the test plan, and a non-developer at the school can follow
`DEPLOYMENT.md` to get the system running and configured for their own
grading bands, subjects, and report card branding.
