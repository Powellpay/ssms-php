-- =====================================================================================
--  SCHOOL MANAGEMENT SYSTEM (SSMS) - DATABASE SCHEMA
--  Aligned to Uganda's New Lower Secondary Curriculum (Competency-Based Curriculum)
-- =====================================================================================
--
--  CONTEXT FOR INTERNS
--  --------------------
--  Uganda's National Curriculum Development Centre (NCDC) rolled out a Competency-
--  Based Curriculum (CBC) for Lower Secondary (S1-S4), first introduced with S1 in
--  2020. Key implications for school software:
--
--   1. Subjects are organised into THEMES/TOPICS, and each theme has one or more
--      LEARNING OUTCOMES (competencies). Learners are assessed against these
--      outcomes, not just "marks out of 100".
--
--   2. Assessment is split into:
--        - FORMATIVE (Continuous Assessment / CA) - done throughout the term via
--          tests, projects, practical work, "Activities of Integration".
--        - SUMMATIVE - End of Topic / End of Term / End of Cycle exams.
--      For the UCE (S4) certificate, CA contributes 20% and the End of Cycle exam
--      contributes 80% of the final subject result.
--
--   3. From 2024, UNEB replaced the old 1-9 (Distinction/Credit/Pass/Fail) grading
--      with LETTER GRADES A-E:
--          A = Exceptional     B = Outstanding
--          C = Satisfactory    D = Basic
--          E = Elementary
--      Every learner gets a grade in every subject (no outright "fail"); a learner
--      qualifies for a certificate with at least a D in one subject.
--
--   4. The curriculum also emphasises GENERIC SKILLS (e.g. Communication,
--      Cooperation/Self-Directed Learning, Critical Thinking & Problem Solving,
--      Creativity & Innovation) which many schools report on separately from
--      subject grades.
--
--   5. There is NO single nationally-mandated report card layout - NCDC sets the
--      assessment/grading framework, but each school designs its own report card.
--      This schema is therefore built to be CONFIGURABLE: grading bands, skill
--      rating scales, assessment types, themes and learning outcomes are all stored
--      in lookup tables that the school (or you, the developer) can edit to match
--      the exact report card your school uses. Always confirm current values
--      against the latest NCDC/UNEB guidance before going live.
--
--  HOW TO USE THIS FILE
--  --------------------
--   1. Import into MySQL/MariaDB via phpMyAdmin -> Import, or:
--        mysql -u root -p < ssms_uganda_database.sql
--   2. The database "ssms_uganda" will be created automatically.
--   3. Sample/seed data is included so the system is usable immediately - replace
--      it with your real school's data.
--
-- =====================================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS ssms_uganda CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ssms_uganda;

-- =====================================================================================
-- MODULE 1: SYSTEM USERS & ROLES
-- =====================================================================================

CREATE TABLE roles (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    role_name   VARCHAR(50)  NOT NULL UNIQUE,
    description VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    role_id       INT NOT NULL,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email         VARCHAR(100) NULL,
    phone         VARCHAR(20)  NULL,
    status        ENUM('active','inactive') DEFAULT 'active',
    last_login    DATETIME NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 2: ACADEMIC STRUCTURE (Years, Terms, Class Levels, Streams)
-- =====================================================================================

CREATE TABLE academic_years (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    year_name  VARCHAR(9) NOT NULL UNIQUE,   -- e.g. '2026'
    start_date DATE NOT NULL,
    end_date   DATE NOT NULL,
    is_current TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE terms (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    academic_year_id  INT NOT NULL,
    term_name         ENUM('Term 1','Term 2','Term 3') NOT NULL,
    start_date        DATE NOT NULL,
    end_date          DATE NOT NULL,
    next_term_begins  DATE NULL,
    is_current        TINYINT(1) DEFAULT 0,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
    UNIQUE KEY uq_year_term (academic_year_id, term_name)
) ENGINE=InnoDB;

CREATE TABLE class_levels (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    level_name    VARCHAR(20) NOT NULL UNIQUE,  -- S1, S2, S3, S4
    numeric_level TINYINT NOT NULL,             -- 1, 2, 3, 4
    description   VARCHAR(100) NULL
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 3: STAFF
-- =====================================================================================

CREATE TABLE staff (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NULL,
    staff_no     VARCHAR(20)  NOT NULL UNIQUE,
    first_name   VARCHAR(50)  NOT NULL,
    last_name    VARCHAR(50)  NOT NULL,
    gender       ENUM('Male','Female') NOT NULL,
    dob          DATE NULL,
    phone        VARCHAR(20)  NULL,
    email        VARCHAR(100) NULL,
    address      VARCHAR(255) NULL,
    designation  VARCHAR(60)  NULL,   -- e.g. Teacher, Head Teacher, Bursar, Librarian
    date_joined  DATE NULL,
    photo        VARCHAR(255) NULL,
    status       ENUM('active','on leave','left') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Streams depend on staff (class teacher), so created after staff
CREATE TABLE streams (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    class_level_id    INT NOT NULL,
    academic_year_id  INT NOT NULL,
    stream_name       VARCHAR(30) NOT NULL,  -- e.g. 'A', 'East', 'Faith'
    class_teacher_id  INT NULL,
    FOREIGN KEY (class_level_id) REFERENCES class_levels(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    FOREIGN KEY (class_teacher_id) REFERENCES staff(id) ON DELETE SET NULL,
    UNIQUE KEY uq_stream (class_level_id, academic_year_id, stream_name)
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 4: STUDENTS & GUARDIANS
-- =====================================================================================

CREATE TABLE students (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT NULL,
    admission_no   VARCHAR(20) NOT NULL UNIQUE,
    lin            VARCHAR(20) NULL COMMENT 'UNEB Learner Identification Number',
    first_name     VARCHAR(50) NOT NULL,
    last_name      VARCHAR(50) NOT NULL,
    gender         ENUM('Male','Female') NOT NULL,
    dob            DATE NULL,
    photo          VARCHAR(255) NULL,
    religion       VARCHAR(30) NULL,
    address        VARCHAR(255) NULL,
    admission_date DATE NOT NULL,
    status         ENUM('active','transferred','graduated','dropped') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE guardians (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    first_name   VARCHAR(50) NOT NULL,
    last_name    VARCHAR(50) NOT NULL,
    relationship VARCHAR(30) NULL,   -- Father, Mother, Guardian
    phone        VARCHAR(20) NULL,
    email        VARCHAR(100) NULL,
    occupation   VARCHAR(60) NULL,
    address      VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE student_guardians (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    student_id          INT NOT NULL,
    guardian_id         INT NOT NULL,
    is_primary_contact  TINYINT(1) DEFAULT 0,
    FOREIGN KEY (student_id)  REFERENCES students(id)  ON DELETE CASCADE,
    FOREIGN KEY (guardian_id) REFERENCES guardians(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE enrollments (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    student_id        INT NOT NULL,
    stream_id         INT NOT NULL,
    academic_year_id  INT NOT NULL,
    enrollment_date   DATE NOT NULL,
    status            ENUM('active','promoted','repeated','transferred','left') DEFAULT 'active',
    FOREIGN KEY (student_id)       REFERENCES students(id)       ON DELETE CASCADE,
    FOREIGN KEY (stream_id)        REFERENCES streams(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    UNIQUE KEY uq_enrollment (student_id, academic_year_id)
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 5: CURRICULUM (Subjects, Themes/Topics, Learning Outcomes, Generic Skills)
-- =====================================================================================

CREATE TABLE subjects (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(10) NOT NULL UNIQUE,
    subject_name VARCHAR(60) NOT NULL,
    category     ENUM('Core','Elective','Pre-Vocational') DEFAULT 'Core',
    description  VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE class_subjects (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    class_level_id  INT NOT NULL,
    subject_id      INT NOT NULL,
    is_compulsory   TINYINT(1) DEFAULT 1,
    FOREIGN KEY (class_level_id) REFERENCES class_levels(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id)     REFERENCES subjects(id)     ON DELETE CASCADE,
    UNIQUE KEY uq_class_subject (class_level_id, subject_id)
) ENGINE=InnoDB;

CREATE TABLE subject_teachers (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    subject_id        INT NOT NULL,
    stream_id         INT NOT NULL,
    staff_id          INT NOT NULL,
    academic_year_id  INT NOT NULL,
    FOREIGN KEY (subject_id)        REFERENCES subjects(id)        ON DELETE CASCADE,
    FOREIGN KEY (stream_id)         REFERENCES streams(id)         ON DELETE CASCADE,
    FOREIGN KEY (staff_id)          REFERENCES staff(id)           ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id)  REFERENCES academic_years(id),
    UNIQUE KEY uq_subject_stream (subject_id, stream_id, academic_year_id)
) ENGINE=InnoDB;

-- The "Theme"/"Topic" structure used in NCDC syllabuses
CREATE TABLE curriculum_themes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    subject_id      INT NOT NULL,
    class_level_id  INT NOT NULL,
    theme_code      VARCHAR(20)  NULL,
    theme_name      VARCHAR(150) NOT NULL,
    description     TEXT NULL,
    FOREIGN KEY (subject_id)     REFERENCES subjects(id)     ON DELETE CASCADE,
    FOREIGN KEY (class_level_id) REFERENCES class_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Competencies / Learning Outcomes under each theme
CREATE TABLE learning_outcomes (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    theme_id     INT NOT NULL,
    outcome_code VARCHAR(20) NULL,
    description  TEXT NOT NULL,
    FOREIGN KEY (theme_id) REFERENCES curriculum_themes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Cross-curricular "Generic Skills" emphasised by the CBC
CREATE TABLE generic_skills (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    skill_name  VARCHAR(60) NOT NULL UNIQUE,
    description VARCHAR(255) NULL
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 6: ASSESSMENT & GRADING
-- =====================================================================================

CREATE TABLE assessment_types (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    type_name          VARCHAR(60) NOT NULL,
    category           ENUM('Formative','Summative') NOT NULL,
    weight_percentage  DECIMAL(5,2) DEFAULT 0,
    description        VARCHAR(255) NULL
) ENGINE=InnoDB;

-- Subject grade bands (A-E). EDIT to match your school's grading policy.
CREATE TABLE grading_scale (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    grade       CHAR(1) NOT NULL UNIQUE,   -- A, B, C, D, E
    descriptor  VARCHAR(30) NOT NULL,
    min_score   DECIMAL(5,2) NOT NULL,
    max_score   DECIMAL(5,2) NOT NULL,
    remarks     VARCHAR(255) NULL
) ENGINE=InnoDB;

-- Rating scale for generic skills / competency levels. EDIT to match your
-- school's report card (number of levels and wording vary by school).
CREATE TABLE skill_rating_scale (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    rating_code  VARCHAR(5)  NOT NULL UNIQUE,
    rating_label VARCHAR(30) NOT NULL,
    rating_value TINYINT     NOT NULL,   -- numeric order, for sorting/averaging
    description  VARCHAR(255) NULL
) ENGINE=InnoDB;

-- Individual assessment entries (one row per test/project/CA item per learner)
CREATE TABLE assessment_records (
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    student_id           INT NOT NULL,
    subject_id           INT NOT NULL,
    theme_id             INT NULL,
    learning_outcome_id  INT NULL,
    assessment_type_id   INT NOT NULL,
    term_id              INT NOT NULL,
    score                DECIMAL(5,2) NULL,
    max_score            DECIMAL(5,2) DEFAULT 100,
    grade                CHAR(1) NULL,
    remarks              VARCHAR(255) NULL,
    date_recorded        DATE NOT NULL,
    recorded_by          INT NULL,
    FOREIGN KEY (student_id)          REFERENCES students(id)          ON DELETE CASCADE,
    FOREIGN KEY (subject_id)          REFERENCES subjects(id)          ON DELETE CASCADE,
    FOREIGN KEY (theme_id)            REFERENCES curriculum_themes(id) ON DELETE SET NULL,
    FOREIGN KEY (learning_outcome_id) REFERENCES learning_outcomes(id) ON DELETE SET NULL,
    FOREIGN KEY (assessment_type_id)  REFERENCES assessment_types(id),
    FOREIGN KEY (term_id)             REFERENCES terms(id)             ON DELETE CASCADE,
    FOREIGN KEY (recorded_by)         REFERENCES staff(id)             ON DELETE SET NULL
) ENGINE=InnoDB;

-- One rating per learner, per term, per generic skill
CREATE TABLE generic_skill_ratings (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    student_id       INT NOT NULL,
    term_id          INT NOT NULL,
    generic_skill_id INT NOT NULL,
    rating_id        INT NOT NULL,
    remarks          VARCHAR(255) NULL,
    recorded_by      INT NULL,
    FOREIGN KEY (student_id)       REFERENCES students(id)         ON DELETE CASCADE,
    FOREIGN KEY (term_id)          REFERENCES terms(id)            ON DELETE CASCADE,
    FOREIGN KEY (generic_skill_id) REFERENCES generic_skills(id)   ON DELETE CASCADE,
    FOREIGN KEY (rating_id)        REFERENCES skill_rating_scale(id),
    FOREIGN KEY (recorded_by)      REFERENCES staff(id)            ON DELETE SET NULL,
    UNIQUE KEY uq_skill_rating (student_id, term_id, generic_skill_id)
) ENGINE=InnoDB;

-- Aggregated final result per learner, per subject, per term - this is what
-- feeds the report card.
CREATE TABLE subject_term_results (
    id                       INT AUTO_INCREMENT PRIMARY KEY,
    student_id               INT NOT NULL,
    subject_id               INT NOT NULL,
    term_id                  INT NOT NULL,
    ca_score                 DECIMAL(5,2) NULL COMMENT 'Aggregated formative/continuous assessment score',
    eot_score                DECIMAL(5,2) NULL COMMENT 'End of term / end of cycle summative score',
    final_score              DECIMAL(5,2) NULL,
    final_grade              CHAR(1) NULL,
    subject_teacher_comment  VARCHAR(255) NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id)    REFERENCES terms(id)    ON DELETE CASCADE,
    UNIQUE KEY uq_subject_term (student_id, subject_id, term_id)
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 7: REPORT CARDS
-- =====================================================================================

CREATE TABLE report_cards (
    id                     INT AUTO_INCREMENT PRIMARY KEY,
    student_id             INT NOT NULL,
    term_id                INT NOT NULL,
    stream_id              INT NOT NULL,
    days_present           INT DEFAULT 0,
    days_absent            INT DEFAULT 0,
    class_teacher_comment  TEXT NULL,
    head_teacher_comment   TEXT NULL,
    next_term_begins       DATE NULL,
    date_issued            DATE NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id)    REFERENCES terms(id)    ON DELETE CASCADE,
    FOREIGN KEY (stream_id)  REFERENCES streams(id),
    UNIQUE KEY uq_report (student_id, term_id)
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 8: ATTENDANCE
-- =====================================================================================

CREATE TABLE attendance (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    term_id         INT NOT NULL,
    attendance_date DATE NOT NULL,
    status          ENUM('Present','Absent','Late','Excused') NOT NULL DEFAULT 'Present',
    recorded_by     INT NULL,
    FOREIGN KEY (student_id)  REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id)     REFERENCES terms(id)    ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES staff(id)    ON DELETE SET NULL,
    UNIQUE KEY uq_attendance (student_id, attendance_date)
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 9: TIMETABLE
-- =====================================================================================

CREATE TABLE timetable (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    stream_id         INT NOT NULL,
    subject_id        INT NOT NULL,
    staff_id          INT NOT NULL,
    academic_year_id  INT NOT NULL,
    day_of_week       ENUM('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
    period_no         TINYINT NOT NULL,
    start_time        TIME NOT NULL,
    end_time          TIME NOT NULL,
    FOREIGN KEY (stream_id)        REFERENCES streams(id)        ON DELETE CASCADE,
    FOREIGN KEY (subject_id)       REFERENCES subjects(id)       ON DELETE CASCADE,
    FOREIGN KEY (staff_id)         REFERENCES staff(id)          ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 10: FINANCE (Fees, Invoices, Payments)
-- =====================================================================================

CREATE TABLE fee_structures (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    class_level_id  INT NOT NULL,
    term_id         INT NOT NULL,
    fee_category    VARCHAR(50) NOT NULL,  -- Tuition, Lunch, Transport, Development...
    amount          DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (class_level_id) REFERENCES class_levels(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id)        REFERENCES terms(id)        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE invoices (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    student_id    INT NOT NULL,
    term_id       INT NOT NULL,
    total_amount  DECIMAL(12,2) NOT NULL,
    amount_paid   DECIMAL(12,2) DEFAULT 0,
    balance       DECIMAL(12,2) GENERATED ALWAYS AS (total_amount - amount_paid) VIRTUAL,
    issue_date    DATE NOT NULL,
    due_date      DATE NULL,
    status        ENUM('unpaid','partial','paid') DEFAULT 'unpaid',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id)    REFERENCES terms(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE payments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id      INT NOT NULL,
    student_id      INT NOT NULL,
    amount          DECIMAL(12,2) NOT NULL,
    payment_method  ENUM('Cash','Mobile Money','Bank Transfer','Cheque') DEFAULT 'Cash',
    reference_no    VARCHAR(50) NULL,
    payment_date    DATE NOT NULL,
    received_by     INT NULL,
    FOREIGN KEY (invoice_id)  REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id)  REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES staff(id)    ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 11: DISCIPLINE
-- =====================================================================================

CREATE TABLE discipline_records (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    student_id    INT NOT NULL,
    term_id       INT NOT NULL,
    incident_date DATE NOT NULL,
    description   TEXT NOT NULL,
    action_taken  VARCHAR(255) NULL,
    recorded_by   INT NULL,
    FOREIGN KEY (student_id)  REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (term_id)     REFERENCES terms(id)    ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES staff(id)    ON DELETE SET NULL
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 12: LIBRARY
-- =====================================================================================

CREATE TABLE library_books (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    title             VARCHAR(150) NOT NULL,
    author            VARCHAR(100) NULL,
    isbn              VARCHAR(30)  NULL,
    category          VARCHAR(50)  NULL,
    total_copies      INT DEFAULT 1,
    available_copies  INT DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE book_loans (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    book_id      INT NOT NULL,
    student_id   INT NULL,
    staff_id     INT NULL,
    borrow_date  DATE NOT NULL,
    due_date     DATE NOT NULL,
    return_date  DATE NULL,
    status       ENUM('borrowed','returned','overdue') DEFAULT 'borrowed',
    FOREIGN KEY (book_id)    REFERENCES library_books(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id)      ON DELETE CASCADE,
    FOREIGN KEY (staff_id)   REFERENCES staff(id)         ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================================
-- MODULE 13: COMMUNICATION
-- =====================================================================================

CREATE TABLE announcements (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    message     TEXT NOT NULL,
    target_role VARCHAR(50) DEFAULT 'all',
    created_by  INT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================================
-- SEED / SAMPLE DATA
-- =====================================================================================

-- Roles -------------------------------------------------------------------
INSERT INTO roles (role_name, description) VALUES
('Administrator',     'Full system access'),
('Head Teacher',      'School head, approves report cards'),
('Director of Studies','Oversees academics and curriculum'),
('Teacher',           'Subject/class teacher'),
('Bursar',            'Manages fees and finance'),
('Librarian',         'Manages library'),
('Parent',            'Views child progress and fees'),
('Student',           'Views own results and timetable');

-- Default admin user. CHANGE THIS PASSWORD before going live.
-- Username: admin   Password: ChangeMe123!
-- (hash generated as a bcrypt hash - verifies correctly with PHP's
--  password_verify($pwd, $hash))
INSERT INTO users (role_id, username, password_hash, email, status) VALUES
(1, 'admin', '$2b$12$VRDcu6KWXgalqWV6ZIyLJO1i0nng6BBRif7Cfjc2CBFopwFOBDwGe', 'admin@school.ug', 'active');

-- Academic structure --------------------------------------------------------
INSERT INTO academic_years (year_name, start_date, end_date, is_current) VALUES
('2026', '2026-02-03', '2026-12-04', 1);

INSERT INTO terms (academic_year_id, term_name, start_date, end_date, next_term_begins, is_current) VALUES
(1, 'Term 1', '2026-02-03', '2026-05-08', '2026-05-25', 1),
(1, 'Term 2', '2026-05-25', '2026-08-21', '2026-09-07', 0),
(1, 'Term 3', '2026-09-07', '2026-12-04', NULL, 0);

INSERT INTO class_levels (level_name, numeric_level, description) VALUES
('S1', 1, 'Senior One'),
('S2', 2, 'Senior Two'),
('S3', 3, 'Senior Three'),
('S4', 4, 'Senior Four');

-- Sample teaching staff -------------------------------------------------------
INSERT INTO staff (staff_no, first_name, last_name, gender, designation, date_joined, status) VALUES
('STF-0001', 'Brian', 'Mutebi', 'Male', 'Director of Studies', '2022-01-10', 'active'),
('STF-0002', 'Grace', 'Namutebi', 'Female', 'Teacher', '2023-02-01', 'active'),
('STF-0003', 'Samuel', 'Okello', 'Male', 'Teacher', '2021-08-15', 'active');

-- Streams for 2026 -------------------------------------------------------------
INSERT INTO streams (class_level_id, academic_year_id, stream_name, class_teacher_id) VALUES
(1, 1, 'A', 2),  -- S1 A, class teacher Grace Namutebi
(1, 1, 'B', 3),  -- S1 B, class teacher Samuel Okello
(2, 1, 'A', NULL),
(3, 1, 'A', NULL),
(4, 1, 'A', NULL);

-- Subjects (sample core subjects - extend per your school's combination) ------
INSERT INTO subjects (subject_code, subject_name, category) VALUES
('ENG', 'English Language', 'Core'),
('MTC', 'Mathematics', 'Core'),
('BIO', 'Biology', 'Core'),
('CHE', 'Chemistry', 'Core'),
('PHY', 'Physics', 'Core'),
('GEO', 'Geography', 'Core'),
('HIS', 'History and Political Education', 'Core'),
('CRE', 'Christian Religious Education', 'Elective'),
('ICT', 'Information & Communications Technology', 'Core'),
('AGR', 'Agriculture', 'Elective'),
('ENT', 'Entrepreneurship Education', 'Core'),
('PE',  'Physical Education', 'Core'),
('KIS', 'Kiswahili', 'Core');

-- Map subjects to S1 (example - repeat/adjust for S2-S4)
INSERT INTO class_subjects (class_level_id, subject_id, is_compulsory)
SELECT 1, id, 1 FROM subjects WHERE category = 'Core';

-- Sample curriculum theme + learning outcomes for S1 Mathematics --------------
INSERT INTO curriculum_themes (subject_id, class_level_id, theme_code, theme_name, description) VALUES
(2, 1, 'MTC-S1-T1', 'Numerical Concepts 1', 'Numbers, place value, operations on integers, fractions and ratios');

INSERT INTO learning_outcomes (theme_id, outcome_code, description) VALUES
(1, 'MTC-S1-T1-LO1', 'The learner performs operations on integers, fractions and decimals correctly'),
(1, 'MTC-S1-T1-LO2', 'The learner applies ratios and proportions to solve everyday problems');

-- Generic skills (commonly referenced under the CBC - verify wording against
-- the latest NCDC Curriculum Framework for your official report template)
INSERT INTO generic_skills (skill_name, description) VALUES
('Communication', 'Ability to express ideas clearly in speech and writing, and to listen and read with understanding'),
('Cooperation and Self-Directed Learning', 'Ability to work effectively with others and to take responsibility for one\'s own learning'),
('Critical Thinking and Problem Solving', 'Ability to analyse situations and apply knowledge to solve problems'),
('Creativity and Innovation', 'Ability to generate and apply new ideas in practical ways');

-- Assessment types --------------------------------------------------------------
INSERT INTO assessment_types (type_name, category, weight_percentage, description) VALUES
('Continuous Assessment Test', 'Formative', 0, 'Topic/in-class tests done during the term'),
('Project Work / Activity of Integration', 'Formative', 0, 'Practical or project-based formative task'),
('End of Topic Assessment', 'Formative', 0, 'Assessment given at the end of a topic'),
('End of Term Exam', 'Summative', 0, 'Summative exam at the end of the term'),
('End of Cycle Exam (UCE)', 'Summative', 80, 'National examination at the end of S4 - contributes 80% of final UCE subject grade');

-- Subject grading scale (A-E). EDIT bands to match your school's policy --------
INSERT INTO grading_scale (grade, descriptor, min_score, max_score, remarks) VALUES
('A', 'Exceptional',  80.00, 100.00, 'Advanced competency - applies knowledge innovatively'),
('B', 'Outstanding',  70.00, 79.99,  'High competency - applies skills effectively'),
('C', 'Satisfactory', 55.00, 69.99,  'Adequate knowledge and skill application'),
('D', 'Basic',        40.00, 54.99,  'Minimum competency - limited practical application'),
('E', 'Elementary',    0.00, 39.99,  'Beginning level - difficulty applying knowledge');

-- Generic skill / competency rating scale. EDIT to match your report card -------
INSERT INTO skill_rating_scale (rating_code, rating_label, rating_value, description) VALUES
('BEG', 'Beginning',  1, 'Learner is starting to develop this skill, needs close support'),
('DEV', 'Developing', 2, 'Learner shows the skill with some support'),
('PRO', 'Proficient', 3, 'Learner applies the skill independently'),
('MAS', 'Mastery',    4, 'Learner applies the skill consistently and helps others');

-- Sample students --------------------------------------------------------------
INSERT INTO students (admission_no, first_name, last_name, gender, dob, admission_date, status) VALUES
('S26-0001', 'Faith', 'Achieng', 'Female', '2012-03-14', '2026-02-03', 'active'),
('S26-0002', 'Daniel', 'Wasswa', 'Male',   '2012-07-22', '2026-02-03', 'active');

INSERT INTO enrollments (student_id, stream_id, academic_year_id, enrollment_date, status) VALUES
(1, 1, 1, '2026-02-03', 'active'),
(2, 1, 1, '2026-02-03', 'active');

-- Sample guardians ---------------------------------------------------------------
INSERT INTO guardians (first_name, last_name, relationship, phone, occupation) VALUES
('Moses', 'Achieng', 'Father', '+256700000001', 'Trader'),
('Susan', 'Wasswa', 'Mother', '+256700000002', 'Teacher');

INSERT INTO student_guardians (student_id, guardian_id, is_primary_contact) VALUES
(1, 1, 1),
(2, 2, 1);

-- =====================================================================================
-- SAMPLE TERM RESULTS FOR THE "NEW CURRICULUM" REPORT CARD (Faith Achieng, Term 1)
-- This gives interns real data to render modules/reports/student_report.php against.
-- ca_score is out of 20 (Continuous Assessment, 20% weight) and eot_score is out of
-- 80 (End of Term/Cycle exam, 80% weight) - final_score = ca_score + eot_score.
-- =====================================================================================

INSERT INTO subject_term_results
    (student_id, subject_id, term_id, ca_score, eot_score, final_score, final_grade, subject_teacher_comment) VALUES
(1, 1,  1, 16.00, 60.00, 76.00, 'B', 'Writes clearly and confidently. Work on punctuation in essays.'),
(1, 2,  1, 18.00, 68.00, 86.00, 'A', 'Excellent grasp of numerical concepts. Keep it up.'),
(1, 3,  1, 14.00, 50.00, 64.00, 'C', 'Understands basic concepts; needs more practice with diagrams.'),
(1, 4,  1, 15.00, 58.00, 73.00, 'B', 'Good understanding of practical work.'),
(1, 5,  1, 13.00, 45.00, 58.00, 'C', 'Needs to revise topics on forces and motion.'),
(1, 6,  1, 17.00, 62.00, 79.00, 'B', 'Good map work skills.'),
(1, 7,  1, 12.00, 44.00, 56.00, 'C', 'Should read more widely around historical themes.'),
(1, 9,  1, 19.00, 70.00, 89.00, 'A', 'Confident with computer practicals and typing skills.'),
(1, 11, 1, 16.00, 55.00, 71.00, 'B', 'Shows good business sense in class activities.'),
(1, 12, 1, 18.00, 64.00, 82.00, 'A', 'Active and disciplined during games.'),
(1, 13, 1, 14.00, 48.00, 62.00, 'C', 'Participates well in conversation practice.');

INSERT INTO generic_skill_ratings (student_id, term_id, generic_skill_id, rating_id, remarks) VALUES
(1, 1, 1, 3, 'Speaks up confidently during class discussions'),
(1, 1, 2, 4, 'Works very well in groups and supports peers'),
(1, 1, 3, 2, 'Still developing confidence in solving novel problems'),
(1, 1, 4, 3, 'Comes up with creative ideas for class projects');

INSERT INTO report_cards
    (student_id, term_id, stream_id, days_present, days_absent, class_teacher_comment, head_teacher_comment, next_term_begins, date_issued) VALUES
(1, 1, 1, 58, 2,
 'Faith is a hardworking and well-behaved learner who participates actively in class. She should pay more attention during Physics practicals.',
 'A promising start to the year. Keep up the good work, Faith.',
 '2026-05-25', '2026-05-08');

-- =====================================================================================
-- USEFUL VIEWS FOR REPORTING
-- =====================================================================================

-- Quick class list with stream and academic year details
CREATE OR REPLACE VIEW v_class_list AS
SELECT
    s.id            AS student_id,
    s.admission_no,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    s.gender,
    cl.level_name,
    st.stream_name,
    ay.year_name
FROM students s
JOIN enrollments e ON e.student_id = s.id
JOIN streams st     ON st.id = e.stream_id
JOIN class_levels cl ON cl.id = st.class_level_id
JOIN academic_years ay ON ay.id = e.academic_year_id;

-- Subject results + computed grade descriptor, per term - the core query
-- behind the "New Curriculum" report card
CREATE OR REPLACE VIEW v_subject_term_report AS
SELECT
    str.student_id,
    CONCAT(s.first_name, ' ', s.last_name) AS student_name,
    sub.subject_name,
    t.term_name,
    ay.year_name,
    str.ca_score,
    str.eot_score,
    str.final_score,
    str.final_grade,
    g.descriptor AS grade_descriptor,
    str.subject_teacher_comment
FROM subject_term_results str
JOIN students s        ON s.id = str.student_id
JOIN subjects sub       ON sub.id = str.subject_id
JOIN terms t            ON t.id = str.term_id
JOIN academic_years ay  ON ay.id = t.academic_year_id
LEFT JOIN grading_scale g ON g.grade = str.final_grade;

-- =====================================================================================
-- END OF SCHEMA
-- =====================================================================================
