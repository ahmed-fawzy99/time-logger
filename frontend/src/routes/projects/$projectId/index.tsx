import { TimeFrameDialog } from '@/components/custom/dialog/TimeFrameDialog';
import FormErrors from '@/components/custom/forms/FormErrors';
import BackToLink from '@/components/custom/generic/BackToLink';
import { TimeFrameDataTable } from '@/components/custom/resources/time-frame/TimeFrameDataTable';
import DataTableSkeleton from '@/components/custom/skeleton/DataTableSkeleton';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { DEFAULT_API_PAGE_SIZE, SWR_CACHE_KEYS } from '@/config';
import type { TimeFrameStatus } from '@/interfaces/entity/time-frame';
import { getProject } from '@/lib/data-access/project';
import { getTimeFrames } from '@/lib/data-access/time-frame';
import { IconPlus } from '@tabler/icons-react';
import { createFileRoute } from '@tanstack/react-router';
import { useQueryState } from 'nuqs';
import useSWR from 'swr';

export const Route = createFileRoute('/projects/$projectId/')({
  component: RouteComponent,
  loader: async ({ params: { projectId } }) => {
    const project = await getProject({
      identifier: projectId,
    });
    return { project };
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
  const { project } = Route.useLoaderData();
  const [tab] = useQueryState('tab', {
    defaultValue: 'all',
  });
  const [pageNumber, setPageNumber] = useQueryState('pageNumber', {
    defaultValue: '1',
  });
  const [pageSize, setPageSize] = useQueryState('pageSize', {
    defaultValue: DEFAULT_API_PAGE_SIZE.toString(),
  });

  const pageNumberInt = parseInt(pageNumber);
  const pageSizeInt = parseInt(pageSize);

  const fetcher = () =>
    getTimeFrames({
      projectId: project.id,
      include: 'timeEntries',
      add: 'totalBillableSeconds',
      sort: '-startDate',
      status: tab === 'all' ? undefined : (tab as TimeFrameStatus),
      pageNumber: pageNumberInt,
      pageSize: pageSizeInt,
    });

  const { data, error, isLoading } = useSWR(
    [SWR_CACHE_KEYS.TIME_FRAMES, tab, pageNumber, pageSize, project.id],
    fetcher,
  );

  return (
    <>
      <BackToLink to={`/projects`} text="Back to Projects" />
      <div className="space-y-4">
        <div className="flex max-md:flex-col items-start justify-between w-full gap-2">
          <div className="space-y-1">
            <h1 className="title">
              Project <i>{project.attributes.name}</i>
            </h1>
            <p className="text-muted-foreground">
              Time frames are used to define the start and end dates for your
              logged hours. <br />
              Invoices later will be generated based on these time frames.
            </p>
          </div>
          <TimeFrameDialog mode="create" projectId={project.id}>
            <Button>
              <IconPlus />
              Create Time Frame
            </Button>
          </TimeFrameDialog>
        </div>
        {error && <FormErrors title="Fetch Error" errors={error.message} />}
        {!error && isLoading && !data && <DataTableSkeleton />}
        {!error && data && (
          <TimeFrameDataTable
            data={data}
            onPageChange={setPageNumber}
            onPageSizeChange={setPageSize}
          />
        )}
      </div>
    </>
  );
}
