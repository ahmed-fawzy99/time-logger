import type { ApiResource, PaginatedApiRes, SingleApiRes } from '../global';

export interface PreferenceAttrs {
  weekStart: string;
  currency: string;
  hourlyRate: number;
  roundDurationTo?: number;
  roundMethod?: 'up' | 'down' | 'nearest';
  invoiceName?: string;
  invoiceTitle?: string;
  invoiceAddress?: string;
  invoicePrimaryColor?: string;

  createdAt: string;
  updatedAt: string;
}

export type Preference = SingleApiRes<PreferenceAttrs, undefined>;
export type Preferences = PaginatedApiRes<PreferenceAttrs, undefined>;
export type PreferenceResource = ApiResource<PreferenceAttrs, undefined>;
