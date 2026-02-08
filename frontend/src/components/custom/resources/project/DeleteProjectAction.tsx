import { ConfirmationDialog } from '@/components/custom/dialog/ConfirmationDialog';
import { DEFAULT_API_PAGE_SIZE, SWR_CACHE_KEYS } from '@/config';
import { deleteProject } from '@/lib/data-access/project';
import { useQueryState } from 'nuqs';
import type { PropsWithChildren } from 'react';
import { useSWRConfig } from 'swr';

type DeleteProjectActionProps = PropsWithChildren & {
  projectId: string;
};

export default function DeleteProjectAction({
  projectId,
  children,
}: DeleteProjectActionProps) {
  const { mutate } = useSWRConfig();
  const [tab] = useQueryState('tab', { defaultValue: 'all' });
  const [pageNumber] = useQueryState('pageNumber', { defaultValue: '1' });
  const [pageSize] = useQueryState('pageSize', {
    defaultValue: DEFAULT_API_PAGE_SIZE.toString(),
  });
  return (
    <ConfirmationDialog
      title="Delete Project"
      description="Are you sure you want to delete this project?"
      ctaText="Delete"
      ctaVariant="destructive"
      successToastMessage="Project deleted successfully"
      onConfirm={async () => {
        await deleteProject({ id: projectId });
        mutate([SWR_CACHE_KEYS.PROJECTS, tab, pageNumber, pageSize]);
      }}
    >
      {children}
    </ConfirmationDialog>
  );
}
