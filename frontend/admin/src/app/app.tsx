import { AuthenticationProvider } from "@/app/authentication/authentication-provider";
import { Admin, CustomRoutes } from "react-admin";
import { DataProvider } from "@/app/data-provider/data-provider.ts";
import { DashboardCard } from "@/features/dashboard/components/dashboard-card";
import { Route } from "react-router-dom";
import { LoginForm } from "@/features/authentication/components/login-form";
import { Layout } from "@/app/layout";

export const App = () => (
  <Admin
    authProvider={AuthenticationProvider}
    dataProvider={DataProvider}
    disableTelemetry
    layout={Layout}
    loginPage={LoginForm}
    requireAuth
  >
    <CustomRoutes>
      <Route path="/" element={<DashboardCard />} />
    </CustomRoutes>
  </Admin>
);
