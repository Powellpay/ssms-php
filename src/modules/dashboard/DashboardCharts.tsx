import { PieChart, Pie, Cell, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { useGenderDistribution, useStatusDistribution } from './useDashboardQueries';
import LoadingSpinner from '../../shared/components/ui/LoadingSpinner';

const pieColors = ['#1f6f43', '#3b82f6', '#f59e0b', '#ec4899', '#8b5cf6', '#ef4444'];

function CustomTooltip({ active, payload }: { active?: boolean; payload?: { name: string; value: number }[] }) {
  if (!active || !payload?.length) return null;
  return (
    <div className="bg-white border border-border rounded-lg shadow-md px-3 py-2 text-sm">
      <p className="font-medium text-gray-900">{payload[0].name}</p>
      <p className="text-muted">{payload[0].value} student{payload[0].value !== 1 ? 's' : ''}</p>
    </div>
  );
}

export function GenderChart() {
  const { data, isLoading } = useGenderDistribution();

  if (isLoading) return <div className="h-64 flex items-center justify-center"><LoadingSpinner /></div>;
  if (!data.length) return <div className="h-64 flex items-center justify-center text-sm text-muted">No student data</div>;

  return (
    <div className="rounded-xl border border-border bg-white p-5">
      <h3 className="text-sm font-semibold text-gray-900 mb-4">Gender Distribution</h3>
      <ResponsiveContainer width="100%" height={220}>
        <PieChart>
          <Pie data={data} cx="50%" cy="50%" innerRadius={55} outerRadius={85} dataKey="value" paddingAngle={3} animationBegin={0} animationDuration={600}>
            {data.map((_, i) => (
              <Cell key={i} fill={pieColors[i]} />
            ))}
          </Pie>
          <Tooltip content={<CustomTooltip />} />
          <Legend
            verticalAlign="bottom"
            iconType="circle"
            iconSize={8}
            formatter={(value: string) => <span className="text-xs text-gray-600">{value}</span>}
          />
        </PieChart>
      </ResponsiveContainer>
    </div>
  );
}

export function StatusChart() {
  const { data, isLoading } = useStatusDistribution();

  if (isLoading) return <div className="h-64 flex items-center justify-center"><LoadingSpinner /></div>;
  if (!data.length) return <div className="h-64 flex items-center justify-center text-sm text-muted">No student data</div>;

  return (
    <div className="rounded-xl border border-border bg-white p-5">
      <h3 className="text-sm font-semibold text-gray-900 mb-4">Student Status Overview</h3>
      <ResponsiveContainer width="100%" height={220}>
        <PieChart>
          <Pie data={data} cx="50%" cy="50%" innerRadius={55} outerRadius={85} dataKey="value" paddingAngle={3} animationBegin={0} animationDuration={600}>
            {data.map((_, i) => (
              <Cell key={i} fill={pieColors[i]} />
            ))}
          </Pie>
          <Tooltip content={<CustomTooltip />} />
          <Legend
            verticalAlign="bottom"
            iconType="circle"
            iconSize={8}
            formatter={(value: string) => <span className="text-xs text-gray-600">{value}</span>}
          />
        </PieChart>
      </ResponsiveContainer>
    </div>
  );
}