import type { ValidationErrors } from '@/interfaces/global';
import { AxiosError } from 'axios';

type SuccessResult<T> = readonly [T, null];
type ErrorResult<E = Error> = readonly [null, E];
type Result<T, E = Error> = SuccessResult<T> | ErrorResult<E>;

export async function tryCatch<T, E = Error>(
  promise: Promise<T>,
): Promise<Result<T, E>> {
  try {
    const data = await promise;
    return [data, null] as const;
  } catch (error) {
    return [null, error as E] as const;
  }
}

type errorResponse = {
  message: string;
  statusCode: number;
  errors?: ValidationErrors;
};
export function parseError(error: unknown): errorResponse {
  let { message, statusCode, errors } = {
    message: 'An unexpected error occurred',
    statusCode: 500,
    errors: undefined,
  };

  if (error instanceof AxiosError) {
    statusCode = error.response?.status || 500;
    message =
      error.response?.data?.message ||
      `Request failed with status code ${statusCode}`;
    errors = error.response?.data?.errors;
  }

  return {
    message: message,
    statusCode: statusCode,
    errors: errors,
  };
}
