import { DEFAULT_API_PAGE_SIZE } from '@/config';
import type {
  TimeEntry,
  TimeEntryResource,
} from '@/interfaces/entity/time-entry';
import type {
  GenericApiPagniatedRequestParams,
  GenericApiRes,
  GenericApiSignleRequestParams,
} from '@/interfaces/global';
import { buildQueryString } from '@/utils/request';
import { fetcher } from '../api/fetcher';
import { deleteResource, postResource, putResource } from '../api/mutations';
import type { TimeEntryApiPayload } from '../schema/time-entry';

export async function getTimeEntries({
  pageSize,
  pageNumber,
  sort,
  include,
  add,
}: GenericApiPagniatedRequestParams = {}): Promise<TimeEntry> {
  const params = buildQueryString({
    'page[size]': pageSize?.toString() ?? DEFAULT_API_PAGE_SIZE.toString(),
    'page[number]': pageNumber?.toString() ?? '1',
    sort: sort ?? '',
    include: include ?? '',
    add: add ?? '',
  });
  return await fetcher<TimeEntry>(`/time-entries${params}`);
}

export async function getTimeEntry({
  identifier,
  include,
  add,
}: GenericApiSignleRequestParams): Promise<TimeEntryResource> {
  const params = buildQueryString({
    include: include ?? '',
    add: add ?? '',
  });
  return (await fetcher<TimeEntry>(`/time-entries/${identifier}${params}`))
    .data;
}

export async function createTimeEntry({
  payload,
}: {
  payload: TimeEntryApiPayload;
}): Promise<TimeEntry> {
  return await postResource<TimeEntry>(`/time-entries`, payload);
}

export async function updateTimeEntry({
  id,
  payload,
}: {
  id: string;
  payload: TimeEntryApiPayload;
}): Promise<TimeEntry> {
  return await putResource<TimeEntry>(`/time-entries/${id}`, payload);
}

export async function deleteTimeEntry({
  id,
}: {
  id: string;
}): Promise<GenericApiRes> {
  return await deleteResource<GenericApiRes>(`/time-entries/${id}`);
}
