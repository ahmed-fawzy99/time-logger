import { zodResolver } from '@hookform/resolvers/zod';
import { addWeeks, format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';
import { useState } from 'react';
import { useForm } from 'react-hook-form';

import type { TimeFrameResource } from '@/interfaces/entity/time-frame';
import type { GenericErrorResponse } from '@/interfaces/global';
import { createTimeFrame, updateTimeFrame } from '@/lib/data-access/time-frame';
import {
  TIMEFRAME_SCHEMA,
  type TimeFrameFormType,
} from '@/lib/schema/time-frame';

import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { DEFAULT_API_PAGE_SIZE, SWR_CACHE_KEYS } from '@/config';
import useDismissModal from '@/hooks/use-dismiss-modal';
import { TIMEFRAME_STATUSES } from '@/lib/data-access/form-constants';
import { usePreferences } from '@/providers/PreferencesProvider';
import { cn } from '@/utils/cn-utils';
import { parseError } from '@/utils/error-handling';
import { useNavigate } from '@tanstack/react-router';
import { useQueryState } from 'nuqs';
import { toast } from 'sonner';
import { useSWRConfig } from 'swr';
import FormErrors from './FormErrors';

interface TimeFrameFormProps {
  projectId: string;
  timeFrame?: TimeFrameResource;
  mode: 'create' | 'edit';
  onSuccess?: () => void;
}

export default function TimeFrameForm({
  projectId,
  timeFrame,
  mode,
  onSuccess,
}: TimeFrameFormProps) {
  const [serverErrors, setServerErrors] = useState<GenericErrorResponse>({});
  const { mutate } = useSWRConfig();
  const { dismiss } = useDismissModal();
  const navigate = useNavigate();
  const [tab] = useQueryState('tab', { defaultValue: 'all' });
  const [pageNumber] = useQueryState('pageNumber', { defaultValue: '1' });
  const [pageSize] = useQueryState('pageSize', {
    defaultValue: DEFAULT_API_PAGE_SIZE.toString(),
  });
  const { preferences } = usePreferences();
  const form = useForm<TimeFrameFormType>({
    resolver: zodResolver(TIMEFRAME_SCHEMA),
    defaultValues: {
      project_id: projectId,
      name: timeFrame?.attributes.name ?? '',
      start_date: timeFrame?.attributes.startDate
        ? new Date(timeFrame.attributes.startDate)
        : new Date(),
      end_date: timeFrame?.attributes.endDate
        ? new Date(timeFrame.attributes.endDate)
        : undefined,
      // : undefined,
      status: timeFrame?.attributes.status ?? 'in_progress',
      notes: timeFrame?.attributes.notes ?? '',
      currency:
        timeFrame?.attributes.currency ??
        preferences?.attributes.currency ??
        '',
      hourly_rate:
        timeFrame?.attributes.hourlyRate ??
        preferences?.attributes.hourlyRate ??
        undefined,
    },
  });

  async function onSubmit(values: TimeFrameFormType) {
    setServerErrors({});

    try {
      const payload = {
        ...values,
        project_id: projectId,
        start_date: format(values.start_date, 'yyyy-MM-dd'),
        end_date: format(values.end_date, 'yyyy-MM-dd'),
      };

      const response =
        mode === 'edit' && timeFrame
          ? await updateTimeFrame({
              id: timeFrame.id,
              payload,
            })
          : await createTimeFrame({
              payload,
            });

      if (response.status > 299) {
        throw new Error(
          `Failed to ${mode === 'edit' ? 'update' : 'create'} timeFrame`,
        );
      }

      dismiss();

      toast.success(
        `${mode === 'edit' ? 'Updated' : 'Created'} successfully`,
        mode === 'create'
          ? {
              action: {
                label: 'View Time Frame?',
                onClick: () =>
                  navigate({
                    to: `./${response.data.id}`,
                  }),
              },
              duration: 5000,
            }
          : undefined,
      );

      mutate([
        SWR_CACHE_KEYS.TIME_FRAMES,
        tab,
        pageNumber,
        pageSize,
        projectId,
      ]);

      onSuccess?.();
    } catch (error) {
      const { statusCode, errors } = parseError(error);
      toast.error(`Request failed with status code ${statusCode}`);
      setServerErrors((prev) => ({ ...prev, ...errors }));
    }
  }

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-5">
        <FormField
          control={form.control}
          name="name"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Name (optional)</FormLabel>
              <FormControl>
                <Input
                  placeholder="Sprint 99"
                  {...field}
                  value={field.value ?? ''}
                />
              </FormControl>
              <FormMessage />
              {serverErrors.name && (
                <p className="text-destructive text-sm">
                  {serverErrors.name.join(', ')}
                </p>
              )}
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="start_date"
          render={({ field }) => (
            <FormItem className="flex flex-col">
              <FormLabel>Start Date</FormLabel>
              <Popover>
                <PopoverTrigger asChild>
                  <FormControl>
                    <Button
                      variant={'outline'}
                      className={cn(
                        'w-full pl-3 text-left font-normal',
                        !field.value && 'text-muted-foreground',
                      )}
                    >
                      {field.value ? (
                        format(field.value, 'PPP')
                      ) : (
                        <span>Pick a starting date</span>
                      )}
                      <CalendarIcon className="ml-auto h-4 w-4 opacity-50" />
                    </Button>
                  </FormControl>
                </PopoverTrigger>
                <PopoverContent className="w-auto p-0" align="start">
                  <Calendar
                    mode="single"
                    selected={field.value}
                    onSelect={(date) => date && field.onChange(date)}
                    initialFocus
                  />
                </PopoverContent>
              </Popover>
              <FormMessage />
              {serverErrors.start_date && (
                <p className="text-destructive text-sm">
                  {serverErrors.start_date.join(', ')}
                </p>
              )}
            </FormItem>
          )}
        />

        <FormField
          control={form.control}
          name="end_date"
          render={({ field }) => {
            const startDate = form.watch('start_date');

            return (
              <FormItem className="flex flex-col">
                <FormLabel>End Date</FormLabel>
                <Popover>
                  <PopoverTrigger asChild>
                    <FormControl>
                      <Button
                        variant={'outline'}
                        className={cn(
                          'w-full pl-3 text-left font-normal',
                          !field.value && 'text-muted-foreground',
                        )}
                      >
                        {field.value ? (
                          format(field.value, 'PPP')
                        ) : (
                          <span>Pick an ending date</span>
                        )}
                        <CalendarIcon className="ml-auto h-4 w-4 opacity-50" />
                      </Button>
                    </FormControl>
                  </PopoverTrigger>
                  <PopoverContent className="w-auto p-0" align="start">
                    <Calendar
                      mode="single"
                      selected={field.value}
                      onSelect={(date) => date && field.onChange(date)}
                      disabled={(date) =>
                        form.watch('start_date')
                          ? date < form.watch('start_date')
                          : false
                      }
                      initialFocus
                    />
                  </PopoverContent>
                </Popover>
                {!!startDate && (
                  <div className="flex gap-2">
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      onClick={() =>
                        field.onChange(addWeeks(field.value || startDate, 1))
                      }
                    >
                      + 1 week
                    </Button>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      onClick={() =>
                        field.onChange(addWeeks(field.value || startDate, 2))
                      }
                    >
                      + 2 weeks
                    </Button>
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      onClick={() =>
                        field.onChange(addWeeks(field.value || startDate, 4))
                      }
                    >
                      + 4 weeks
                    </Button>
                  </div>
                )}
                <FormMessage />
                {serverErrors.end_date && (
                  <p className="text-destructive text-sm">
                    {serverErrors.end_date.join(', ')}
                  </p>
                )}
              </FormItem>
            );
          }}
        />

        <FormField
          control={form.control}
          name="status"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Status</FormLabel>
              <Select onValueChange={field.onChange} defaultValue={field.value}>
                <FormControl>
                  <SelectTrigger>
                    <SelectValue placeholder="Select a status" />
                  </SelectTrigger>
                </FormControl>
                <SelectContent>
                  {TIMEFRAME_STATUSES.map((status) => (
                    <SelectItem key={status.value} value={status.value}>
                      {status.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
              <FormMessage />
              {serverErrors.status && (
                <p className="text-destructive text-sm">
                  {serverErrors.status.join(', ')}
                </p>
              )}
            </FormItem>
          )}
        />

        <div className="grid grid-cols-2 gap-2 py-4 px-2 bg-accent rounded-lg">
          <FormField
            control={form.control}
            name="currency"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Currency</FormLabel>
                <FormControl>
                  <Input placeholder="USD" {...field} />
                </FormControl>
                <FormMessage />
                {serverErrors.currency && (
                  <p className="text-destructive text-sm">
                    {serverErrors.currency.join(', ')}
                  </p>
                )}
              </FormItem>
            )}
          />

          <FormField
            control={form.control}
            name="hourly_rate"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Hourly Rate</FormLabel>
                <FormControl>
                  <Input
                    type="number"
                    step="0.01"
                    placeholder="50.00"
                    {...field}
                    value={field.value ?? ''}
                    onChange={(e) => {
                      const value = e.target.value;
                      field.onChange(value === '' ? undefined : Number(value));
                    }}
                  />
                </FormControl>
                <FormMessage />
                {serverErrors.hourlyRate && (
                  <p className="text-destructive text-sm">
                    {serverErrors.hourlyRate.join(', ')}
                  </p>
                )}
              </FormItem>
            )}
          />
          <p className="text-sm text-muted-foreground col-span-2">
            You can override your default rate for this time frame by entering a
            different value here.
          </p>
        </div>

        <FormField
          control={form.control}
          name="notes"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Notes (optional)</FormLabel>
              <FormControl>
                <Textarea
                  placeholder="Add any notes about this timeFrame..."
                  className="resize-none"
                  {...field}
                  value={field.value ?? ''}
                />
              </FormControl>
              <FormMessage />
              {serverErrors.notes && (
                <p className="text-destructive text-sm">
                  {serverErrors.notes.join(', ')}
                </p>
              )}
            </FormItem>
          )}
        />

        <FormErrors errors={serverErrors} />
        <div className="flex items-center justify-end gap-2">
          <Button type="button" variant="outline" onClick={dismiss}>
            Close
          </Button>
          <Button type="submit" disabled={form.formState.isSubmitting}>
            {form.formState.isSubmitting
              ? mode === 'edit'
                ? 'Updating...'
                : 'Creating...'
              : mode === 'edit'
                ? 'Update Time Frame'
                : 'Create Time Frame'}
          </Button>
        </div>
      </form>
    </Form>
  );
}
