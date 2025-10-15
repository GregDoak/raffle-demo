export interface ResultInterface {
  isSuccess: boolean;
  code: number;
  json: object;
}

export interface HttpClientInterface {
  get(
    path: string,
    params: object = {},
    headers: Headers = new Headers(),
  ): Promise<ResultInterface>;

  post(
    path: string,
    params: object = {},
    headers: Headers = new Headers(),
  ): Promise<ResultInterface>;
}
