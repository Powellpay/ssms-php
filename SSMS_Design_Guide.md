# School Management System (SSMS) - Design Guide
## Aligned to Uganda's New Lower Secondary Curriculum (Competency-Based Curriculum)

This guide accompanies `ssms_uganda_database.sql`. Read it before writing any
PHP code - it explains *why* the database is structured the way it is, what
each module covers, and the order in which interns should build the system.

---

## 1. Background: Uganda's New Curriculum and what it means for the system

Uganda's Ministry of Education and Sports, through the **National Curriculum
Development Centre (NCDC)**, rolled out a **Competency-Based Curriculum (CBC)**
for Lower Secondary (S1-S4), starting with S1 in 2020. The first cohort sat
the Uganda Certificate of Education (UCE) under this curriculum in 2024.

Key points that shape the database design:

1. **Themes/Topics and Learning Outcomes** - Each subject syllabus is broken
   into themes/topics, and each theme has specific learning outcomes
   (competencies) that a learner should demonstrate. This is reflected in the
   `curriculum_themes` and `learning_outcomes` tables.

2. **Formative vs Summative Assessment** - Learners are assessed continuously
   (Continuous Assessment/CA - tests, projects, "Activities of Integration",
   practical work) and through summative exams (End of Topic, End of Term,
   and the End of Cycle national exam at S4). For the UCE certificate, CA
   contributes 20% and the End of Cycle exam contributes 80% of the final
   subject result. This is reflected in `assessment_types` and
   `assessment_records`.

3. **Letter Grades A-E** - From the 2024 UCE results onward, UNEB replaced the
   old 1-9 (Distinction/Credit/Pass/Fail) grading with letter grades:

   | Grade | Descriptor   | What it means                                         |
   |-------|--------------|--------------------------------------------------------|
   | A     | Exceptional  | Advanced competency, applies knowledge innovatively   |
   | B     | Outstanding  | High competency, applies skills effectively           |
   | C     | Satisfactory | Adequate knowledge and skill application              |
   | D     | Basic        | Minimum competency, limited practical application    |
   | E     | Elementary   | Beginning level, difficulty applying knowledge        |

   Every learner gets a grade (nobody "fails" outright); a certificate
   requires at least a D in one subject. This is reflected in `grading_scale`.

4. **Generic Skills** - The curriculum emphasises cross-cutting skills such as
   Communication, Cooperation & Self-Directed Learning, Critical Thinking &
   Problem Solving, and Creativity & Innovation. Many schools report on these
   separately on the report card. This is reflected in `generic_skills`,
   `skill_rating_scale`, and `generic_skill_ratings`.

5. **No single mandated report card** - NCDC sets the assessment framework,
   but each school designs its own report card layout. **This is why the
   schema keeps grading bands, skill ratings, assessment types, themes, and
   learning outcomes in editable lookup tables** rather than hard-coding them.
   Before going live, sit with the Head Teacher / Director of Studies and
   confirm the exact wording and bands your school's report card should use,
   and cross-check against the latest NCDC Curriculum Framework and UNEB
   assessment guidelines.

---

## 2. Module Overview

| # | Module | Key tables | What it does |
|---|--------|-----------|--------------|
| 1 | Users & Roles | `roles`, `users` | Login, authentication, role-based access (Admin, Head Teacher, DOS, Teacher, Bursar, Librarian, Parent, Student) |
| 2 | Academic Structure | `academic_years`, `terms`, `class_levels`, `streams` | Defines the school calendar (years/terms) and class structure (S1-S4, streams A/B/etc.) |
| 3 | Staff | `staff` | Teacher and non-teaching staff records, linked to user accounts |
| 4 | Students & Guardians | `students`, `guardians`, `student_guardians`, `enrollments` | Admissions, learner bio-data, parent/guardian contacts, yearly enrollment/promotion history |
| 5 | Curriculum | `subjects`, `class_subjects`, `subject_teachers`, `curriculum_themes`, `learning_outcomes`, `generic_skills` | Subject combinations per class, teacher allocations, and the NCC theme/competency structure |
| 6 | Assessment & Grading | `assessment_types`, `grading_scale`, `skill_rating_scale`, `assessment_records`, `generic_skill_ratings`, `subject_term_results` | Records CA and exam scores, computes grades, rates generic skills |
| 7 | Report Cards | `report_cards` (+ views) | Generates the "New Curriculum" report card per learner per term |
| 8 | Attendance | `attendance` | Daily attendance register, feeds into report card |
| 9 | Timetable | `timetable` | Weekly class timetable per stream |
| 10 | Finance | `fee_structures`, `invoices`, `payments` | Fee billing and payment tracking per term |
| 11 | Discipline | `discipline_records` | Conduct/discipline log per learner |
| 12 | Library | `library_books`, `book_loans` | Book catalogue and borrowing records |
| 13 | Communication | `announcements` | School-wide or role-targeted notices |

---

## 3. How the "New Curriculum Report" flows through the database

```
students --> enrollments --> streams --> class_levels
   |
   v
assessment_records (many per term: CA tests, projects, exams)
   - linked to: subjects, curriculum_themes, learning_outcomes, assessment_types
   |
   v  (teacher aggregates/term-end process computes these)
subject_term_results (one row per student/subject/term)
   - ca_score, eot_score, final_score, final_grade
   |
   v
generic_skill_ratings (one row per student/term/generic_skill)
   |
   v
report_cards (one row per student/term: attendance summary + comments)
   |
   v
PRINTED REPORT = report_cards + subject_term_results (joined via
v_subject_term_report view) + generic_skill_ratings + grading_scale descriptors
```

The two views included in the SQL file (`v_class_list` and
`v_subject_term_report`) are starting points for the report-card query - the
PHP report page will mostly be a `SELECT` from `v_subject_term_report` filtered
by `student_id` and `term_id`, plus a query against `generic_skill_ratings` and
`report_cards`.

---

## 4. Suggested PHP Project Structure

```
ssms-php/
├── config/
│   └── database.php          # PDO connection (uses ssms_uganda DB)
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── auth.php               # session check / role guard
├── assets/
│   ├── css/style.css
│   └── js/
├── modules/
│   ├── auth/                  # login, logout
│   ├── students/              # admissions, list, profile
│   ├── staff/
│   ├── academics/              # academic years, terms, classes, streams, subjects
│   ├── curriculum/             # themes & learning outcomes management
│   ├── assessment/             # record CA/exam scores, generic skill ratings
│   ├── reports/                # report card generation & printing
│   ├── attendance/
│   ├── timetable/
│   ├── finance/                # fee structures, invoices, payments
│   ├── discipline/
│   ├── library/
│   └── announcements/
├── index.php                   # login / dashboard router
└── ssms_uganda_database.sql
```

---

## 5. Recommended Build Order (Sprints / GitHub Issues)

Mirror the backlog-issue approach used for the Data Analysis track. Suggested
order, each as its own milestone/issue set:

1. **Setup** - Import `ssms_uganda_database.sql`, build `config/database.php`,
   build login/auth (Module 1).
2. **Academic structure CRUD** - academic years, terms, class levels, streams
   (Module 2).
3. **Staff management** - CRUD for staff records (Module 3).
4. **Student admissions** - student CRUD, guardians, enrollment/promotion
   (Module 4).
5. **Curriculum setup** - subjects, class-subject mapping, subject-teacher
   allocation, themes & learning outcomes, generic skills (Module 5).
6. **Assessment recording** - teacher UI to enter CA scores, project scores,
   exam scores per learning outcome; compute `subject_term_results` (Module 6).
7. **Generic skills rating** - teacher UI to rate each learner on the generic
   skills per term (Module 6).
8. **Report card generation** - compile `report_cards` + subject results +
   skill ratings into a printable PDF/HTML report (Module 7) - **this is the
   "New Curriculum Report" deliverable**.
9. **Attendance register** (Module 8).
10. **Timetable management** (Module 9).
11. **Finance** - fee structures, invoicing, payments, balances (Module 10).
12. **Discipline records** (Module 11).
13. **Library** (Module 12).
14. **Announcements/communication** (Module 13).

Each sprint can be split into intern tasks: one builds the database
queries/model, another builds the form/UI, another tests and reviews via pull
request - following the same git workflow used on the school database repo.

---

## 6. Before Going Live - Checklist

- [ ] Confirm the **grading_scale** bands match your school's current grading
      policy (the A-E bands in the seed data are illustrative).
- [ ] Confirm the **skill_rating_scale** wording matches your school's actual
      report card (the 4-level Beginning/Developing/Proficient/Mastery scale
      in the seed data is a common pattern but not an official fixed standard).
- [ ] Populate `subjects`, `class_subjects`, `curriculum_themes`, and
      `learning_outcomes` from the actual NCDC syllabuses for each subject and
      class level you teach.
- [ ] Change the default admin password (`admin` / `ChangeMe123!`).
- [ ] Decide whether your report card will show a class position/ranking -
      the CBC discourages ranking by division, but some schools still show a
      position; if so, add a computed field/view for it.
