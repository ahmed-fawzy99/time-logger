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
  invoicePrimaryColor: z.union([
    z.string().regex(/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/, 'Invalid HEX color code'),
    z.literal(''),
    z.null(),
  ]),
});

export type PreferenceForm = z.infer<typeof PREFERNCE_SCHEMA>;
