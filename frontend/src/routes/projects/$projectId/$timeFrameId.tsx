import FormErrors from '@/components/custom/forms/FormErrors';
import TimeFrame from '@/components/custom/resources/time-frame/TimeFrame';
import { Skeleton } from '@/components/ui/skeleton';
import { getTimeFrame } from '@/lib/data-access/time-frame';
import { createFileRoute } from '@tanstack/react-router';

export const Route = createFileRoute('/projects/$projectId/$timeFrameId')({
  component: RouteComponent,
  loader: async ({ params: { timeFrameId } }) => {
    const timeFrame = await getTimeFrame({
      identifier: timeFrameId,
      include: 'timeEntries,project:id;name;slug,media',
      add: 'totalBillableSeconds',
    });
    return { timeFrame };
  },
  pendingComponent: () => <Skeleton className="h-180 w-full" />,
  errorComponent: ({ error }) => {
    return (
      <FormErrors
        errors={error?.message ?? ['An unexpected error occurred.']}
      />
    );
  },
});

function RouteComponent() {
  const { timeFrame } = Route.useLoaderData();
  return <TimeFrame timeFrame={timeFrame} />;
}
