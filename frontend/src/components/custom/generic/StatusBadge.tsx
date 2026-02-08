import type { TimeFrameStatus } from '@/interfaces/entity/time-frame';
import {
  IconCancel,
  IconCircleCheckFilled,
  IconClock,
} from '@tabler/icons-react';
import { Badge } from '../../ui/badge';

export default function StatusBadge({ status }: { status: TimeFrameStatus }) {
  return (
    <Badge variant="outline" className="text-muted-foreground px-1.5">
      {status === 'done' ? (
        <IconCircleCheckFilled className="fill-green-500 dark:fill-green-400" />
      ) : status === 'in_progress' ? (
        <IconClock />
      ) : (
        <IconCancel className="text-destructive" />
      )}
      {status}
    </Badge>
  );
}
