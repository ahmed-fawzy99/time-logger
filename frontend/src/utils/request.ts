/**
 * Builds a query string from a given object of key-value pairs.
 *
 * Example: buildQueryString({ a: '1', b: '2' }) returns '?a=1&b=2'
 *
 * @param {Record<string, string>} params - An object containing key-value pairs. Empty values will be excluded from the query string.
 * @returns {string} The built query string.
 */
export function buildQueryString(
  params: Record<string, string | string[]>,
): string {
  const query = Object.entries(params)
    .filter(([, value]) => {
      if (Array.isArray(value)) return value.length > 0;
      return value !== '';
    })
    .map(([key, value]) => {
      if (Array.isArray(value)) {
        return `${key}=${value.join(',')}`;
      }
      return `${key}=${value}`;
    })
    .join('&');

  return query ? `?${query}` : '';
}
