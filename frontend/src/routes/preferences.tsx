import PreferenceForm from '@/components/custom/forms/PreferenceForm';
import { SWR_CACHE_KEYS } from '@/config';
import { getPreferences } from '@/lib/data-access/preference';
import { createFileRoute } from '@tanstack/react-router';
import useSWR from 'swr';

export const Route = createFileRoute('/preferences')({
  component: RouteComponent,
});

function RouteComponent() {
  const {
    data: curPreferences,
    error,
    isLoading,
  } = useSWR(SWR_CACHE_KEYS.PREFERENCES, getPreferences);

  if (isLoading) return <div>Loading...</div>;
  if (error || !curPreferences)
    return <div>Error: {error.message || JSON.stringify(error)}</div>;

  return (
    <div>
      <PreferenceForm curPreferences={curPreferences} />
    </div>
  );
}
