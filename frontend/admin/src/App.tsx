import authProvider from "./lib/authProvider.ts";
import { Admin, CustomRoutes } from "react-admin";
import { Route } from "react-router-dom";
import { Dashboard } from "./Dashboard";
import { LoginComponent } from "./LoginComponent.tsx";
import { Layout } from "./Layout";

export const App = () => (
  <Admin
    authProvider={authProvider}
    layout={Layout}
    loginPage={LoginComponent}
    requireAuth
  >
    <CustomRoutes>
      <Route path="/" element={<Dashboard />} />
    </CustomRoutes>
  </Admin>
);
