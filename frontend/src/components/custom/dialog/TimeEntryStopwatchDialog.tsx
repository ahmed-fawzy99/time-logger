import TimeEntryForm from '@/components/custom/forms/TimeEntryForm';
import { Button } from '@/components/ui/button';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import {
  useState,
  type ComponentPropsWithoutRef,
  type PropsWithChildren,
} from 'react';
import Stopwatch from '../generic/Stopwatch';

type FormProps = PropsWithChildren &
  Omit<
    ComponentPropsWithoutRef<typeof TimeEntryForm>,
    'mode' | 'useStopwatchValue'
  >;

export function TimeEntryStopwatchDialog({ children, ...props }: FormProps) {
  const [convert, setConvert] = useState(false);
  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="sm:max-w-sm">
        <DialogHeader>
          <DialogTitle>
            {convert ? 'Create Time Entry' : 'Stopwatch'}
          </DialogTitle>
        </DialogHeader>
        {convert ? (
          <div className="space-y-4">
            <TimeEntryForm mode="create" useStopwatchValue={true} {...props} />
            <Button
              variant="ghost"
              size="sm"
              className="w-full mx-auto"
              onClick={() => setConvert(false)}
            >
              Return to Stopwatch
            </Button>
          </div>
        ) : (
          <div>
            <Stopwatch />
            <Button
              variant="ghost"
              size="sm"
              className="w-full mx-auto"
              onClick={() => setConvert(true)}
            >
              Convert to Time Entry
            </Button>
          </div>
        )}
      </DialogContent>
    </Dialog>
  );
}
