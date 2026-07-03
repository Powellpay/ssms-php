import ModulePlaceholder from '../../../shared/components/ModulePlaceholder';
import { School } from 'lucide-react';

export default function AcademicYearsPage() {
  return <ModulePlaceholder title="Academic Years" icon={School} description="Manage academic years and set the current active year." features={['Create new academic year', 'Set current year', 'View year timeline', 'Edit year dates']} />;
}
