import { ConfirmationDialog } from '@/components/custom/dialog/ConfirmationDialog';
import { deleteTimeEntry } from '@/lib/data-access/time-entry';
import { useRouter } from '@tanstack/react-router';
import type { PropsWithChildren } from 'react';

type DeleteTimeEntryActionProps = PropsWithChildren & {
  timeEntryId: string;
};

export default function DeleteTimeEntryAction({
  timeEntryId,
  children,
}: DeleteTimeEntryActionProps) {
  const router = useRouter();
  return (
    <ConfirmationDialog
      title="Delete Time Entry"
      description="Are you sure you want to delete this time enTimtry?"
      ctaText="Delete"
      ctaVariant="destructive"
      successToastMessage="Time entry deleted successfully"
      onConfirm={async () => {
        await deleteTimeEntry({ id: timeEntryId });
        router.invalidate();
      }}
    >
      {children}
    </ConfirmationDialog>
  );
}
