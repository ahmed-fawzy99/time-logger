import StatusBadge from '@/components/custom/generic/StatusBadge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { TimeFrameResource } from '@/interfaces/entity/time-frame';
import dayjs from '@/lib/dayjs';
import {
  IconCalendar,
  IconClock,
  IconDownload,
  IconHash,
} from '@tabler/icons-react';

interface TimeFrameCardsProps {
  timeFrame: TimeFrameResource;
}

export default function TimeFrameCards({ timeFrame }: TimeFrameCardsProps) {
  return (
    <>
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card className="py-4">
          <CardContent className="space-y-3">
            <div className="flex items-center gap-2 text-sm font-medium text-muted-foreground">
              <IconCalendar className="size-4" />
              Period
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <div className="text-xs text-muted-foreground">Start Date</div>
                <div className="text-sm font-semibold mt-0.5">
                  {dayjs(timeFrame.attributes.startDate).format('MMM D, YYYY')}
                </div>
              </div>
              <div>
                <div className="text-xs text-muted-foreground">End Date</div>
                <div className="text-sm font-semibold mt-0.5">
                  {dayjs(timeFrame.attributes.endDate).format('MMM D, YYYY')}
                </div>
              </div>
            </div>
            <div>
              <div className="text-xs text-muted-foreground">Duration</div>
              <div className="text-sm font-semibold mt-0.5">
                {timeFrame.attributes.periodDurationInDays} days (
                {timeFrame.attributes.daysTracked} tracked)
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="py-4">
          <CardContent className="space-y-3">
            <div className="flex items-center gap-2 text-sm font-medium text-muted-foreground">
              <IconClock className="size-4" />
              Time Tracked
            </div>
            <div>
              <div className="text-xs text-muted-foreground">
                Total Duration
              </div>
              <div className="text-lg font-semibold mt-0.5">
                {timeFrame.attributes.totalRecordedDurationInMinutes
                  ? dayjs
                      .duration(
                        Number(
                          timeFrame.attributes.totalRecordedDurationInMinutes,
                        ),
                        'minutes',
                      )
                      .format('H [hrs] m [min]')
                  : '-'}
              </div>
            </div>
            <div>
              <div className="text-xs text-muted-foreground">Average Daily</div>
              <div className="text-sm font-semibold mt-0.5">
                {timeFrame.attributes.averageDailyDurationInMinutes
                  ? dayjs
                      .duration(
                        Number(
                          timeFrame.attributes.averageDailyDurationInMinutes,
                        ),
                        'minutes',
                      )
                      .format('H [hrs] m [min]')
                  : '-'}
              </div>
            </div>
          </CardContent>
        </Card>

        <Card className="py-4">
          <CardContent className="space-y-3">
            <div className="flex items-center gap-2 text-sm font-medium text-muted-foreground">
              <IconHash className="size-4" />
              Summary
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <div className="text-xs text-muted-foreground">Status</div>
                <div className="mt-1">
                  <StatusBadge status={timeFrame.attributes.status} />
                </div>
              </div>
              <div>
                <div className="text-xs text-muted-foreground">Entries</div>
                <div className="text-sm font-semibold mt-0.5">
                  {timeFrame.attributes.entriesCount}
                </div>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <div className="text-xs text-muted-foreground">
                  Total Billable{' '}
                  {timeFrame.attributes.status === 'in_progress'
                    ? '(so far)'
                    : ''}
                </div>
                <div className="text-lg font-semibold mt-0.5 text-green-600 dark:text-green-400">
                  {timeFrame.attributes.totalBillable ?? '-'}
                </div>
              </div>
              {timeFrame.attributes.invoiceUrl && (
                <div>
                  <div className="text-xs text-muted-foreground">Invoice</div>
                  <div className="mt-1">
                    <Button variant="outline" size="sm" asChild>
                      <a
                        href={timeFrame.attributes.invoiceUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                      >
                        <IconDownload className="size-4" />
                        Download
                      </a>
                    </Button>
                  </div>
                </div>
              )}
            </div>
          </CardContent>
        </Card>
      </div>

      {timeFrame.attributes.notes && (
        <Card className="space-y-2 gap-0">
          <CardHeader>
            <CardTitle className="font-medium">Notes</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground whitespace-pre-wrap">
              {timeFrame.attributes.notes}
            </p>
          </CardContent>
        </Card>
      )}
    </>
  );
}
