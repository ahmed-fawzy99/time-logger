import api from './client';

export const postResource = async <T>(
  url: string,
  body: object,
): Promise<T> => {
  const response = await api.post(url, body);
  return response.data;
};

export const putResource = async <T>(url: string, body: object): Promise<T> => {
  const response = await api.put(url, body);
  return response.data;
};

export const patchResource = async <T>(
  url: string,
  body: object,
): Promise<T> => {
  const response = await api.patch(url, body);
  return response.data;
};

export const deleteResource = async <T>(url: string): Promise<T> => {
  const response = await api.delete(url);
  return response.data;
};
