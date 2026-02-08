import { TimeFrameDialog } from '@/components/custom/dialog/TimeFrameDialog';
import BackToLink from '@/components/custom/generic/BackToLink';
import { Button } from '@/components/ui/button';
import type { TimeFrameResource } from '@/interfaces/entity/time-frame';
import { capitalizeFirstLetter } from '@/utils/dom';
import { IconPencil, IconTrash } from '@tabler/icons-react';
import { useRouter } from '@tanstack/react-router';
import GetInvoice from '../invoice/GetInvoice';
import DeleteTimeFrameAction from './DeleteTimeFrameAction';

interface TimeFrameHeaderProps {
  timeFrame: TimeFrameResource;
}

export default function TimeFrameHeader({ timeFrame }: TimeFrameHeaderProps) {
  const router = useRouter();
  return (
    <>
      <BackToLink
        to={`/projects/${timeFrame.includes?.project?.attributes.slug ?? timeFrame.relationships.project.data.id}`}
        text="Back to Time Frames"
      />
      <div className="w-full flex max-md:flex-col items-start justify-between gap-2">
        <div className="space-y-1">
          <h1 className="title">
            {capitalizeFirstLetter(timeFrame.attributes.name ?? 'Time Frame')}{' '}
            Details
          </h1>
          <p className="text-muted-foreground">
            Manage the details of this time frame and its associated time
            entries.
          </p>
          <p className="text-muted-foreground">
            Invoices will be available for completed time frames.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <DeleteTimeFrameAction timeFrameId={timeFrame.id} redirect>
            <Button variant="destructive">
              <IconTrash />
              Delete
            </Button>
          </DeleteTimeFrameAction>

          {timeFrame.attributes.status === 'done' && (
            <GetInvoice timeFrame={timeFrame} />
          )}
          <TimeFrameDialog
            mode="edit"
            timeFrame={timeFrame}
            projectId={timeFrame.relationships.project.data.id}
            onSuccess={router.invalidate}
          >
            <Button variant="outline">
              <IconPencil />
              Edit
            </Button>
          </TimeFrameDialog>
        </div>
      </div>
    </>
  );
}
