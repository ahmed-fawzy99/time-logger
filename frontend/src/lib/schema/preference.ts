import { z } from 'zod';

export const PREFERNCE_SCHEMA = z.object({
  week_start: z.string().min(1, 'Week start day is required'),
  currency: z.string().min(1, 'Currency is required'),
  hourly_rate: z.number().positive('Hourly rate must be positive'),
  roundDurationTo: z.number().min(0).max(60).int().optional(),
  roundMethod: z.enum(['up', 'down', 'nearest']).optional(),

  invoiceName: z.string().max(255).nullable(),
  invoiceTitle: z.string().max(255).nullable(),
  invoiceAddress: z.string().max(255).nullable(),
});

export type PreferenceForm = z.infer<typeof PREFERNCE_SCHEMA>;
