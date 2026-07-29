export interface Role {
  id: number;
  role_name: string;
  description?: string;
}

export type ModuleSlug =
  | 'dashboard'
  | 'academic'
  | 'staff'
  | 'students'
  | 'curriculum'
  | 'assessment'
  | 'reports'
  | 'attendance'
  | 'timetable'
  | 'finance'
  | 'discipline'
  | 'library'
  | 'announcements';

export interface User {
  id: number;
  role_id: number;
  role_slug?: string;
  username: string;
  name: string;
  email: string;
  phone?: string;
  status: 'active' | 'inactive';
  created_at: string;
  updated_at: string;
  role?: Role;
  is_school_admin?: boolean;
  modules?: ModuleSlug[];
}

export interface AcademicYear {
  id: number;
  year_name: string;
  start_date: string;
  end_date: string;
  is_current: boolean;
  created_at: string;
  updated_at: string;
}

export interface Term {
  id: number;
  academic_year_id: number;
  term_name: string;
  start_date: string;
  end_date: string;
  next_term_begins?: string;
  is_current: boolean;
  created_at: string;
  updated_at: string;
}

export interface ClassLevel {
  id: number;
  level_name: string;
  numeric_level: number;
  description?: string;
}

export interface Stream {
  id: number;
  class_level_id: number;
  academic_year_id: number;
  stream_name: string;
  class_teacher_id?: number;
  class_level?: ClassLevel;
}

export interface LinkedUser {
  id: number;
  email: string;
  username: string;
  name: string;
  status: string;
  modules: ModuleSlug[];
  role?: { id: number; role_name: string } | null;
}

export interface Staff {
  id: number;
  user_id?: number;
  staff_no: string;
  first_name: string;
  last_name: string;
  gender: 'Male' | 'Female';
  dob?: string;
  phone?: string;
  email?: string;
  designation?: string;
  status: 'active' | 'on leave' | 'left';
  user?: LinkedUser | null;
  created_at?: string;
  updated_at?: string;
}

export interface Student {
  id: number;
  user_id?: number;
  admission_no: string;
  lin?: string;
  first_name: string;
  last_name: string;
  gender: 'Male' | 'Female';
  dob?: string;
  admission_date: string;
  status: 'active' | 'transferred' | 'graduated' | 'dropped';
  created_at: string;
  updated_at: string;
}

export interface Guardian {
  id: number;
  first_name: string;
  last_name: string;
  relationship?: string;
  phone?: string;
  email?: string;
  occupation?: string;
}

export interface Enrollment {
  id: number;
  student_id: number;
  stream_id: number;
  academic_year_id: number;
  enrollment_date: string;
  status: 'active' | 'promoted' | 'repeated' | 'transferred' | 'left';
}

export interface Subject {
  id: number;
  subject_code: string;
  subject_name: string;
  category: 'Core' | 'Elective' | 'Pre-Vocational';
}

export interface ClassSubject {
  id: number;
  class_level_id: number;
  subject_id: number;
  is_compulsory: boolean;
}

export interface SubjectTeacher {
  id: number;
  subject_id: number;
  stream_id: number;
  staff_id: number;
  academic_year_id: number;
}

export interface CurriculumTheme {
  id: number;
  subject_id: number;
  class_level_id: number;
  theme_code?: string;
  theme_name: string;
  description?: string;
}

export interface LearningOutcome {
  id: number;
  theme_id: number;
  outcome_code?: string;
  description: string;
}

export interface GenericSkill {
  id: number;
  skill_name: string;
  description?: string;
}

export interface AssessmentType {
  id: number;
  type_name: string;
  category: 'Formative' | 'Summative';
  weight_percentage: number;
}

export interface GradingScale {
  id: number;
  grade: string;
  descriptor: string;
  min_score: number;
  max_score: number;
}

export interface SkillRatingScale {
  id: number;
  rating_code: string;
  rating_label: string;
  rating_value: number;
}

export interface AssessmentRecord {
  id: number;
  student_id: number;
  subject_id: number;
  theme_id?: number;
  learning_outcome_id?: number;
  assessment_type_id: number;
  term_id: number;
  score?: number;
  max_score: number;
  date_recorded: string;
}

export interface GenericSkillRating {
  id: number;
  student_id: number;
  term_id: number;
  generic_skill_id: number;
  rating_id: number;
  remarks?: string;
}

export interface SubjectTermResult {
  id: number;
  student_id: number;
  subject_id: number;
  term_id: number;
  ca_score?: number;
  eot_score?: number;
  final_score?: number;
  final_grade?: string;
  subject_teacher_comment?: string;
}

export interface ReportCard {
  id: number;
  student_id: number;
  term_id: number;
  stream_id: number;
  days_present: number;
  days_absent: number;
  class_teacher_comment?: string;
  head_teacher_comment?: string;
  next_term_begins?: string;
  date_issued?: string;
}

export interface Attendance {
  id: number;
  student_id: number;
  term_id: number;
  attendance_date: string;
  status: 'Present' | 'Absent' | 'Late' | 'Excused';
  recorded_by?: number;
}

export interface Timetable {
  id: number;
  stream_id: number;
  subject_id: number;
  staff_id: number;
  academic_year_id: number;
  day_of_week: string;
  period_no: number;
  start_time: string;
  end_time: string;
}

export interface FeeStructure {
  id: number;
  class_level_id: number;
  term_id: number;
  fee_category: string;
  amount: number;
}

export interface Invoice {
  id: number;
  student_id: number;
  term_id: number;
  total_amount: number;
  amount_paid: number;
  balance: number;
  issue_date: string;
  status: 'unpaid' | 'partial' | 'paid';
}

export interface Payment {
  id: number;
  invoice_id: number;
  student_id: number;
  amount: number;
  payment_method: string;
  reference_no?: string;
  payment_date: string;
}

export interface DisciplineRecord {
  id: number;
  student_id: number;
  term_id: number;
  incident_date: string;
  description: string;
  action_taken?: string;
}

export interface LibraryBook {
  id: number;
  title: string;
  author?: string;
  isbn?: string;
  category?: string;
  total_copies: number;
  available_copies: number;
}

export interface BookLoan {
  id: number;
  book_id: number;
  student_id?: number;
  staff_id?: number;
  borrow_date: string;
  due_date: string;
  return_date?: string;
  status: 'borrowed' | 'returned' | 'overdue';
}

export interface Announcement {
  id: number;
  title: string;
  message: string;
  target_role: string;
  created_by?: number;
  created_at: string;
}
