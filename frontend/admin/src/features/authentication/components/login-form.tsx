import {
  Login,
  LoginForm as BaseLoginForm,
  TextInput,
  email,
  required,
} from "react-admin";

export const LoginForm = () => (
  <Login>
    <BaseLoginForm>
      <TextInput
        name="username"
        autoFocus
        source="email"
        label="Email"
        autoComplete="email"
        type="email"
        validate={[email(), required()]}
      />
    </BaseLoginForm>
  </Login>
);
