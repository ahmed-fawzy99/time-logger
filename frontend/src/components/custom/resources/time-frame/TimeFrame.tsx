import type { TimeFrameResource } from '@/interfaces/entity/time-frame';
import TimeEntries from '../time-entry/TimeEntries';
import TimeFrameCards from './TimeFrameCards';
import TimeFrameHeader from './TimeFrameHeader';

interface TimeFrameProps {
  timeFrame: TimeFrameResource;
}

export default function TimeFrame({ timeFrame }: TimeFrameProps) {
  return (
    <div className="space-y-6">
      <TimeFrameHeader timeFrame={timeFrame} />
      <TimeFrameCards timeFrame={timeFrame} />
      <TimeEntries
        timeFrameId={timeFrame.id}
        entries={timeFrame.includes?.timeEntries}
      />
    </div>
  );
}
