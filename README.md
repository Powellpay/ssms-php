# SSMS Uganda — PHP Application

School Management System aligned to Uganda's Competency-Based Curriculum.

## Database
Name: `ssms_uganda`  
Schema file: `ssms_uganda_database.sql`  
Default login: `admin` / `ChangeMe123!`

## Setup (XAMPP / WAMP)

1. **Import the database**
   ```bash
   mysql -u root -p < ssms_uganda_database.sql
   ```
   Or use phpMyAdmin → Import → select the .sql file.

2. **Copy this folder** to your web root:
   - XAMPP: `C:/xampp/htdocs/ssms/`
   - WAMP: `C:/wamp64/www/ssms/`

3. **Edit the database config**  
   Open `config/database.php` and set your MySQL username/password.

4. **Visit** `http://localhost/ssms/` in your browser.

## File Structure

```
ssms/
├── index.php                   ← Root redirect
├── config/
│   └── database.php            ← DB connection (edit this)
├── includes/
│   ├── auth.php                ← Session helpers
│   ├── header.php              ← Top bar + sidebar
│   └── footer.php              ← Close layout
├── assets/
│   ├── css/app.css             ← All styles (Teal Trust palette)
│   └── js/app.js               ← Sidebar toggle, live search
└── modules/
    ├── auth/
    │   ├── login.php           ← Login form → users JOIN roles
    │   └── logout.php
    ├── dashboard/
    │   └── index.php           ← Live stats from DB
    ├── students/
    │   ├── index.php           ← Paginated list with filters
    │   ├── add.php             ← INSERT students + enrollments
    │   └── view.php            ← Student profile + results
    ├── staff/                  ← (your Issue 4 work goes here)
    ├── attendance/             ← (your Issue 10 work goes here)
    ├── reports/                ← (your Issue 9 work goes here)
    └── finance/                ← (your Issue 12 work goes here)
```

## Roles & Access

| Role          | Dashboard | Students | Staff | Finance |
|---------------|-----------|----------|-------|---------|
| Admin         | ✅        | ✅       | ✅    | ✅      |
| Head Teacher  | ✅        | ✅       | ✅    | —       |
| Teacher       | ✅        | ✅       | —     | —       |
| Bursar        | ✅        | —        | —     | ✅      |

## Next Steps for Interns

Each module below maps to a GitHub issue in `Powellpay-LTD/ssms-php`:

- `modules/staff/`       → Issue 4 (Staff table)
- `modules/attendance/`  → Issue 10 (Attendance)  
- `modules/reports/`     → Issue 9 (Report Cards)
- `modules/finance/`     → Issue 12 (Finance)
- `modules/timetable/`   → Issue 11 (Timetable)
