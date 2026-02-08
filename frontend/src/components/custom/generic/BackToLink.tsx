import { cn } from '@/utils/cn-utils';
import { IconChevronLeft } from '@tabler/icons-react';
import { Link } from '@tanstack/react-router';

interface BackToLinkProps {
  to: string;
  text?: string;
  className?: string;
}

export default function BackToLink({ to, text, className }: BackToLinkProps) {
  return (
    <Link
      to={to}
      className={cn(
        'text-xs font-medium text-muted-foreground hover:underline flex items-center gap-1 mb-2',
        className,
      )}
    >
      <IconChevronLeft size={12} className="-mt-0.5" />
      {text ?? 'Back'}
    </Link>
  );
}
