// preferences-context.tsx
import { SWR_CACHE_KEYS } from '@/config';
import type { PreferenceResource } from '@/interfaces/entity/preference';
import { getPreferences } from '@/lib/data-access/preference';
import { createContext, useContext } from 'react';
import useSWR from 'swr';

type PreferencesContextType = {
  preferences?: PreferenceResource;
  loading: boolean;
};

const PreferencesContext = createContext<PreferencesContextType | undefined>(
  undefined,
);

export function PreferencesProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  const { data: preferences, isLoading } = useSWR(
    [SWR_CACHE_KEYS.PREFERENCES],
    getPreferences,
  );

  return (
    <PreferencesContext.Provider value={{ preferences, loading: isLoading }}>
      {children}
    </PreferencesContext.Provider>
  );
}

export function usePreferences() {
  const ctx = useContext(PreferencesContext);
  if (!ctx) {
    throw new Error('usePreferences must be used inside PreferencesProvider');
  }
  return ctx;
}
