import { ConfirmationDialog } from '@/components/custom/dialog/ConfirmationDialog';
import { DEFAULT_API_PAGE_SIZE, SWR_CACHE_KEYS } from '@/config';
import { deleteTimeFrame } from '@/lib/data-access/time-frame';
import { useRouter } from '@tanstack/react-router';
import { useQueryState } from 'nuqs';
import type { PropsWithChildren } from 'react';
import { useSWRConfig } from 'swr';

type DeleteTimeFrameActionProps = PropsWithChildren & {
  timeFrameId: string;
  redirect?: boolean;
};

export default function DeleteTimeFrameAction({
  timeFrameId,
  redirect = false,
  children,
}: DeleteTimeFrameActionProps) {
  const { mutate } = useSWRConfig();
  const router = useRouter();
  const [tab] = useQueryState('tab', { defaultValue: 'all' });
  const [pageNumber] = useQueryState('pageNumber', { defaultValue: '1' });
  const [pageSize] = useQueryState('pageSize', {
    defaultValue: DEFAULT_API_PAGE_SIZE.toString(),
  });
  return (
    <ConfirmationDialog
      title="Delete Time Frame"
      description="Are you sure you want to delete this time frame? this action will also delete all time entries associated with this time frame"
      ctaText="Delete"
      ctaVariant="destructive"
      successToastMessage="Time Frame deleted successfully"
      onConfirm={async () => {
        await deleteTimeFrame({ id: timeFrameId });
        mutate([SWR_CACHE_KEYS.TIME_FRAMES, tab, pageNumber, pageSize]);
        if (redirect) {
          router.navigate({
            to: '/',
          });
        }
      }}
    >
      {children}
    </ConfirmationDialog>
  );
}
