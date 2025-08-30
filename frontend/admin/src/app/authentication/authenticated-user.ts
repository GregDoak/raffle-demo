import { UserIdentity } from "ra-core/src/types";

export class AuthenticatedUser implements UserIdentity {
  readonly fullName: string;
  constructor(readonly id: string) {
    this.fullName = id;
  }

  [key: string]: string;
}
