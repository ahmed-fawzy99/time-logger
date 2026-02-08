import { zodResolver } from '@hookform/resolvers/zod';
import { format } from 'date-fns';
import { CalendarIcon } from 'lucide-react';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';

import type { TimeEntryResource } from '@/interfaces/entity/time-entry';
import type { GenericErrorResponse } from '@/interfaces/global';
import { createTimeEntry, updateTimeEntry } from '@/lib/data-access/time-entry';
import {
  TIME_ENTRY_SCHEMA,
  type TimeEntryFormType,
} from '@/lib/schema/time-entry';

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
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import useDismissModal from '@/hooks/use-dismiss-modal';
import usePersistedStopWatch from '@/hooks/use-persisted-stopwatch';
import { cn } from '@/utils/cn-utils';
import { combineDateTime } from '@/utils/date-time';
import { parseError } from '@/utils/error-handling';
import { useRouter } from '@tanstack/react-router';
import { toast } from 'sonner';
import { Input } from '../../ui/input';
import FormErrors from './FormErrors';

interface TimeEntryFormProps {
  timeEntry?: TimeEntryResource;
  mode: 'create' | 'edit';
  onSuccess?: () => void;
  timeFrameId: string;
  useStopwatchValue?: boolean;
}

export default function TimeEntryForm({
  timeEntry,
  mode,
  onSuccess,
  timeFrameId,
  useStopwatchValue = false,
}: TimeEntryFormProps) {
  const [serverErrors, setServerErrors] = useState<GenericErrorResponse>({});
  const { dismiss } = useDismissModal();
  const router = useRouter();

  // Helper function to parse datetime string to Date and time string
  const parseDateTime = (dateTimeString?: string) => {
    if (!dateTimeString) return { date: undefined, time: '' };
    const date = new Date(dateTimeString);
    const time = format(date, 'HH:mm');
    return { date, time };
  };

  // Get current date and time for defaults
  const now = new Date();
  const currentTime = format(now, 'HH:mm');

  const startDateTime = parseDateTime(timeEntry?.attributes.startTime);
  const endDateTime = parseDateTime(timeEntry?.attributes.endTime);

  const [startTime, setStartTime] = useState(startDateTime.time || currentTime);
  const [endTime, setEndTime] = useState(endDateTime.time || '');

  const { totalSeconds } = usePersistedStopWatch();

  const form = useForm<TimeEntryFormType>({
    resolver: zodResolver(TIME_ENTRY_SCHEMA),
    defaultValues: {
      time_frame_id: timeFrameId,
      start_time: startDateTime.date || now,
      end_time: endDateTime.date || now,
      description: timeEntry?.attributes.description ?? '',
      billable: timeEntry?.attributes.billable ?? true,
    },
  });

  // When using stopwatch value, prefill start/end times from the persisted stopwatch
  useEffect(() => {
    if (!useStopwatchValue || totalSeconds === 0) return;
    const endDate = new Date();
    const startDate = new Date(endDate.getTime() - totalSeconds * 1000);

    form.setValue('start_time', startDate);
    form.setValue('end_time', endDate);
    setStartTime(format(startDate, 'HH:mm'));
    setEndTime(format(endDate, 'HH:mm'));
  }, [useStopwatchValue, totalSeconds, form]);

  async function onSubmit(values: TimeEntryFormType) {
    setServerErrors({});
    if (!startTime || !endTime) {
      toast.error('Start time and end time are required');
      return;
    }

    try {
      // Combine date and time for API submission
      const startDateTime = combineDateTime(values.start_time, startTime ?? '');
      const endDateTime = combineDateTime(values.end_time, endTime ?? '');

      const payload = {
        time_frame_id: timeFrameId,
        start_time: format(startDateTime, "yyyy-MM-dd'T'HH:mm:ssxxx"),
        end_time: format(endDateTime, "yyyy-MM-dd'T'HH:mm:ssxxx"),
        description: values.description,
        billable: values.billable,
      };

      const response =
        mode === 'edit' && timeEntry
          ? await updateTimeEntry({
              id: timeEntry.id,
              payload,
            })
          : await createTimeEntry({
              payload,
            });

      if (response.status > 299) {
        throw new Error(
          `Failed to ${mode === 'edit' ? 'update' : 'create'} time entry`,
        );
      }

      dismiss();

      toast.success(
        `Time entry ${mode === 'edit' ? 'updated' : 'created'} successfully`,
      );

      onSuccess?.();
      router.invalidate();
    } catch (error) {
      console.error('Error submitting time entry form:', error);
      const { statusCode, errors } = parseError(error);
      toast.error(`Request failed with status code ${statusCode}`);
      setServerErrors((prev) => ({ ...prev, ...errors }));
    }
  }

  return (
    <Form {...form}>
      <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-5">
        {/* Start Date & Time */}
        <FormField
          control={form.control}
          name="start_time"
          render={({ field }) => (
            <FormItem className="flex flex-col">
              <FormLabel>Start Date & Time</FormLabel>
              <div className="flex gap-2">
                <Popover>
                  <PopoverTrigger asChild>
                    <FormControl>
                      <Button
                        variant={'outline'}
                        className={cn(
                          'flex-1 pl-3 text-left font-normal',
                          !field.value && 'text-muted-foreground',
                        )}
                      >
                        {field.value ? (
                          format(field.value, 'PPP')
                        ) : (
                          <span>Pick a date</span>
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
                <Input
                  type="time"
                  value={startTime}
                  onChange={(e) => {
                    setStartTime(e.target.value);
                  }}
                  className="w-32"
                />
              </div>
              <FormMessage />
              {serverErrors.start_time && (
                <p className="text-destructive text-sm">
                  {serverErrors.start_time.join(', ')}
                </p>
              )}
            </FormItem>
          )}
        />

        {/* End Date & Time */}
        <FormField
          control={form.control}
          name="end_time"
          render={({ field }) => (
            <FormItem className="flex flex-col">
              <FormLabel>End Date & Time</FormLabel>
              <div className="flex gap-2">
                <Popover>
                  <PopoverTrigger asChild>
                    <FormControl>
                      <Button
                        variant={'outline'}
                        className={cn(
                          'flex-1 pl-3 text-left font-normal',
                          !field.value && 'text-muted-foreground',
                        )}
                      >
                        {field.value ? (
                          format(field.value, 'PPP')
                        ) : (
                          <span>Pick a date</span>
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
                        form.watch('start_time')
                          ? date < form.watch('start_time')
                          : false
                      }
                      initialFocus
                    />
                  </PopoverContent>
                </Popover>
                <Input
                  type="time"
                  value={endTime}
                  onChange={(e) => setEndTime(e.target.value)}
                  className="w-32"
                />
              </div>
              <FormMessage />
              {serverErrors.end_time && (
                <p className="text-destructive text-sm">
                  {serverErrors.end_time.join(', ')}
                </p>
              )}
            </FormItem>
          )}
        />

        {/* Billable Switch */}
        <FormField
          control={form.control}
          name="billable"
          render={({ field }) => (
            <FormItem className="flex flex-row items-center justify-between">
              <FormLabel>Billable</FormLabel>
              <FormControl>
                <Switch
                  checked={field.value}
                  onCheckedChange={field.onChange}
                />
              </FormControl>
            </FormItem>
          )}
        />

        {/* Description */}
        <FormField
          control={form.control}
          name="description"
          render={({ field }) => (
            <FormItem>
              <FormLabel>Description (optional)</FormLabel>
              <FormControl>
                <Textarea
                  placeholder="What did you work on?"
                  className="resize-none"
                  rows={4}
                  {...field}
                  value={field.value ?? ''}
                />
              </FormControl>
              <FormMessage />
              {serverErrors.description && (
                <p className="text-destructive text-sm">
                  {serverErrors.description.join(', ')}
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
                ? 'Update Time Entry'
                : 'Create Time Entry'}
          </Button>
        </div>
      </form>
    </Form>
  );
}
