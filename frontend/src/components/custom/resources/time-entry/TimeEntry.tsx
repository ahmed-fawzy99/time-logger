import { TimeEntryDialog } from '@/components/custom/dialog/TimeEntryDialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { TimeEntryResource } from '@/interfaces/entity/time-entry';
import { usePreferences } from '@/providers/PreferencesProvider';
import {
  IconClock,
  IconCurrencyDollar,
  IconCurrencyDollarOff,
  IconPencil,
  IconTrash,
} from '@tabler/icons-react';
import dayjs from 'dayjs';
import DeleteTimeEntryAction from './DeleteTimeEntryAction';

export default function TimeEntry({ entry }: { entry: TimeEntryResource }) {
  const { preferences } = usePreferences();
  const startTime = dayjs(entry.attributes.startTime);
  const endTime = dayjs(entry.attributes.endTime);
  const duration = dayjs.duration(endTime.diff(startTime));

  return (
    <div className="flex max-md:flex-col sm:items-start gap-2 sm:gap-4 py-3 px-4 rounded-lg hover:bg-muted/50 transition-colors border border-transparent hover:border-border">
      <div className="w-full flex max-md:flex-col justify-between items-start">
        <div className="flex-1 space-y-1 min-w-0">
          <div className="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm">
            <IconClock className="size-4 text-muted-foreground" />
            <span className="font-medium">
              {startTime.format('h:mm A')} - {endTime.format('h:mm A')}
            </span>
            <span className="text-muted-foreground">
              ({duration.format('H[h] m[m]')})
            </span>
            {!entry.attributes.billable ? (
              <span className="text-sm text-muted-foreground flex items-center gap-1">
                {' - '}
                <IconCurrencyDollarOff className="text-destructive" size={16} />
                not billable
              </span>
            ) : (
              Number(duration) > 0 &&
              preferences?.attributes.hourlyRate &&
              preferences?.attributes.currency && (
                <Badge
                  variant="outline"
                  className="flex items-center gap-1 text-xs text-muted-foreground"
                >
                  <IconCurrencyDollar className="size-4 text-green-600 dark:text-green-400" />
                  <span>
                    {preferences.attributes.currency}{' '}
                    {(
                      duration.asHours() *
                      Number(preferences.attributes.hourlyRate)
                    ).toFixed(2)}
                  </span>
                </Badge>
              )
            )}
          </div>
          {entry.attributes.description ? (
            <p className="text-sm text-muted-foreground ">
              {entry.attributes.description}
            </p>
          ) : (
            <p className="text-sm text-muted-foreground italic">
              No description provided
            </p>
          )}
          <div className="text-xs text-muted-foreground">
            {dayjs(entry.attributes.workDay).format('MMM D, YYYY')}
          </div>
        </div>
        <div className="w-full flex justify-end items-center gap-1 shrink-0 sm:hidden">
          <DeleteTimeEntryAction timeEntryId={entry.id}>
            <Button
              size="icon"
              variant="ghost"
              className="size-8 text-muted-foreground"
            >
              <IconTrash className="size-4" />
            </Button>
          </DeleteTimeEntryAction>
          <TimeEntryDialog
            timeFrameId={entry.relationships.timeFrame?.data.id || ''}
            mode="edit"
            timeEntry={entry}
          >
            <Button
              size="icon"
              variant="ghost"
              className="size-8 text-muted-foreground"
            >
              <IconPencil className="size-4" />
            </Button>
          </TimeEntryDialog>
        </div>
      </div>
      <div className="hidden sm:flex items-center gap-1 shrink-0">
        <DeleteTimeEntryAction timeEntryId={entry.id}>
          <Button
            size="sm"
            variant="ghost"
            className="gap-2 text-muted-foreground"
          >
            <IconTrash className="-mb-0.5" />
            Delete
          </Button>
        </DeleteTimeEntryAction>
        <TimeEntryDialog
          timeFrameId={entry.relationships.timeFrame?.data.id || ''}
          mode="edit"
          timeEntry={entry}
        >
          <Button
            size="sm"
            variant="ghost"
            className="gap-2 text-muted-foreground"
          >
            <IconPencil className="-mb-0.5" />
            Edit
          </Button>
        </TimeEntryDialog>
      </div>
    </div>
  );
}
