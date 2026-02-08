import type {
  ApiResource,
  GenericRelationship,
  PaginatedApiRes,
  SingleApiRes,
} from '../global';
import type { TimeFrameResource } from './time-frame';

export interface TimeEntryAttrs {
  workDay: string;
  startTime: string;
  endTime: string;
  description: string;
  billable: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface TimeEntryRel {
  timeFrame?: GenericRelationship;
}

export interface TimeEntryInc {
  timeFrame?: TimeFrameResource;
}

export type TimeEntry = SingleApiRes<
  TimeEntryAttrs,
  TimeEntryInc,
  TimeEntryRel
>;
export type TimeEntries = PaginatedApiRes<
  TimeEntryAttrs,
  TimeEntryInc,
  TimeEntryRel
>;
export type TimeEntryResource = ApiResource<
  TimeEntryAttrs,
  TimeEntryInc,
  TimeEntryRel
>;
