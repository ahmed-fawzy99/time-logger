import TimeEntryForm from '@/components/custom/forms/TimeEntryForm';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import type { ComponentPropsWithoutRef, PropsWithChildren } from 'react';

type FormProps = PropsWithChildren &
  ComponentPropsWithoutRef<typeof TimeEntryForm>;

export function TimeEntryDialog({ children, ...props }: FormProps) {
  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="sm:max-w-sm">
        <DialogHeader>
          <DialogTitle>
            {props.mode === 'create' ? 'Create' : 'Edit'}
          </DialogTitle>
        </DialogHeader>
        <TimeEntryForm {...props} />
      </DialogContent>
    </Dialog>
  );
}
