import ModulePlaceholder from '../../../shared/components/ModulePlaceholder';
import { Library } from 'lucide-react';
export default function BooksPage() { return <ModulePlaceholder title="Library" icon={Library} description="Book catalogue and borrowing management system." features={['Add books to catalogue', 'Issue and return books', 'Track overdue loans', 'Manage available copies']} />; }
