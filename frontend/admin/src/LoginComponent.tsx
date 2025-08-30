import { Login, LoginForm, TextInput, email, required } from "react-admin";

export const LoginComponent = () => (
  <Login>
    <LoginForm>
      <TextInput
        name="username"
        autoFocus
        source="email"
        label="Email"
        autoComplete="email"
        type="email"
        validate={[email(), required()]}
      />
    </LoginForm>
  </Login>
);
