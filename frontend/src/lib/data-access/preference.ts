import type {
  Preference,
  PreferenceResource,
} from '@/interfaces/entity/preference';
import type { GenericApiRes } from '@/interfaces/global';
import { fetcher } from '../api/fetcher';
import { putResource } from '../api/mutations';
import type { PreferenceForm } from '../schema/preference';

export async function getPreferences(): Promise<PreferenceResource> {
  const response = await fetcher<Preference>(`/preferences`);
  return response.data;
}

export async function updatePreferences({
  id,
  payload,
}: {
  id: string;
  payload: PreferenceForm;
}): Promise<GenericApiRes> {
  const { week_start, currency, hourly_rate, ...additional_properties } =
    payload;
  return await putResource<GenericApiRes>(`/preferences/${id}`, {
    week_start: week_start,
    currency: currency,
    hourly_rate: hourly_rate,
    additional_properties: additional_properties,
  });
}
