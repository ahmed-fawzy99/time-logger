import { zodResolver } from '@hookform/resolvers/zod';
import { useState } from 'react';
import { useForm } from 'react-hook-form';

import type { PreferenceResource } from '@/interfaces/entity/preference';
import type { GenericErrorResponse } from '@/interfaces/global';
import { updatePreferences } from '@/lib/data-access/preference';
import {
  PREFERNCE_SCHEMA,
  type PreferenceForm as PreferenceFormType,
} from '@/lib/schema/preference';

import { Button } from '@/components/ui/button';
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
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { SWR_CACHE_KEYS } from '@/config';
import { DAYS_OF_WEEK } from '@/lib/data-access/form-constants';
import { parseError } from '@/utils/error-handling';
import { toast } from 'sonner';
import { useSWRConfig } from 'swr';
import FormErrors from './FormErrors';

interface PreferenceFormProps {
  curPreferences: PreferenceResource;
}

export default function PreferenceForm({
  curPreferences,
}: PreferenceFormProps) {
  const [serverErrors, setServerErrors] = useState<GenericErrorResponse>({});
  const { mutate } = useSWRConfig();
  const form = useForm<PreferenceFormType>({
    resolver: zodResolver(PREFERNCE_SCHEMA),
    defaultValues: {
      week_start: curPreferences.attributes.weekStart,
      currency: curPreferences.attributes.currency,
      hourly_rate: curPreferences.attributes.hourlyRate,
      // roundDurationTo: curPreferences.attributes.roundDurationTo,
      // roundMethod: curPreferences.attributes.roundMethod,
      invoiceName: curPreferences.attributes.invoiceName,
      invoiceTitle: curPreferences.attributes.invoiceTitle,
      invoiceAddress: curPreferences.attributes.invoiceAddress,
    },
  });

  async function onSubmit(values: PreferenceFormType) {
    setServerErrors({});

    try {
      const response = await updatePreferences({
        id: curPreferences.id,
        payload: values,
      });

      if (response.status > 299) {
        throw new Error('Failed to update preferences');
      }

      mutate(SWR_CACHE_KEYS.PREFERENCES);
      toast.success('Preferences updated successfully');
    } catch (error) {
      const { statusCode, errors } = parseError(error);
      toast.error(`Request failed with status code ${statusCode}`);
      setServerErrors((prev) => ({ ...prev, ...errors }));
    }
  }

  return (
    <Form {...form}>
      <div className="max-w-2xl space-y-4">
        <div className="space-y-1">
          <h1 className="title">System Preferences</h1>
          <p className="text-muted-foreground">
            You can set your system preferences here. These preferences will be
            used as defaults when creating new time frames and generating
            invoices.
          </p>
        </div>
        <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-5">
          <FormField
            control={form.control}
            name="week_start"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Week Start Day</FormLabel>
                <Select
                  onValueChange={field.onChange}
                  defaultValue={field.value}
                >
                  <FormControl>
                    <SelectTrigger>
                      <SelectValue placeholder="Select a day" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent>
                    {DAYS_OF_WEEK.map((day) => (
                      <SelectItem key={day.value} value={day.value}>
                        {day.label}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
                <FormMessage />
                {serverErrors.weekStart && (
                  <p className="text-destructive text-sm">
                    {serverErrors.weekStart.join(', ')}
                  </p>
                )}
              </FormItem>
            )}
          />

          <FormField
            control={form.control}
            name="currency"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Default Currency</FormLabel>
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

          <FormField
            control={form.control}
            name="invoiceName"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Invoice Name (optional)</FormLabel>
                <FormControl>
                  <Input
                    placeholder="Your Name"
                    {...field}
                    value={field.value ?? ''}
                  />
                </FormControl>
                <FormMessage />
                {serverErrors.invoiceName && (
                  <p className="text-destructive text-sm">
                    {serverErrors.invoiceName.join(', ')}
                  </p>
                )}
              </FormItem>
            )}
          />

          <FormField
            control={form.control}
            name="invoiceTitle"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Invoice Title (optional)</FormLabel>
                <FormControl>
                  <Input
                    placeholder="Job Title"
                    {...field}
                    value={field.value ?? ''}
                  />
                </FormControl>
                <FormMessage />
                {serverErrors.invoiceTitle && (
                  <p className="text-destructive text-sm">
                    {serverErrors.invoiceTitle.join(', ')}
                  </p>
                )}
              </FormItem>
            )}
          />

          <FormField
            control={form.control}
            name="invoiceAddress"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Invoice Address (optional)</FormLabel>
                <FormControl>
                  <Input
                    placeholder="Your Address"
                    {...field}
                    value={field.value ?? ''}
                  />
                </FormControl>
                <FormMessage />
                {serverErrors.invoiceAddress && (
                  <p className="text-destructive text-sm">
                    {serverErrors.invoiceAddress.join(', ')}
                  </p>
                )}
              </FormItem>
            )}
          />

          <FormErrors errors={serverErrors} />
          <div className="pt-2">
            <Button type="submit" disabled={form.formState.isSubmitting}>
              {form.formState.isSubmitting ? 'Saving...' : 'Save Preferences'}
            </Button>
          </div>
        </form>
      </div>
    </Form>
  );
}
