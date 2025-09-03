import { AuthenticationProvider } from "@/app/authentication/authentication-provider";
import { Admin, Resource } from "react-admin";
import { DataProvider } from "@/app/data-provider/data-provider.ts";
import { LoginForm } from "@/features/authentication/components/login-form";
import { Layout } from "@/app/layout";
import {
  RaffleCreate,
  RaffleList,
} from "@/features/raffles/components/index.ts";

export const App = () => (
  <Admin
    authProvider={AuthenticationProvider}
    dataProvider={DataProvider}
    disableTelemetry
    layout={Layout}
    loginPage={LoginForm}
    requireAuth
  >
    <Resource name="raffles" create={RaffleCreate} list={RaffleList} />
  </Admin>
);
