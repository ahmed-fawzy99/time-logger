import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import type { PropsWithChildren } from 'react';

type SeeMoreDialogProps = PropsWithChildren & {
  title: string;
  description?: string;
  text: string;
};

export function SeeMoreDialog({
  title,
  description,
  text,
  children,
}: SeeMoreDialogProps) {
  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
          {description && <DialogDescription>{description}</DialogDescription>}
        </DialogHeader>
        <div className="no-scrollbar -mx-4 max-h-[50vh] overflow-y-auto px-4">
          <p className="mb-4 leading-normal">{text}</p>
        </div>
      </DialogContent>
    </Dialog>
  );
}
