export interface AuthTokenStorageInterface {
  clear(): void;

  has(): boolean;

  get(): string | null;

  set(token: string): void;
}
