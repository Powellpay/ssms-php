export const ROUTES = {
  HOME: '/',
  AUTH: {
    LOGIN: '/login',
    REGISTER: '/register',
    FORGOT_PASSWORD: '/forgot-password',
    VERIFY_EMAIL: '/verify-email',
    RESET_PASSWORD: '/reset-password',
  },
  DASHBOARD: '/dashboard',
  ACADEMIC: {
    YEARS: '/academic/years',
    TERMS: '/academic/terms',
    CLASSES: '/academic/classes',
    STREAMS: '/academic/streams',
  },
  STAFF: {
    LIST: '/staff',
    ADD: '/staff/add',
    VIEW: '/staff/:id',
  },
  STUDENTS: {
    LIST: '/students',
    ADD: '/students/add',
    VIEW: '/students/:id',
  },
  CURRICULUM: {
    SUBJECTS: '/curriculum/subjects',
    THEMES: '/curriculum/themes',
    OUTCOMES: '/curriculum/outcomes',
    SKILLS: '/curriculum/skills',
  },
  ASSESSMENT: {
    TYPES: '/assessment/types',
    GRADING: '/assessment/grading',
    RECORDS: '/assessment/records',
    RESULTS: '/assessment/results',
  },
  REPORTS: {
    LIST: '/reports',
    VIEW: '/reports/:id',
  },
  ATTENDANCE: {
    REGISTER: '/attendance',
    SUMMARY: '/attendance/summary',
  },
  TIMETABLE: {
    VIEW: '/timetable',
  },
  FINANCE: {
    FEES: '/finance/fees',
    INVOICES: '/finance/invoices',
    PAYMENTS: '/finance/payments',
  },
  DISCIPLINE: {
    LIST: '/discipline',
  },
  LIBRARY: {
    BOOKS: '/library/books',
    LOANS: '/library/loans',
  },
  ANNOUNCEMENTS: {
    LIST: '/announcements',
  },
};
