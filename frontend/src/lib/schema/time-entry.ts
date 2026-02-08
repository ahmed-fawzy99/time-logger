import { z } from 'zod';

export const TIME_ENTRY_SCHEMA = z
  .object({
    time_frame_id: z.string(),
    start_time: z.date(),
    end_time: z.date(),
    description: z.string().optional(),
    billable: z.boolean(),
  })
  .refine((data) => data.end_time >= data.start_time, {
    message: 'End time must be after or equal to start time',
    path: ['end_time'],
  });

export type TimeEntryFormType = z.infer<typeof TIME_ENTRY_SCHEMA>;

// API payload type with string dates
export type TimeEntryApiPayload = Omit<
  TimeEntryFormType,
  'start_time' | 'end_time'
> & {
  time_frame_id: string;
  start_time: string;
  end_time: string;
};
