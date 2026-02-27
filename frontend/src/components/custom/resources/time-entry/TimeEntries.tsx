import { TimeEntryDialog } from '@/components/custom/dialog/TimeEntryDialog';
import { TimeEntryStopwatchDialog } from '@/components/custom/dialog/TimeEntryStopwatchDialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import type { TimeEntryResource } from '@/interfaces/entity/time-entry';
import dayjs from '@/lib/dayjs';
import { IconPlayerPlay, IconPlus } from '@tabler/icons-react';
import { useMemo } from 'react';
import TimeEntry from './TimeEntry';

interface TimeEntriesProps {
  timeFrameId: string;
  entries?: TimeEntryResource[];
  currency?: string;
  hourlyRate?: number;
}

interface WeekGroup {
  weekKey: string;
  weekNumber: string;
  dateRange: string;
  totalMinutes: number;
  entries: TimeEntryResource[];
}

function groupEntriesByWeek(entries: TimeEntryResource[]): WeekGroup[] {
  const map = new Map<string, WeekGroup>();

  for (const entry of entries) {
    const day = dayjs(entry.attributes.workDay);
    const isoYear = day.isoWeekYear();
    const weekNumber = String(day.isoWeek());
    const weekKey = `${isoYear}-W${weekNumber.padStart(2, '0')}`;
    const weekStart = day.startOf('isoWeek');
    const weekEnd = day.endOf('isoWeek');
    const dateRange = `${weekStart.format('MMM D')} – ${weekEnd.format('MMM D, YYYY')}`;

    const start = dayjs(entry.attributes.startTime);
    const end = dayjs(entry.attributes.endTime);
    const minutes = end.diff(start, 'minute');

    if (!map.has(weekKey)) {
      map.set(weekKey, { weekKey, weekNumber, dateRange, totalMinutes: 0, entries: [] });
    }
    const group = map.get(weekKey)!;
    group.totalMinutes += minutes;
    group.entries.push(entry);
  }

  return Array.from(map.values()).sort((a, b) => b.weekKey.localeCompare(a.weekKey));
}

function formatWeekTotal(totalMinutes: number): string {
  const h = Math.floor(totalMinutes / 60);
  const m = totalMinutes % 60;
  return m > 0 ? `${h}h ${m}m` : `${h}h`;
}

export default function TimeEntries({
  timeFrameId,
  entries,
  currency,
  hourlyRate,
}: TimeEntriesProps) {
  const weekGroups = useMemo(
    () => (entries && entries.length > 0 ? groupEntriesByWeek(entries) : []),
    [entries],
  );

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
        {weekGroups.length > 0 ? (
          <div className="space-y-1">
            {weekGroups.map((group) => (
              <div key={group.weekKey}>
                <div className="flex items-center gap-3 px-1 py-1.5">
                  <span className="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                    Week {group.weekNumber}
                  </span>
                  <span className="text-xs text-muted-foreground">
                    {group.dateRange}
                  </span>
                  <div className="flex-1 h-px bg-border" />
                  <span className="text-xs font-medium text-muted-foreground tabular-nums">
                    {formatWeekTotal(group.totalMinutes)}
                  </span>
                </div>
                {group.entries.map((entry) => (
                  <TimeEntry
                    key={entry.id}
                    entry={entry}
                    currency={currency}
                    hourlyRate={hourlyRate}
                  />
                ))}
              </div>
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
