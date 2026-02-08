import { Skeleton } from '@/components/ui/skeleton';

export default function DataTableSkeleton() {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between w-full">
        <Skeleton className="h-8 w-1/8" />
        <Skeleton className="h-9 w-1/8" />
      </div>
      <Skeleton className="h-100 w-full" />
      <div className="flex items-start justify-between w-full">
        <Skeleton className="h-6 w-1/12" />
        <Skeleton className="h-10 w-1/6" />
      </div>
    </div>
  );
}
