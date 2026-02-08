import {
  IconCancel,
  IconCircleCheckFilled,
  IconClock,
  IconDotsVertical,
  IconEye,
  IconPencil,
  IconTrash,
} from '@tabler/icons-react';
import { type ColumnDef } from '@tanstack/react-table';

import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { TimeFrameResource } from '@/interfaces/entity/time-frame';
import dayjs from '@/lib/dayjs';
import type { ComponentPropsWithoutRef } from 'react';
import { Badge } from '../../../ui/badge';
import { TimeFrameDialog } from '../../dialog/TimeFrameDialog';
import { GenericTable } from '../../table/GenericTable';
import TableRowLink from '../../table/TableRowLink';
import DeleteTimeFrameAction from './DeleteTimeFrameAction';
import TimeFrameTableTabs from './TimeFrameTableTabs';

const columns: ColumnDef<TimeFrameResource>[] = [
  {
    accessorKey: 'name',
    header: 'Name',
    cell: ({ row }) => {
      return row.original.attributes.name ? (
        <TableRowLink to={`./${row.original.id}`}>
          {row.original.attributes.name}
        </TableRowLink>
      ) : (
        <span className="text-muted-foreground">Unnammed Entry</span>
      );
    },
    enableHiding: true,
  },
  {
    accessorKey: 'startDate',
    header: 'Start Date',
    cell: ({ row }) => {
      return dayjs(row.original.attributes.startDate).format('MMM D, YYYY');
    },
    enableHiding: false,
  },
  {
    accessorKey: 'endDate',
    header: 'End Date',
    cell: ({ row }) => {
      return dayjs(row.original.attributes.endDate).format('MMM D, YYYY');
    },
    enableHiding: false,
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: ({ row }) => (
      <Badge variant="outline" className="text-muted-foreground px-1.5">
        {row.original.attributes.status === 'done' ? (
          <IconCircleCheckFilled className="fill-green-500 dark:fill-green-400" />
        ) : row.original.attributes.status === 'in_progress' ? (
          <IconClock />
        ) : null}
        {row.original.attributes.status === 'canceled' && (
          <IconCancel className="text-destructive" />
        )}
        {row.original.attributes.status}
      </Badge>
    ),
    enableHiding: false,
  },
  {
    accessorKey: 'periodDuration',
    header: 'Period Duration (Tracked)',
    cell: ({ row }) => {
      return (
        row.original.attributes.periodDurationInDays +
        ` (${row.original.attributes.daysTracked ?? 0} Tracked)`
      );
    },
    enableHiding: true,
  },
  {
    accessorKey: 'totalRecordedDuration',
    header: 'Total Recorded Duration',
    cell: ({ row }) => {
      return row.original.attributes.totalRecordedDurationInMinutes ? (
        dayjs
          .duration(
            Number(row.original.attributes.totalRecordedDurationInMinutes),
            'minutes',
          )
          .format('H [hrs] m [min]')
      ) : (
        <span className="text-muted-foreground">0 hrs 0 min</span>
      );
    },
    enableHiding: true,
  },
  {
    accessorKey: 'entriesCount',
    header: 'Entries Count',
    cell: ({ row }) => {
      return row.original.attributes.entriesCount ?? 0;
    },
    enableHiding: true,
  },
  {
    accessorKey: 'averageDailyDuration',
    header: 'Average Daily Duration',
    cell: ({ row }) => {
      return row.original.attributes.averageDailyDurationInMinutes ? (
        dayjs
          .duration(
            Number(row.original.attributes.averageDailyDurationInMinutes),
            'minutes',
          )
          .format('H [hrs] m [min]')
      ) : (
        <span className="text-muted-foreground">0 hrs 0 min</span>
      );
    },
    enableHiding: true,
  },
  {
    accessorKey: 'total_amount',
    header: 'Total Amount',
    cell: ({ row }) => {
      return `${row.original.attributes.totalBillable}`;
    },
    enableHiding: true,
  },
  {
    id: 'actions',
    cell: ({ row }) => {
      return (
        <div className="flex items-center gap-2">
          <TableRowLink to={`./${row.original.id}`}>
            <IconEye size={16} className="" />
          </TableRowLink>
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button
                variant="ghost"
                className="data-[state=open]:bg-muted text-muted-foreground flex size-8"
                size="icon"
              >
                <IconDotsVertical />
                <span className="sr-only">Open menu</span>
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-32">
              <TimeFrameDialog
                mode="edit"
                timeFrame={row.original}
                projectId={row.original.relationships.project.data.id}
              >
                <DropdownMenuItem
                  className="cursor-pointer"
                  onSelect={(e) => e.preventDefault()}
                >
                  <IconPencil size={14} />
                  Edit
                </DropdownMenuItem>
              </TimeFrameDialog>
              <DeleteTimeFrameAction timeFrameId={row.original.id}>
                <DropdownMenuItem
                  className="cursor-pointer"
                  onSelect={(e) => e.preventDefault()}
                >
                  <IconTrash size={14} />
                  Delete
                </DropdownMenuItem>
              </DeleteTimeFrameAction>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      );
    },
  },
];

type TableProbs = Omit<
  ComponentPropsWithoutRef<typeof GenericTable<TimeFrameResource>>,
  'columns' | 'TabsNode'
>;
export function TimeFrameDataTable({ ...props }: TableProbs) {
  return (
    <GenericTable
      columns={columns}
      TabsNode={<TimeFrameTableTabs />}
      {...props}
    />
  );
}
