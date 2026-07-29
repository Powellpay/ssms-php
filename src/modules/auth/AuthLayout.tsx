import type { PropsWithChildren, ReactNode } from 'react';
import { Link } from 'react-router-dom';
import { ROUTES } from '../../app/routes/constants';
import { Home, Sparkles } from 'lucide-react';
import LogoImage from '../../shared/components/LogoImage';

interface AuthLayoutProps {
  title: string;
  subtitle?: string;
  children: ReactNode;
}

export default function AuthLayout({ title, subtitle, children }: PropsWithChildren<AuthLayoutProps>) {
  return (
    <div className="min-h-screen flex bg-gray-50">
      {/* Left hero panel */}
      <div className="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-primary-dark via-primary to-primary/80">
        <div className="relative z-10 flex flex-col justify-between p-10 xl:p-12 w-full">
          <Link to={ROUTES.HOME} className="inline-flex items-center gap-2.5 w-fit">
            <LogoImage size="sm" className="brightness-0 invert" />
            <span className="text-white text-xl font-bold tracking-tight">SSMS</span>
          </Link>
          <div className="max-w-md space-y-5">
            <div>
              <p className="text-white/80 text-xs font-semibold uppercase tracking-[0.2em] mb-3">
                School Management System
              </p>
              <h1 className="text-3xl xl:text-4xl font-bold text-white mb-3">
                Manage Your School. All in One Place.
              </h1>
              <p className="text-green-100/90 text-base leading-relaxed">
                From student admissions and curriculum to report cards and finance — 
                aligned to Uganda's Competency-Based Curriculum.
              </p>
            </div>
            <p className="text-green-200/80 text-sm flex items-center gap-1">
              <Sparkles className="w-3.5 h-3.5" /> Uganda NCDC Competency-Based Curriculum
            </p>
          </div>
          <div className="text-center">
            <p className="text-white/70 text-xs">&copy; {new Date().getFullYear()} SSMS. All rights reserved.</p>
          </div>
        </div>
      </div>

      {/* Right panel */}
      <div className="flex-1 flex flex-col min-h-screen">
        <header className="flex items-center gap-3 px-5 sm:px-6 py-4 border-b border-border bg-white/95 backdrop-blur-sm sticky top-0 z-20">
          <Link to={ROUTES.HOME} className="inline-flex items-center gap-2.5">
            <LogoImage size="sm" />
            <span className="text-lg font-bold text-primary">SSMS</span>
          </Link>
          <div className="ml-auto">
            <Link
              to={ROUTES.HOME}
              className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-border text-sm font-medium text-gray-600 hover:bg-gray-50"
            >
              <Home className="w-4 h-4" />
              <span className="hidden sm:inline">Home</span>
            </Link>
          </div>
        </header>
        <main className="flex-1 flex items-center justify-center px-5 py-8 sm:px-8 sm:py-10">
          <div className="w-full max-w-md">
            <div className="bg-white rounded-2xl border border-border/80 shadow-sm p-6 sm:p-8 lg:border-0 lg:shadow-none lg:bg-transparent lg:p-0">
              <div className="text-center mb-7">
                <h2 className="text-2xl sm:text-[1.65rem] font-bold text-gray-900 mb-1.5">{title}</h2>
                {subtitle && <p className="text-gray-500 text-sm sm:text-base leading-relaxed">{subtitle}</p>}
              </div>
              {children}
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}
