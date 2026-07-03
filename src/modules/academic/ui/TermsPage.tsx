import ModulePlaceholder from '../../../shared/components/ModulePlaceholder';
import { GraduationCap } from 'lucide-react';

export default function TermsPage() {
  return <ModulePlaceholder title="Terms" icon={GraduationCap} description="Configure terms (Term 1–3) within each academic year." features={['Create terms per year', 'Set start/end dates', 'Mark current term', 'Set next term date']} />;
}
