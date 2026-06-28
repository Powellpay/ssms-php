# SSMS - School Management System (PHP Starter)

A starter PHP/MySQL codebase for the **School Management System aligned to
Uganda's New Lower Secondary Curriculum (Competency-Based Curriculum)**.

This is a *starting point* for interns to build on - it includes working
authentication, a dashboard, a student list/add/view flow, and the core
**New Curriculum Report Card** page. Every other module described in
`SSMS_Design_Guide.md` (Assessment, Finance, Attendance, Timetable,
Discipline, Library, Announcements) follows the same pattern and is left
for the team to build, sprint by sprint.

## 1. Requirements

- PHP 8.0+ with the `pdo_mysql` extension enabled
- MySQL 5.7+ / MariaDB 10.3+ (or any WAMP/XAMPP/MAMP stack)
- A web server (Apache/Nginx) or just PHP's built-in server for development

## 2. Setup

1. **Create the database** - import the schema and seed data:

   ```bash
   mysql -u root -p < ssms_uganda_database.sql
   ```

   This creates the `ssms_uganda` database with all 34 tables, seed lookup
   data (roles, grading scale, generic skills, etc.) and two sample
   learners - one of them (Faith Achieng, admission no `S26-0001`) has a
   full set of Term 1 results so you can immediately view a working report
   card.

2. **Configure the database connection** - edit
   `config/database.php` if your MySQL host/user/password differ from the
   XAMPP/WAMP defaults (`127.0.0.1`, `root`, empty password).

3. **Point your web server at the `php-starter` folder** as the document
   root, or run PHP's built-in server from inside it:

   ```bash
   php -S localhost:8000
   ```

   Then open `http://localhost:8000/index.php`.

4. **Log in** with the default administrator account:

   - Username: `admin`
   - Password: `ChangeMe123!`

   **Change this password (or create new users) before going live** - see
   the `users` table and `modules/auth/login.php`.

## 3. Folder structure

```
php-starter/
├── config/
│   └── database.php       # PDO connection settings
├── includes/
│   ├── auth.php            # require_login(), require_role(), current_user()
│   ├── header.php          # shared page header + nav
│   └── footer.php           # shared page footer
├── assets/
│   └── css/style.css       # shared stylesheet (incl. report card styles)
├── modules/
│   ├── auth/                # login / logout
│   ├── students/            # list / add / view learners
│   └── reports/              # New Curriculum report card
└── index.php                 # dashboard
```

## 4. The New Curriculum Report Card

`modules/reports/index.php` lets a user pick a learner and a term, then
opens `modules/reports/student_report.php`, which renders:

1. Learner & class details
2. **Subject Performance** - CA score (20%), End of Term score (80%),
   Final score, Grade (A-E) and descriptor, and the subject teacher's
   comment - pulled from the `v_subject_term_report` view
3. **Generic Skills Assessment** - ratings for Communication, Cooperation
   & Self-Directed Learning, Critical Thinking & Problem Solving, and
   Creativity & Innovation
4. **Attendance** summary (days present/absent)
5. **Comments** - class teacher & head teacher remarks, next term date
6. A **Grading Key** legend, generated from the `grading_scale` and
   `skill_rating_scale` tables

A "Print / Save as PDF" button is included - the stylesheet has a
print-friendly layout that hides the navigation.

Try it with the seeded data:
`modules/reports/student_report.php?student_id=1&term_id=1`

### ⚠️ Before going live

There is **no single nationally-mandated layout** for the New Curriculum
report card - NCDC sets the assessment framework (themes, learning
outcomes, generic skills, CA + End-of-Cycle weighting, A-E grading), but
each school designs its own report card. Before this goes into production:

- Compare this layout against your school's actual printed report card
- Confirm the `grading_scale` bands match your school's policy
- Confirm the wording/levels in `skill_rating_scale` match your school's
  report (some schools use 3 levels, others 4, with different wording)
- Add your school's name, logo/letterhead, and motto to the report header
- Decide whether you need class rankings/positions (the New Curriculum
  does not require these, but some schools still include them)

## 5. Next steps

Follow the sprint order in `SSMS_Design_Guide.md` to build out the
remaining modules (Assessment entry forms, Attendance registers, Finance,
Timetable, Discipline, Library, Announcements), reusing the same
`config/database.php`, `includes/auth.php`, `includes/header.php` /
`footer.php`, and `assets/css/style.css` patterns shown here.
