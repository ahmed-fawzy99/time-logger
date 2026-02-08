import { TableCell, TableRow } from '@/components/ui/table';
import { flexRender, type Row } from '@tanstack/react-table';
export function Row<T>({ row }: { row: Row<T> }) {
  return (
    <TableRow data-state={row.getIsSelected() && 'selected'}>
      {row.getVisibleCells().map((cell) => (
        <TableCell key={cell.id}>
          {flexRender(cell.column.columnDef.cell, cell.getContext())}
        </TableCell>
      ))}
    </TableRow>
  );
}
