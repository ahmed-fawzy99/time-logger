import type { SWRConfiguration } from 'swr';

export const DEFAULT_API_PAGE_SIZE = 25;

export const SWR_CONFIG: SWRConfiguration = {
  // Revalidation
  revalidateOnFocus: false,
  revalidateOnReconnect: false,
  revalidateIfStale: true,

  // Deduplication interval: prevent duplicate requests within this window (ms)
  dedupingInterval: 10000,

  // Cache lifespan: data is considered fresh for this duration (ms)
  focusThrottleInterval: 5000,

  // Error handling
  shouldRetryOnError: true,
  errorRetryCount: 3,
  errorRetryInterval: 5000,

  // Performance optimizations
  keepPreviousData: true,

  // Fallback data while revalidating
  revalidateOnMount: true,
};

export const SWR_CACHE_KEYS = {
  PROJECTS: 'projects',
  PREFERENCES: 'preferences',
  TIME_FRAMES: 'timeFrames',
  TIME_ENTRIES: 'timeEntries',
};
