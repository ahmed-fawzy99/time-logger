import type {
  ApiResource,
  GenericRelationship,
  PaginatedApiRes,
  SingleApiRes,
} from '../global';
import type { TimeFrameResource } from './time-frame';

export interface ProjectAttrs {
  name: string;
  slug: string;
  description?: string;
  timeFramesCount?: number;
  timeEntriesCount?: number;
  additionalProperties?: object;
  createdAt: string;
  updatedAt: string;
}
export interface ProjectRel {
  user?: GenericRelationship;
}

export interface ProjectInc {
  timeFrames?: TimeFrameResource[];
}

export type Project = SingleApiRes<ProjectAttrs, ProjectInc, ProjectRel>;
export type Projects = PaginatedApiRes<ProjectAttrs, ProjectInc, ProjectRel>;
export type ProjectResource = ApiResource<ProjectAttrs, ProjectInc, ProjectRel>;
