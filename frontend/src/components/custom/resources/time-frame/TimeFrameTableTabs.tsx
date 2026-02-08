import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs';
import type { TimeFrameStatus } from '@/interfaces/entity/time-frame';
import { useQueryState } from 'nuqs';

const TABS: { title: string; id: TimeFrameStatus | 'all' }[] = [
  { title: 'All', id: 'all' },
  { title: 'Done', id: 'done' },
  { title: 'In Progress', id: 'in_progress' },
];

export default function TimeFrameTableTabs() {
  const [tab, setTab] = useQueryState('tab');

  return (
    <Tabs
      defaultValue={tab || 'all'}
      className="w-full flex-col justify-start gap-6"
    >
      <div className="flex items-center justify-between">
        <Label htmlFor="view-selector" className="sr-only">
          View
        </Label>
        <Select defaultValue={tab || 'all'}>
          <SelectTrigger
            className="flex w-fit @4xl/main:hidden"
            size="sm"
            id="view-selector"
          >
            <SelectValue placeholder="Select a view" />
          </SelectTrigger>
          <SelectContent>
            {TABS.map((tab) => (
              <SelectItem key={tab.id} value={tab.id}>
                {tab.title}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
        <TabsList className="**:data-[slot=badge]:bg-muted-foreground/30 hidden **:data-[slot=badge]:size-5 **:data-[slot=badge]:rounded-full **:data-[slot=badge]:px-1 @4xl/main:flex">
          {TABS.map((tab) => (
            <TabsTrigger
              key={tab.id}
              value={tab.id}
              className="cursor-pointer"
              onClick={() => setTab(tab.id)}
            >
              {tab.title}
            </TabsTrigger>
          ))}
        </TabsList>
      </div>
    </Tabs>
  );
}
