/* eslint-disable @typescript-eslint/no-unused-vars */

import { AuthenticationProvider } from "@/app/authentication/authentication-provider.ts";
import {
  HttpClientInterface,
  ResultInterface,
} from "@/lib/http-client/http-client-interface.ts";
import { FetchJsonHttpClient } from "@/lib/http-client/fetch-json-http-client.ts";
import {
  CreateParams,
  CreateResult,
  DataProvider,
  DeleteManyParams,
  DeleteManyResult,
  DeleteParams,
  DeleteResult,
  GetListParams,
  GetListResult,
  GetManyParams,
  GetManyReferenceParams,
  GetManyReferenceResult,
  GetManyResult,
  GetOneParams,
  GetOneResult,
  QueryFunctionContext,
  UpdateManyParams,
  UpdateManyResult,
  UpdateParams,
  UpdateResult,
} from "react-admin";

const httpClient: HttpClientInterface = new FetchJsonHttpClient(
  import.meta.env.VITE_API_BASE_URL,
);

const getHeaders = (): Headers => {
  const headers = new Headers();
  headers.set("Authorization", "Bearer " + AuthenticationProvider.getToken());

  return headers;
};

const transformProblemToMessage = (problem: ResultInterface): string => {
  if (problem.json.detail && problem.json.errors) {
    return problem.json.detail + "\n" + problem.json.errors.join("\n");
  }

  return "An unknown error occurred.";
};

export const DataProvider: DataProvider = {
  async create<RecordType, ResultRecordType>(
    resource: string,
    params: CreateParams,
  ): Promise<CreateResult<ResultRecordType>> {
    return new Promise((resolve, reject) => {
      httpClient
        .post("/" + resource, params.data, getHeaders())
        .then((result: ResultInterface) => {
          return resolve({ data: result.json });
        })
        .catch((result: ResultInterface) => {
          return reject({ message: transformProblemToMessage(result) });
        });
    });
  },
  delete<RecordType>(
    resource: string,
    params: DeleteParams<RecordType>,
  ): Promise<DeleteResult<RecordType>> {
    return Promise.resolve(undefined);
  },
  deleteMany<RecordType>(
    resource: string,
    params: DeleteManyParams<RecordType>,
  ): Promise<DeleteManyResult<RecordType>> {
    return Promise.resolve(undefined);
  },
  getList<RecordType>(
    resource: string,
    params: GetListParams & QueryFunctionContext,
  ): Promise<GetListResult<RecordType>> {
    const { page, perPage } = params.pagination;
    const { field, order } = params.sort;

    const query = {
      page: page,
      limit: perPage,
      sort: field,
      order: order,
      ...params.filter,
    };

    return new Promise((resolve, reject) => {
      httpClient
        .get("/" + resource, query, getHeaders())
        .then((result: ResultInterface) => {
          return resolve(result.json);
        })
        .catch((result: ResultInterface) => {
          return reject({ message: transformProblemToMessage(result) });
        });
    });
  },
  getMany<RecordType>(
    resource: string,
    params: GetManyParams<RecordType> & QueryFunctionContext,
  ): Promise<GetManyResult<RecordType>> {
    return Promise.resolve(undefined);
  },
  getManyReference<RecordType>(
    resource: string,
    params: GetManyReferenceParams & QueryFunctionContext,
  ): Promise<GetManyReferenceResult<RecordType>> {
    return Promise.resolve(undefined);
  },
  getOne<RecordType>(
    resource: string,
    params: GetOneParams<RecordType> & QueryFunctionContext,
  ): Promise<GetOneResult<RecordType>> {
    return Promise.resolve(undefined);
  },
  update<RecordType>(
    resource: string,
    params: UpdateParams,
  ): Promise<UpdateResult<RecordType>> {
    return Promise.resolve(undefined);
  },
  updateMany<RecordType>(
    resource: string,
    params: UpdateManyParams,
  ): Promise<UpdateManyResult<RecordType>> {
    return Promise.resolve(undefined);
  },
};
