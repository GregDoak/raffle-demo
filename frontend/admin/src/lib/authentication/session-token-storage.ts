import { TokenStorageInterface } from "./token-storage-interface";

export class SessionTokenStorage implements TokenStorageInterface {
  private readonly tokenKey = "raffle-demo-admin-token";

  public clear() {
    sessionStorage.removeItem(this.tokenKey);
  }

  public has(): boolean {
    return sessionStorage.getItem(this.tokenKey) !== null;
  }

  public get(): string {
    const token = sessionStorage.getItem(this.tokenKey);

    if (token == null) {
      throw new Error();
    }

    return token;
  }

  public set(token: string): void {
    sessionStorage.setItem(this.tokenKey, token);
  }
}
