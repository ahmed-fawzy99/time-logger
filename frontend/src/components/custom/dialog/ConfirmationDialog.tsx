import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { useState, type PropsWithChildren } from 'react';
import { toast } from 'sonner';

type ConfirmationDialogProps = PropsWithChildren & {
  ctaText?: string;
  ctaVariant?: 'default' | 'destructive' | 'outline';
  cancelText?: string;
  title?: string;
  description?: string;
  successToastMessage?: string;
  onConfirm: () => Promise<void>;
};

export function ConfirmationDialog({
  ctaText,
  ctaVariant = 'default',
  title,
  description,
  onConfirm,
  successToastMessage,
  cancelText = 'Cancel',
  children,
}: ConfirmationDialogProps) {
  const [open, setOpen] = useState(false);
  return (
    <AlertDialog open={open} onOpenChange={setOpen}>
      <AlertDialogTrigger asChild>{children}</AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>
            {title || 'Are you absolutely sure?'}
          </AlertDialogTitle>
          <AlertDialogDescription className='whitespace-pre-line'>
            {description ||
              'This action cannot be undone. This will permanently delete your account from our servers.'}
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>{cancelText}</AlertDialogCancel>
          <AlertDialogAction
            variant={ctaVariant}
            onClick={async (e) => {
              e.preventDefault();
              onConfirm()
                .then(() => {
                  if (successToastMessage) {
                    toast.success(successToastMessage);
                  }
                  setOpen(false);
                })
                .catch((e) => {
                  toast.error(
                    e instanceof Error ? e.message : 'An error occurred',
                  );
                });
            }}
          >
            {ctaText || 'Continue'}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
