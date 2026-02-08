import api from './client';

export const fetcher = <T>(url: string): Promise<T> => {
  return api.get<T>(url).then(res => res.data);
};
