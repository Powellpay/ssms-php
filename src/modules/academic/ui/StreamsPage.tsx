import ModulePlaceholder from '../../../shared/components/ModulePlaceholder';
import { GitBranch } from 'lucide-react';

export default function StreamsPage() {
  return <ModulePlaceholder title="Streams" icon={GitBranch} description="Create and manage class streams (A, B, East, etc.) with class teachers." features={['Create streams per class', 'Assign class teachers', 'View stream lists', 'Manage student placement']} />;
}
