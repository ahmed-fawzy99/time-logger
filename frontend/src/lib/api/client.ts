import axios from 'axios';

const buildBaseURL = () => {
  const apiUrl = import.meta.env.VITE_API_URL;
  const apiPort = import.meta.env.VITE_API_PORT;
  const apiVersion = import.meta.env.VITE_API_VERSION || 'v1';

  if (apiUrl) {
    return `${apiUrl}${apiPort ? ':' + apiPort : ''}/api/${apiVersion}/`;
  }

  // No URL set — use relative path (routed through traefik)
  return `/api/${apiVersion}/`;
};

const api = axios.create({
  baseURL: buildBaseURL(),
  headers: {
    'Content-Type': 'application/json',
  },
});

export default api;
