import {
  IconDotsVertical,
  IconEye,
  IconPencil,
  IconTrash,
} from '@tabler/icons-react';
import { type ColumnDef } from '@tanstack/react-table';

import { ProjectDialog } from '@/components/custom/dialog/ProjectDialog';
import { SeeMoreDialog } from '@/components/custom/generic/ScrollableDialog';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { ProjectResource } from '@/interfaces/entity/project';
import type { ComponentPropsWithoutRef } from 'react';
import { GenericTable } from '../../table/GenericTable';
import TableRowLink from '../../table/TableRowLink';
import DeleteProjectAction from './DeleteProjectAction';

const columns: ColumnDef<ProjectResource>[] = [
  {
    accessorKey: 'name',
    header: 'name',
    cell: ({ row }) => (
      <TableRowLink to={`/projects/${row.original.attributes.slug}`}>
        {row.original.attributes.name}
      </TableRowLink>
    ),
  },
  {
    accessorKey: 'description',
    header: 'Description',
    cell: ({ row }) =>
      row.original.attributes.description ? (
        row.original.attributes.description.length < 80 ? (
          <TableRowLink to={`/projects/${row.original.attributes.slug}`}>
            {row.original.attributes.description}
          </TableRowLink>
        ) : (
          <div className="space-x-2">
            <TableRowLink
              className="hover:font-normal"
              to={`/projects/${row.original.attributes.slug}`}
            >
              {row.original.attributes.description.slice(0, 80)}...
            </TableRowLink>
            <SeeMoreDialog
              title="Project Description"
              description={`Description for project "${row.original.attributes.name}"`}
              text={row.original.attributes.description}
            >
              <Button variant="link" size="sm">
                See More
              </Button>
            </SeeMoreDialog>
          </div>
        )
      ) : (
        <span className="text-muted-foreground">-</span>
      ),
    enableHiding: true,
  },
  {
    accessorKey: 'timeFramesCount',
    header: 'Time Frames Count',
    cell: ({ row }) => {
      return (
        row.original.attributes.timeFramesCount ?? (
          <span className="text-muted-foreground">-</span>
        )
      );
    },
    enableHiding: true,
  },
  {
    accessorKey: 'timeEntriesCount',
    header: 'Time Entries Count',
    cell: ({ row }) => {
      return (
        row.original.attributes.timeEntriesCount ?? (
          <span className="text-muted-foreground">-</span>
        )
      );
    },
    enableHiding: true,
  },
  {
    id: 'actions',
    cell: ({ row }) => {
      return (
        <div className="flex items-center gap-2">
          <TableRowLink to={`/projects/${row.original.attributes.slug}`}>
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
              <ProjectDialog mode="edit" project={row.original}>
                <DropdownMenuItem
                  className="cursor-pointer"
                  onSelect={(e) => e.preventDefault()}
                >
                  <IconPencil size={14} />
                  Edit
                </DropdownMenuItem>
              </ProjectDialog>
              <DeleteProjectAction projectId={row.original.id}>
                <DropdownMenuItem
                  className="cursor-pointer"
                  onSelect={(e) => e.preventDefault()}
                >
                  <IconTrash size={14} />
                  Delete
                </DropdownMenuItem>
              </DeleteProjectAction>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      );
    },
  },
];

type TableProbs = Omit<
  ComponentPropsWithoutRef<typeof GenericTable<ProjectResource>>,
  'columns' | 'TabsNode'
>;
export function ProjectDataTable({ ...props }: TableProbs) {
  return <GenericTable columns={columns} {...props} />;
}
