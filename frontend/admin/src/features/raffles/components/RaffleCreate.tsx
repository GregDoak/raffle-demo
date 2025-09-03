import {
  Create,
  DateTimeInput,
  required,
  SimpleForm,
  TextInput,
} from "react-admin";

const now = new Date();
now.setHours(0);
now.setMinutes(0);
now.setSeconds(0);
now.setMilliseconds(0);
const startAt = now.setDate(now.getDate() + 1);
const closeAt = now.setDate(now.getDate() + 1);
const drawAt = now.setDate(now.getDate() + 1);

export const RaffleCreate = () => (
  <Create>
    <SimpleForm>
      <TextInput
        name="name"
        source="name"
        validate={[required()]}
        defaultValue="name"
      />
      <TextInput
        name="prize"
        source="prize"
        validate={[required()]}
        defaultValue="prize"
      />
      <DateTimeInput
        name="startAt"
        source="startAt"
        validate={[required()]}
        defaultValue={startAt}
      />
      <DateTimeInput
        name="closeAt"
        source="closeAt"
        validate={[required()]}
        defaultValue={closeAt}
      />
      <DateTimeInput
        name="drawAt"
        source="drawAt"
        validate={[required()]}
        defaultValue={drawAt}
      />
      <TextInput
        name="totalTickets"
        source="totalTickets"
        validate={[required()]}
        defaultValue="100"
      />
      <TextInput
        name="ticketPriceAmount"
        source="ticketPriceCurrency"
        validate={[required()]}
        defaultValue="GBP"
      />
      <TextInput
        name="createdBy"
        source="createdBy"
        validate={[required()]}
        defaultValue="crsfafs"
      />
    </SimpleForm>
  </Create>
);
