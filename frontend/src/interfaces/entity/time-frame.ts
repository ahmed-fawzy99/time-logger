import type {
  ApiResource,
  GenericRelationship,
  PaginatedApiRes,
  SingleApiRes,
} from '../global';
import type { ProjectResource } from './project';
import type { TimeEntryResource } from './time-entry';

export type TimeFrameStatus = 'done' | 'in_progress' | 'canceled';

export interface TimeFrameAttrs {
  startDate: string;
  endDate: string;
  name: string;
  status: TimeFrameStatus;
  notes: string;
  periodDurationInDays: number;
  daysTracked: number;
  totalRecordedDurationInMinutes: string;
  entriesCount: string;
  averageDailyDurationInMinutes?: string;
  totalBillable?: string;
  invoiceUrl?: string;
  createdAt: string;
  updatedAt: string;
}

export interface TimeFrameInc {
  timeEntries?: TimeEntryResource[];
  project?: ProjectResource;
}

export interface TimeFrameRel {
  project: GenericRelationship;
}

export type TimeFrame = SingleApiRes<
  TimeFrameAttrs,
  TimeFrameInc,
  TimeFrameRel
>;
export type TimeFrames = PaginatedApiRes<
  TimeFrameAttrs,
  TimeFrameInc,
  TimeFrameRel
>;
export type TimeFrameResource = ApiResource<
  TimeFrameAttrs,
  TimeFrameInc,
  TimeFrameRel
>;
