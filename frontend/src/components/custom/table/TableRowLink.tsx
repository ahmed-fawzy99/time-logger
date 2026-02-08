import { cn } from '@/utils/cn-utils';
import { Link } from '@tanstack/react-router';
import type { PropsWithChildren } from 'react';

type TableRowLinkProps = PropsWithChildren & {
  to: string;
  relative?: boolean;
  className?: string;
};

export default function TableRowLink({
  children,
  className,
  to,
}: TableRowLinkProps) {
  return (
    <Link to={to} className={cn('w-fit hover:font-medium', className)}>
      {children}
    </Link>
  );
}
