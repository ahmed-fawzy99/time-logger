import { z } from 'zod';

export const PROJECT_SCHEMA = z.object({
  name: z.string().min(1, 'Project name is required'),
  description: z.string().optional(),
});

export type ProjectFormType = z.infer<typeof PROJECT_SCHEMA>;
