import { AuthenticationProvider } from "@/app/authentication/authentication-provider";
import { Admin, Resource } from "react-admin";
import { BaseUrlVerifier } from "@/app/routing/base-url-verifier.ts";
import { DataProvider } from "@/app/data-provider/data-provider.ts";
import { LoginForm } from "@/features/authentication/components/login-form";
import { Layout } from "@/app/layout";
import {
  RaffleCreate,
  RaffleList,
  RaffleShow,
} from "@/features/raffles/components/index.ts";

BaseUrlVerifier(import.meta.env.VITE_ADMIN_BASE_URL);

export const App = () => (
  <Admin
    authProvider={AuthenticationProvider}
    dataProvider={DataProvider}
    disableTelemetry
    layout={Layout}
    loginPage={LoginForm}
    requireAuth
  >
    <Resource
      name="raffles"
      create={RaffleCreate}
      list={RaffleList}
      show={RaffleShow}
    />
  </Admin>
);
