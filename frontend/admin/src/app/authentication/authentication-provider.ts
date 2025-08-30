import { AuthenticatedUser } from "./authenticated-user";
import type { AuthProvider, HttpError } from "react-admin";
import { SessionTokenStorage } from "@/lib/authentication/session-token-storage";

const tokenStorage = new SessionTokenStorage();

export const AuthenticationProvider: AuthProvider = {
  checkAuth(): Promise<void> {
    if (!tokenStorage.has()) {
      throw new Error();
    }

    const input = document.createElement("input");
    input.value = tokenStorage.get();
    input.type = "email";

    if (!input.checkValidity()) {
      throw new Error();
    }
  },

  checkError(error: HttpError): Promise<void> {
    const status = error.status;
    if (status === 401 || status === 403) {
      tokenStorage.clear();
      throw new Error();
    }
  },

  getIdentity(): Promise<AuthenticatedUser> {
    return Promise.resolve(new AuthenticatedUser(tokenStorage.get()));
  },

  async login({ username }): Promise<void> {
    tokenStorage.set(username);
  },

  async logout(): Promise<void | false | string> {
    tokenStorage.clear();
  },
};
