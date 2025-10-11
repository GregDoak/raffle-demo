import { Box, Stack } from "@mui/material";

import {
  Create,
  DateTimeInput,
  required,
  NumberInput,
  SimpleForm,
  TextInput,
  useNotify,
  useRedirect,
} from "react-admin";

const now = new Date();
const startAt = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
const closeAt = new Date(
  startAt.getFullYear(),
  startAt.getMonth(),
  startAt.getDate() + 1,
);
const drawAt = new Date(
  closeAt.getFullYear(),
  closeAt.getMonth(),
  closeAt.getDate() + 1,
);

export const RaffleCreate = () => {
  const notify = useNotify();
  const redirect = useRedirect();

  const onSuccess = () => {
    notify("Raffle created successfully.", { type: "success" });
    redirect("/raffles");
  };

  const onError = (error) => {
    notify(error.message, { type: "error", multiLine: true });
  };

  const transform = (data) => ({
    ...data,
    startAt: new Date(data.startAt).toISOString(),
    closeAt: new Date(data.closeAt).toISOString(),
    drawAt: new Date(data.drawAt).toISOString(),
    ticketPrice: {
      amount: Number((data.ticketPrice.amount * 100).toFixed(0)),
      currency: data.ticketPrice.currency,
    },
  });

  return (
    <Create mutationOptions={{ onError, onSuccess }} transform={transform}>
      <SimpleForm>
        <Box component="fieldset" sx={{ width: "100%" }}>
          <legend>Basic Details</legend>
          <Stack direction="row" gap={1}>
            <TextInput
              name="name"
              source="name"
              validate={[required()]}
              defaultValue=""
            />
            <TextInput
              name="prize"
              source="prize"
              validate={[required()]}
              defaultValue=""
            />
          </Stack>
        </Box>

        <Box component="fieldset" sx={{ width: "100%" }}>
          <legend>Schedule</legend>
          <Stack direction="column" gap={1} sx={{ width: "100%" }}>
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
          </Stack>
        </Box>
        <Box component="fieldset" sx={{ width: "100%" }}>
          <legend>Tickets</legend>
          <Stack direction="row" gap={1}>
            <NumberInput
              name="totalTickets"
              source="totalTickets"
              label="Number of tickets available"
              validate={[required()]}
              defaultValue={100}
              min={1}
              max={10_000}
            />
            <NumberInput
              name="ticketPrice.amount"
              label="Cost Per Ticket"
              source="ticketPrice.amount"
              format={(v: number) => Number(v).toFixed(2)}
              validate={[required()]}
              defaultValue={1.0}
              min={0}
              max={1000}
            />
            <TextInput
              name="ticketPrice.currency"
              source="ticketPrice.currency"
              label="Currency"
              validate={[required()]}
              defaultValue="GBP"
            />
          </Stack>
        </Box>
        <TextInput
          name="createdBy"
          source="createdBy"
          validate={[required()]}
          defaultValue="REMOVE THIS AFTER ADDING AUTH"
        />
      </SimpleForm>
    </Create>
  );
};
