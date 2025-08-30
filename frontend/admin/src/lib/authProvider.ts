import type { AuthProvider, HttpError } from "react-admin";
import SessionAuthTokenStorage from "./SessionAuthTokenStorage";
import { UserIdentity } from "ra-core/src/types.ts";
import AuthenticatedUser from "./AuthenticatedUser.ts";

const authTokenStorage = new SessionAuthTokenStorage();

const authProvider: AuthProvider = {
  checkAuth(): Promise<void> {
    if (!authTokenStorage.has()) {
      throw new Error();
    }

    const input = document.createElement("input");
    input.value = authTokenStorage.get();
    input.type = "email";

    if (!input.checkValidity()) {
      throw new Error();
    }
  },

  checkError(error: HttpError): Promise<void> {
    const status = error.status;
    if (status === 401 || status === 403) {
      authTokenStorage.clear();
      throw new Error();
    }
  },

  getIdentity(): Promise<UserIdentity> {
    return Promise.resolve(new AuthenticatedUser(authTokenStorage.get()));
  },

  async login({ username }): Promise<void> {
    authTokenStorage.set(username);
  },

  async logout(): Promise<void | false | string> {
    console.log("logout");
    authTokenStorage.clear();
  },
};

export default authProvider;
