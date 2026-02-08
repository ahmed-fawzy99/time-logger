import { DEFAULT_API_PAGE_SIZE } from '@/config';
import type {
  Project,
  ProjectResource,
  Projects,
} from '@/interfaces/entity/project';
import type {
  GenericApiPagniatedRequestParams,
  GenericApiRes,
  GenericApiSignleRequestParams,
} from '@/interfaces/global';
import { buildQueryString } from '@/utils/request';
import { fetcher } from '../api/fetcher';
import { deleteResource, postResource, putResource } from '../api/mutations';
import type { ProjectFormType } from '../schema/project';

export async function getProjects({
  pageSize,
  pageNumber,
  sort,
  include,
  add,
}: GenericApiPagniatedRequestParams = {}): Promise<Projects> {
  const params = buildQueryString({
    'page[size]': pageSize?.toString() ?? DEFAULT_API_PAGE_SIZE.toString(),
    'page[number]': pageNumber?.toString() ?? '1',
    sort: sort ?? '',
    include: include ?? '',
    add: add ?? '',
  });
  return await fetcher<Projects>(`/projects${params}`);
}

export async function getProject({
  identifier,
  include,
  add,
}: GenericApiSignleRequestParams): Promise<ProjectResource> {
  const params = buildQueryString({
    include: include ?? '',
    add: add ?? '',
  });
  return (await fetcher<Project>(`/projects/${identifier}${params}`)).data;
}

export async function createProject({
  payload,
}: {
  payload: ProjectFormType;
}): Promise<Project> {
  return await postResource<Project>(`/projects`, payload);
}

export async function updateProject({
  id,
  payload,
}: {
  id: string;
  payload: ProjectFormType;
}): Promise<Project> {
  return await putResource<Project>(`/projects/${id}`, payload);
}

export async function deleteProject({
  id,
}: {
  id: string;
}): Promise<GenericApiRes> {
  return await deleteResource<GenericApiRes>(`/projects/${id}`);
}
