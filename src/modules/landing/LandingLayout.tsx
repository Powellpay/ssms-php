import { Outlet, Link } from 'react-router-dom';
import { ROUTES } from '../../app/routes/constants';
import { GraduationCap, Menu, X } from 'lucide-react';
import { useState } from 'react';

export default function LandingLayout() {
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <div className="min-h-screen flex flex-col bg-bg">
      <header className="sticky top-0 z-30 bg-white/95 backdrop-blur-sm border-b border-border">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-16">
            <Link to={ROUTES.HOME} className="flex items-center gap-2.5">
              <div className="w-9 h-9 rounded-lg bg-primary flex items-center justify-center">
                <GraduationCap className="w-5 h-5 text-white" />
              </div>
              <span className="text-lg font-bold text-primary">SSMS</span>
            </Link>

            <nav className="hidden md:flex items-center gap-6 text-sm">
              <Link to={ROUTES.HOME} className="text-gray-600 hover:text-primary transition-colors">Home</Link>
              <Link to={ROUTES.AUTH.LOGIN} className="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-primary text-primary hover:bg-primary-light transition-colors text-sm font-medium">
                Sign In
              </Link>
              <Link to={ROUTES.AUTH.REGISTER} className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white hover:bg-primary-dark transition-colors text-sm font-medium">
                Get Started
              </Link>
            </nav>

            <button onClick={() => setMobileOpen(!mobileOpen)} className="md:hidden p-2 text-gray-600 cursor-pointer">
              {mobileOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>

          {mobileOpen && (
            <nav className="md:hidden pb-4 flex flex-col gap-3 text-sm">
              <Link to={ROUTES.HOME} onClick={() => setMobileOpen(false)} className="px-2 py-1.5 text-gray-600">Home</Link>
              <Link to={ROUTES.AUTH.LOGIN} onClick={() => setMobileOpen(false)} className="px-4 py-2 rounded-lg border border-primary text-primary text-center font-medium">Sign In</Link>
              <Link to={ROUTES.AUTH.REGISTER} onClick={() => setMobileOpen(false)} className="px-4 py-2 rounded-lg bg-primary text-white text-center font-medium">Get Started</Link>
            </nav>
          )}
        </div>
      </header>

      <main className="flex-1">
        <Outlet />
      </main>

      <footer className="border-t border-border bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-muted">
            <div className="flex items-center gap-2">
              <GraduationCap className="w-4 h-4 text-primary" />
              <span className="font-semibold text-gray-700">SSMS</span>
              <span>&mdash; School Management System</span>
            </div>
            <p>&copy; {new Date().getFullYear()} SSMS. Uganda New Lower Secondary Curriculum.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
