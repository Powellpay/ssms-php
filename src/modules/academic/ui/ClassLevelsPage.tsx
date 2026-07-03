import ModulePlaceholder from '../../../shared/components/ModulePlaceholder';
import { Layers } from 'lucide-react';

export default function ClassLevelsPage() {
  return <ModulePlaceholder title="Class Levels" icon={Layers} description="Set up S1–S4 class levels for your school." features={['Add class levels', 'Set numeric ordering', 'Configure subjects per class']} />;
}
