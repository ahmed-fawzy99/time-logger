import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import usePersistedStopWatch from '@/hooks/use-persisted-stopwatch';
import {
  IconPlayerPause,
  IconPlayerPlay,
  IconRefresh,
} from '@tabler/icons-react';

const formatTime = (value: number) => String(value).padStart(2, '0');

export default function Stopwatch() {
  const { seconds, minutes, hours, days, isRunning, start, pause, reset } =
    usePersistedStopWatch();

  // Format time values with leading zeros

  return (
    <div className="flex flex-col items-center gap-4 py-2">
      {/* Time Display */}
      <Card className="w-full px-6 py-8 bg-muted/30">
        <div className="flex items-center justify-center gap-1 font-mono text-5xl font-bold tabular-nums tracking-tight">
          {days > 0 && (
            <>
              <span className="text-primary">{formatTime(days)}</span>
              <span className="text-muted-foreground">:</span>
            </>
          )}
          <span className="text-foreground">{formatTime(hours)}</span>
          <span className="text-muted-foreground">:</span>
          <span className="text-foreground">{formatTime(minutes)}</span>
          <span className="text-muted-foreground">:</span>
          <span className="text-foreground">{formatTime(seconds)}</span>
        </div>
        <div className="mt-2 text-center text-xs text-muted-foreground font-medium tracking-wide">
          {days > 0 && 'DAYS : '}
          HOURS : MINUTES : SECONDS
        </div>
      </Card>
      <p className="text-xs text-muted-foreground">
        Closing this popup will pause the stopwatch.
      </p>

      {/* Control Buttons */}
      <div className="flex items-center gap-2 w-full">
        <Button
          onClick={isRunning ? pause : start}
          size="lg"
          variant={isRunning ? 'outline' : 'default'}
          className="flex-1"
        >
          {isRunning ? (
            <>
              <IconPlayerPause className="size-5" />
              Pause
            </>
          ) : (
            <>
              <IconPlayerPlay className="size-5" />
              Start
            </>
          )}
        </Button>
        <Button
          onClick={() => reset()}
          size="lg"
          variant="outline"
          className="flex-1"
          disabled={hours === 0 && minutes === 0 && seconds === 0 && days === 0}
        >
          <IconRefresh className="size-5" />
          Reset
        </Button>
      </div>
    </div>
  );
}
