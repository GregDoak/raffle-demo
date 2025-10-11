export interface ResultInterface {
  isSuccess: boolean;
  id?: string;
  message?: string;
}

export interface HttpClientInterface {
  get(
    path: string,
    params: object = {},
    headers: Headers = new Headers(),
  ): Promise<{
    status: number;
    body: string;
  }>;

  post(
    path: string,
    params: object = {},
    headers: Headers = new Headers(),
  ): Promise<ResultInterface>;
}
