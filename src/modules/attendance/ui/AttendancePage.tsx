import ModulePlaceholder from '../../../shared/components/ModulePlaceholder';
import { CalendarCheck } from 'lucide-react';
export default function AttendancePage() { return <ModulePlaceholder title="Attendance" icon={CalendarCheck} description="Daily attendance register per stream. Term summaries feed into report cards." features={['Mark daily attendance', 'Per-stream register', 'Term summary reports', 'Auto-populates report cards']} />; }
