import { ProjectDialog } from '@/components/custom/dialog/ProjectDialog';
import FormErrors from '@/components/custom/forms/FormErrors';
import { ProjectDataTable } from '@/components/custom/resources/project/ProjectDataTable';
import DataTableSkeleton from '@/components/custom/skeleton/DataTableSkeleton';
import { Button } from '@/components/ui/button';
import { DEFAULT_API_PAGE_SIZE, SWR_CACHE_KEYS } from '@/config';
import { getProjects } from '@/lib/data-access/project';
import { IconPlus } from '@tabler/icons-react';
import { createFileRoute } from '@tanstack/react-router';
import { useQueryState } from 'nuqs';
import useSWR from 'swr';

export const Route = createFileRoute('/')({
  component: RouteComponent,
});

function RouteComponent() {
  const [tab] = useQueryState('tab', {
    defaultValue: 'all',
  });
  const [pageNumber, setPageNumber] = useQueryState('pageNumber', {
    defaultValue: '1',
  });
  const [pageSize, setPageSize] = useQueryState('pageSize', {
    defaultValue: DEFAULT_API_PAGE_SIZE.toString(),
  });

  const fetcher = () =>
    getProjects({
      add: 'timeFramesCount,timeEntriesCount',
      sort: 'createdAt,id',
      pageNumber: Number(pageNumber),
      pageSize: Number(pageSize),
    });

  const {
    data: projects,
    error,
    isLoading,
  } = useSWR([SWR_CACHE_KEYS.PROJECTS, tab, pageNumber, pageSize], fetcher);

  return (
    <div className="space-y-4">
      <div className="flex max-md:flex-col items-start justify-between w-full gap-2">
        <div className="space-y-1">
          <h1 className="title">Projects</h1>
          <p className="text-muted-foreground">
            Manage your projects and their associated time frames and entries.
          </p>
        </div>
        <ProjectDialog mode="create">
          <Button>
            <IconPlus />
            Create Project
          </Button>
        </ProjectDialog>
      </div>
      {error && <FormErrors title="Fetch Error" errors={error.message} />}
      {!error && isLoading && !projects && <DataTableSkeleton />}
      {!error && projects && (
        <ProjectDataTable
          data={projects}
          onPageChange={setPageNumber}
          onPageSizeChange={setPageSize}
        />
      )}
    </div>
  );
}
