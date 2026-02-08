import { zodResolver } from '@hookform/resolvers/zod';
import { useState } from 'react';
import { Controller, useForm } from 'react-hook-form';

import type { GenericErrorResponse } from '@/interfaces/global';

import { Button } from '@/components/ui/button';
import { Form } from '@/components/ui/form';
import { DEFAULT_API_PAGE_SIZE, SWR_CACHE_KEYS } from '@/config';
import useDismissModal from '@/hooks/use-dismiss-modal';
import type { ProjectResource } from '@/interfaces/entity/project';
import { createProject, updateProject } from '@/lib/data-access/project';
import { PROJECT_SCHEMA, type ProjectFormType } from '@/lib/schema/project';
import { parseError } from '@/utils/error-handling';
import { useNavigate } from '@tanstack/react-router';
import { useQueryState } from 'nuqs';
import { toast } from 'sonner';
import { useSWRConfig } from 'swr';
import { Field, FieldError, FieldLabel } from '../../ui/field';
import { Input } from '../../ui/input';
import { Textarea } from '../../ui/textarea';
import FormErrors from './FormErrors';

interface ProjectFormProps {
  project?: ProjectResource;
  mode: 'create' | 'edit';
  onSuccess?: () => void;
}

export default function ProjectForm({
  project,
  mode,
  onSuccess,
}: ProjectFormProps) {
  const [serverErrors, setServerErrors] = useState<GenericErrorResponse>({});
  const { dismiss } = useDismissModal();
  const navigate = useNavigate();
  const { mutate } = useSWRConfig();
  const [tab] = useQueryState('tab', { defaultValue: 'all' });
  const [pageNumber] = useQueryState('pageNumber', { defaultValue: '1' });
  const [pageSize] = useQueryState('pageSize', {
    defaultValue: DEFAULT_API_PAGE_SIZE.toString(),
  });

  const form = useForm<ProjectFormType>({
    resolver: zodResolver(PROJECT_SCHEMA),
    defaultValues: {
      name: project?.attributes.name || '',
      description: project?.attributes.description || '',
    },
  });

  async function onSubmit(values: ProjectFormType) {
    setServerErrors({});
    try {
      const payload = {
        name: values.name,
        description: values.description,
      };

      const response =
        mode === 'edit' && project
          ? await updateProject({
              id: project.id,
              payload,
            })
          : await createProject({
              payload,
            });

      if (response.status > 299) {
        throw new Error(
          `Failed to ${mode === 'edit' ? 'update' : 'create'} project`,
        );
      }

      dismiss();

      toast.success(
        `Project ${mode === 'edit' ? 'updated' : 'created'} successfully`,
        mode === 'create'
          ? {
              action: {
                label: 'View Project?',
                onClick: () =>
                  navigate({
                    to: `/projects/${response.data.id}`,
                  }),
              },
              duration: 5000,
            }
          : undefined,
      );

      mutate([SWR_CACHE_KEYS.PROJECTS, tab, pageNumber, pageSize]);

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
        <Controller
          name="name"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor={field.name}>Project Name</FieldLabel>
              <Input
                {...field}
                id={field.name}
                type="text"
                aria-invalid={fieldState.invalid}
                placeholder="Kittens United"
              />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
          )}
        />
        <Controller
          name="description"
          control={form.control}
          render={({ field, fieldState }) => (
            <Field data-invalid={fieldState.invalid}>
              <FieldLabel htmlFor={field.name}>
                Project Description (optional)
              </FieldLabel>
              <Textarea
                {...field}
                id={field.name}
                aria-invalid={fieldState.invalid}
                placeholder="Give the kittens some love. Oh, I mean, describe the project here..."
              />
              {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
            </Field>
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
                ? 'Update Project'
                : 'Create Project'}
          </Button>
        </div>
      </form>
    </Form>
  );
}
