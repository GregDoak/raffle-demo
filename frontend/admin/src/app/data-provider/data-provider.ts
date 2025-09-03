/* eslint-disable @typescript-eslint/no-unused-vars */

import { AuthenticationProvider } from "@/app/authentication/authentication-provider.ts";
import { HttpClientInterface } from "@/lib/http-client/http-client-interface.ts";
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

console.log(getHeaders());

function getHeaders(): Headers {
  const headers = new Headers();
  headers.set("Authorization", "Bearer " + AuthenticationProvider.getToken());

  return headers;
}

export const DataProvider: DataProvider = {
  create<RecordType, ResultRecordType>(
    resource: string,
    params: CreateParams,
  ): Promise<CreateResult<ResultRecordType>> {
    return Promise.resolve(undefined);
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
    return Promise.resolve(undefined);
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
