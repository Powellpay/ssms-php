import ModulePlaceholder from '../../../shared/components/ModulePlaceholder';
import { ClipboardCheck } from 'lucide-react';
export default function AssessmentRecordsPage() { return <ModulePlaceholder title="Assessment Records" icon={ClipboardCheck} description="Record CA and exam scores per learner per subject. Auto-computes final grades (A–E)." features={['Enter CA and exam scores', 'Auto grade computation', 'View subject term results', 'Generate report cards']} />; }
