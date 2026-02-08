import ProjectForm from '@/components/custom/forms/ProjectForm';
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';
import type { ComponentPropsWithoutRef, PropsWithChildren } from 'react';

type FormProps = PropsWithChildren &
  ComponentPropsWithoutRef<typeof ProjectForm>;

export function ProjectDialog({ children, ...props }: FormProps) {
  return (
    <Dialog>
      <DialogTrigger asChild>{children}</DialogTrigger>
      <DialogContent className="sm:max-w-sm">
        <DialogHeader>
          <DialogTitle>
            {props.mode === 'create' ? 'Create' : 'Edit'}
          </DialogTitle>
        </DialogHeader>
        <ProjectForm {...props} />
      </DialogContent>
    </Dialog>
  );
}
