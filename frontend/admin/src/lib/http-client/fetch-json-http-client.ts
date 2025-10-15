import { fetchUtils, HttpError } from "react-admin";
import {
  HttpClientInterface,
  ResultInterface,
} from "@/lib/http-client/http-client-interface.ts";

export class FetchJsonHttpClient implements HttpClientInterface {
  private readonly baseUrl: string;
  private readonly headers: Headers;

  constructor(baseUrl: string, headers: Headers = new Headers()) {
    headers.set("Content-Type", "application/json");

    this.baseUrl = baseUrl;
    this.headers = headers;
  }

  async get(
    path: string,
    params: object = {},
    headers: Headers = new Headers(),
  ): Promise<ResultInterface> {
    return new Promise((resolve, reject) => {
      fetchUtils
        .fetchJson(
          this.baseUrl + path + "?" + new URLSearchParams(params).toString(),
          {
            method: "GET",
            headers: new Headers([
              ...this.headers.entries(),
              ...headers.entries(),
            ]),
          },
        )
        .then((success) => {
          return resolve(this.handleSuccess(success));
        })
        .catch((problem: HttpError) => {
          return reject(this.handleProblem(problem));
        });
    });
  }

  async post(
    path: string,
    params: object = {},
    headers: Headers = new Headers(),
  ): Promise<ResultInterface> {
    return new Promise((resolve, reject) => {
      fetchUtils
        .fetchJson(this.baseUrl + path, {
          method: "POST",
          body: JSON.stringify(params),
          headers: new Headers([
            ...this.headers.entries(),
            ...headers.entries(),
          ]),
        })
        .then((success) => {
          return resolve(this.handleSuccess(success));
        })
        .catch((problem: HttpError) => {
          return reject(this.handleProblem(problem));
        });
    });
  }

  private handleSuccess(success): ResultInterface {
    return {
      isSuccess: true,
      code: success.status,
      json: success.json,
    };
  }

  private handleProblem(problem: HttpError): ResultInterface {
    return {
      isSuccess: true,
      code: problem.status,
      json: problem.body,
    };
  }
}
