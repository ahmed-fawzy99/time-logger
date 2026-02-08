import { DEFAULT_API_PAGE_SIZE } from '@/config';
import type {
  TimeFrame,
  TimeFrameResource,
  TimeFrames,
  TimeFrameStatus,
} from '@/interfaces/entity/time-frame';
import type {
  GenericApiPagniatedRequestParams,
  GenericApiRes,
  GenericApiSignleRequestParams,
} from '@/interfaces/global';
import { buildQueryString } from '@/utils/request';
import { fetcher } from '../api/fetcher';
import { deleteResource, postResource, putResource } from '../api/mutations';
import type { TimeFrameApiPayload } from '../schema/time-frame';

export async function getTimeFrames({
  pageSize,
  pageNumber,
  sort,
  include,
  add,
  status,
  projectId,
}: GenericApiPagniatedRequestParams & {
  projectId?: string;
  status?: TimeFrameStatus;
} = {}): Promise<TimeFrames> {
  const params = buildQueryString({
    'page[size]': pageSize?.toString() ?? DEFAULT_API_PAGE_SIZE.toString(),
    'page[number]': pageNumber?.toString() ?? '1',
    sort: sort ?? '',
    include: include ?? '',
    'filter[status]': status ?? '',
    'filter[projectId]': projectId ?? '',
    add: add ?? '',
  });
  return await fetcher<TimeFrames>(`/time-frames${params}`);
}

export async function getTimeFrame({
  identifier,
  include,
  add,
}: GenericApiSignleRequestParams): Promise<TimeFrameResource> {
  const params = buildQueryString({
    include: include ?? '',
    add: add ?? '',
  });
  return (await fetcher<TimeFrame>(`/time-frames/${identifier}${params}`)).data;
}

export async function createTimeFrame({
  payload,
}: {
  payload: TimeFrameApiPayload;
}): Promise<TimeFrame> {
  return await postResource<TimeFrame>(`/time-frames`, payload);
}

export async function updateTimeFrame({
  id,
  payload,
}: {
  id: string;
  payload: TimeFrameApiPayload;
}): Promise<TimeFrame> {
  return await putResource<TimeFrame>(`/time-frames/${id}`, payload);
}

export async function deleteTimeFrame({
  id,
}: {
  id: string;
}): Promise<GenericApiRes> {
  return await deleteResource<GenericApiRes>(`/time-frames/${id}`);
}

export async function getTimeFrameInvoice({
  identifier,
}: {
  identifier: string;
}): Promise<GenericApiRes<{ invoiceUrl: string }>> {
  return await fetcher<GenericApiRes<{ invoiceUrl: string }>>(
    `/time-frames/${identifier}/invoice`,
  );
}
