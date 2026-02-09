import { z } from 'zod';

export const TIMEFRAME_SCHEMA = z
  .object({
    project_id: z.string(),
    name: z.string().optional(),
    start_date: z.date('Start date is required'),
    end_date: z.date('End date is required'),
    status: z.enum(['done', 'in_progress', 'canceled']),
    notes: z.string().optional(),

    currency: z.string().min(1, 'Currency is required'),
    hourly_rate: z.number().positive('Hourly rate must be positive'),
  })
  .refine((data) => data.end_date >= data.start_date, {
    message: 'End date must be after or equal to start date',
    path: ['end_date'],
  });

export type TimeFrameFormType = z.infer<typeof TIMEFRAME_SCHEMA>;

// API payload type with string dates
export type TimeFrameApiPayload = Omit<
  TimeFrameFormType,
  'start_date' | 'end_date'
> & {
  start_date: string;
  end_date: string;
};
