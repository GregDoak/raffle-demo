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
  ): Promise<{ status: number; body: string }> {
    return fetchUtils.fetchJson(
      this.baseUrl + path + new URLSearchParams(params).toString(),
      {
        method: "GET",
        headers: new Headers([...this.headers.entries(), ...headers.entries()]),
      },
    );
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
        .then((response) => {
          const result: ResultInterface = {
            isSuccess: true,
            id: response.json.id,
          };
          return resolve(result);
        })
        .catch((error) => {
          return reject(this.transformErrorToResult(error));
        });
    });
  }

  private transformErrorToResult(error): ResultInterface {
    if (error instanceof HttpError) {
      if (error.body.detail && error.body.errors) {
        return {
          isSuccess: false,
          message: error.body.detail + "\n" + error.body.errors.join("\n"),
        };
      }
      return {
        isSuccess: false,
        message: error.message,
      };
    }

    return {
      isSuccess: false,
      message: "An unknown error occurred.",
    };
  }
}
