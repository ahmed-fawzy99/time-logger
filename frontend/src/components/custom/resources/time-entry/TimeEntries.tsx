import { TimeEntryDialog } from '@/components/custom/dialog/TimeEntryDialog';
import { TimeEntryStopwatchDialog } from '@/components/custom/dialog/TimeEntryStopwatchDialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { TimeEntryResource } from '@/interfaces/entity/time-entry';
import { IconPlayerPlay, IconPlus } from '@tabler/icons-react';
import TimeEntry from './TimeEntry';

interface TimeEntriesProps {
  timeFrameId: string;
  entries?: TimeEntryResource[];
  currency?: string;
  hourlyRate?: number;
}

export default function TimeEntries({
  timeFrameId,
  entries,
  currency,
  hourlyRate,
}: TimeEntriesProps) {
  return (
    <Card>
      <div className="flex max-md:flex-col justify-between gap-2 px-6">
        <div className="space-y-1">
          <h2 className="title">Time Entries</h2>
          <p className="text-muted-foreground">
            All time entries recorded for this time frame.
          </p>
        </div>
        <div className="flex gap-2">
          <TimeEntryStopwatchDialog timeFrameId={timeFrameId}>
            <Button variant="outline">
              <IconPlayerPlay />
              Start Stopwatch
            </Button>
          </TimeEntryStopwatchDialog>
          <TimeEntryDialog mode="create" timeFrameId={timeFrameId}>
            <Button>
              <IconPlus />
              Add Entry
            </Button>
          </TimeEntryDialog>
        </div>
      </div>
      <CardContent className="space-y-2">
        {entries && entries.length > 0 ? (
          <div className="space-y-1">
            {entries.map((entry) => (
              <TimeEntry
                key={entry.id}
                entry={entry}
                currency={currency}
                hourlyRate={hourlyRate}
              />
            ))}
          </div>
        ) : (
          <div className="text-center py-8 text-muted-foreground">
            No time entries recorded yet
          </div>
        )}
      </CardContent>
    </Card>
  );
}
