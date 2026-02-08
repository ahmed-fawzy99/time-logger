export type GenericErrorResponse = {
  [key: string]: string[];
};

export interface ApiResource<T1, T2, T3 = unknown> {
  id: string;
  type: string;
  attributes: T1;
  links?: {
    self: string;
  };
  includes: T2;
  relationships: T3;
}

export interface SingleApiRes<T1, T2, T3 = unknown, T4 = GenericErrorResponse> {
  status: number;
  message: string;
  data: ApiResource<T1, T2, T3>;
  errors?: T4;
}

export interface PaginatedApiRes<T1, T2, T3 = unknown> {
  data: ApiResource<T1, T2, T3>[];
  links: Links;
  meta: Meta;
}

export interface GenericApiRes<T1 = unknown, T2 = unknown> {
  status: number;
  message: string;
  data: T1;
  errors?: T2;
}

type SimplePaginationMeta = {
  per_page: number;

  current_page: number;
  from: number;
  last_page: number;
  links: {
    url: string;
    label: string;
    active: boolean;
  }[];
  path: string;
  to: number;
  total: number;
};

export interface Meta {
  per_page: number;
  path: string;

  current_page: number;
  from: number;
  last_page: number;
  links: {
    url: string;
    label: string;
    active: boolean;
  }[];
  to: number;
  total: number;

  next_cursor?: string;
  prev_cursor?: string;
}

export interface Links {
  first: string;
  last: string;
  prev?: string;
  next?: string;
}

export interface GenericRelationship {
  data: {
    type: string;
    id: string;
  };
  links?: {
    self?: string;
  };
}

export interface PaginationParams {
  pageSize?: number;
  pageNumber?: number;
}

export type GenericApiPagniatedRequestParams = PaginationParams & {
  sort?: string;
  include?: string;
  add?: string;
};

export type GenericApiSignleRequestParams = {
  identifier: string;
  include?: string;
  add?: string;
};

export type ValidationErrors = {
  [field: string]: string[];
};
