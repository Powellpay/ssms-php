import { useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { ROUTES } from '../../app/routes/constants';
import {
  GraduationCap, BookOpen, Users, ClipboardCheck, BarChart3,
  CalendarCheck, Timer, Wallet, ShieldCheck, Library, Megaphone,
  UserPlus, LogIn, Sparkles, CheckCircle, type LucideIcon
} from 'lucide-react';

interface Benefit {
  icon: LucideIcon;
  title: string;
  description: string;
  color: string;
}

const benefits: Benefit[] = [
  { icon: Users, title: 'Students & Guardians', description: 'Admissions, bio-data, guardian contacts, yearly enrollment and promotion tracking.', color: 'from-green-500 to-green-600' },
  { icon: BookOpen, title: 'Academic Structure', description: 'Academic years, terms, class levels (S1–S4) and stream management with class teacher assignment.', color: 'from-blue-500 to-blue-600' },
  { icon: ClipboardCheck, title: 'Curriculum & Assessment', description: 'NCDC themes, learning outcomes, CA and exam scoring with automatic A–E grade computation.', color: 'from-purple-500 to-purple-600' },
  { icon: BarChart3, title: 'Report Cards', description: 'New Curriculum report cards with subject results, generic skills, attendance, and teacher comments.', color: 'from-amber-500 to-amber-600' },
  { icon: CalendarCheck, title: 'Attendance', description: 'Daily attendance register with term summaries feeding directly into the report card.', color: 'from-cyan-500 to-cyan-600' },
  { icon: Timer, title: 'Timetable', description: 'Weekly timetable builder per stream with period slots, subject, and teacher allocation.', color: 'from-indigo-500 to-indigo-600' },
  { icon: Wallet, title: 'Finance', description: 'Fee structures, invoice generation, payment tracking with cash and mobile money support.', color: 'from-emerald-500 to-emerald-600' },
  { icon: ShieldCheck, title: 'Discipline', description: 'Conduct and incident logging per learner, visible on student profiles.', color: 'from-red-500 to-red-600' },
  { icon: Library, title: 'Library', description: 'Book catalogue, borrowing and return tracking with overdue loan flagging.', color: 'from-teal-500 to-teal-600' },
  { icon: Megaphone, title: 'Announcements', description: 'School-wide or role-targeted notices surfaced on the dashboard.', color: 'from-pink-500 to-pink-600' },
  { icon: GraduationCap, title: 'Staff Management', description: 'Teacher and non-teaching staff records linked to user accounts and class assignments.', color: 'from-orange-500 to-orange-600' },
  { icon: Users, title: 'Roles & Permissions', description: 'Role-based access for Admin, Head Teacher, DOS, Teacher, Bursar, Librarian, Parent, and Student.', color: 'from-violet-500 to-violet-600' },
];

export default function LandingPage() {
  const navigate = useNavigate();

  return (
    <>
      {/* Hero */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-12 lg:pt-8 lg:pb-16">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
          <div className="text-center lg:text-left space-y-8">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              className="inline-flex items-center gap-2 px-4 py-2 bg-primary rounded-full shadow-lg shadow-primary/20"
            >
              <Sparkles className="w-4 h-4 text-white" />
              <span className="text-sm font-semibold text-white">Aligned to Uganda's NCDC Curriculum</span>
            </motion.div>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.05 }}
              className="text-sm font-semibold text-primary uppercase tracking-widest"
            >
              School Management System
            </motion.p>
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
              className="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight"
            >
              Manage Your School.{' '}
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-primary to-primary-dark">
                All in One Place.
              </span>
            </motion.h1>
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.15 }}
              className="text-lg sm:text-xl text-gray-600 max-w-xl mx-auto lg:mx-0 leading-relaxed"
            >
              From student admissions and curriculum management to report cards and finance — 
              a complete system aligned to Uganda's Competency-Based Curriculum.
            </motion.p>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
              className="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start"
            >
              <button
                type="button"
                onClick={() => navigate(ROUTES.AUTH.REGISTER)}
                className="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3.5 text-base font-medium rounded-lg bg-primary text-white hover:bg-primary-dark transition-all duration-200 shadow-sm hover:shadow cursor-pointer"
              >
                <UserPlus className="w-5 h-5 mr-2" />
                Register Your School
              </button>
              <button
                type="button"
                onClick={() => navigate(ROUTES.AUTH.LOGIN)}
                className="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3.5 text-base font-medium rounded-lg border-2 border-primary text-primary hover:bg-primary-light transition-all duration-200 cursor-pointer"
              >
                <LogIn className="w-5 h-5 mr-2" />
                Sign In
              </button>
            </motion.div>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              transition={{ delay: 0.3 }}
              className="flex items-center gap-4 justify-center lg:justify-start text-sm text-muted"
            >
              <span className="flex items-center gap-1"><CheckCircle className="w-4 h-4 text-primary" /> Free to use</span>
              <span className="flex items-center gap-1"><CheckCircle className="w-4 h-4 text-primary" /> Offline-ready</span>
              <span className="flex items-center gap-1"><CheckCircle className="w-4 h-4 text-primary" /> CBC Aligned</span>
            </motion.div>
          </div>

          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: 0.2 }}
            className="hidden lg:flex items-center justify-center"
          >
            <div className="rounded-2xl bg-gradient-to-br from-primary/5 to-primary-light border border-border p-8 shadow-xl w-full">
              <img
                src="https://placehold.co/600x400/1f6f43/ffffff?text=SSMS+Dashboard"
                alt="SSMS Dashboard Preview"
                className="w-full h-auto rounded-lg shadow-md"
              />
            </div>
          </motion.div>
        </div>
      </section>

      {/* Features */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-center mb-12"
        >
          <h2 className="text-3xl font-bold text-gray-900 mb-3">Complete School Management</h2>
          <p className="text-muted max-w-2xl mx-auto">
            Thirteen integrated modules covering every aspect of running a school — 
            from admissions to report cards.
          </p>
        </motion.div>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {benefits.map((benefit, i) => (
            <motion.div
              key={benefit.title}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.03 }}
              className="group rounded-xl border border-border bg-white p-6 hover:shadow-lg hover:border-primary/30 transition-all duration-300"
            >
              <div className={`inline-flex p-3 rounded-xl bg-gradient-to-br ${benefit.color} shadow-sm mb-4`}>
                <benefit.icon className="w-5 h-5 text-white" />
              </div>
              <h3 className="text-lg font-bold text-gray-900 mb-2">{benefit.title}</h3>
              <p className="text-sm text-muted leading-relaxed">{benefit.description}</p>
            </motion.div>
          ))}
        </div>
      </section>





      {/* CTA */}
      <section className="relative py-16">
        <div className="absolute inset-0 bg-primary-light/50 border-t border-b border-border" />
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="max-w-4xl mx-auto px-4 text-center space-y-6 relative"
        >
          <h2 className="text-3xl sm:text-4xl font-bold text-gray-900">
            Ready to Transform Your School?
          </h2>
          <p className="text-gray-600 text-lg max-w-2xl mx-auto">
            Join schools already using SSMS. Free to start, aligned to the Uganda NCDC curriculum.
          </p>
          <div className="flex items-center justify-center gap-3">
            <button
              type="button"
              onClick={() => navigate(ROUTES.AUTH.REGISTER)}
              className="inline-flex items-center px-8 py-3.5 rounded-lg bg-primary text-white hover:bg-primary-dark transition-colors font-medium cursor-pointer"
            >
              <UserPlus className="w-5 h-5 mr-2" />
              Register Your School
            </button>
            <button
              type="button"
              onClick={() => navigate(ROUTES.AUTH.LOGIN)}
              className="inline-flex items-center px-8 py-3.5 rounded-lg border-2 border-primary text-primary hover:bg-white transition-colors font-medium cursor-pointer"
            >
              <LogIn className="w-5 h-5 mr-2" />
              Sign In
            </button>
          </div>
        </motion.div>
      </section>
    </>
  );
}
