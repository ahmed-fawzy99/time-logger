import TimeFrameForm from '@/components/custom/forms/TimeFrameForm';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import type { ComponentPropsWithoutRef, PropsWithChildren } from 'react';

type FormProps = PropsWithChildren &
  ComponentPropsWithoutRef<typeof TimeFrameForm>;

export function TimeFrameDialog({ children, ...props }: FormProps) {
  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="sm:max-w-sm">
        <DialogHeader>
          <DialogTitle>
            {props.mode === 'create' ? 'Create' : 'Edit'}
          </DialogTitle>
        </DialogHeader>
        <TimeFrameForm {...props} />
      </DialogContent>
    </Dialog>
  );
}
