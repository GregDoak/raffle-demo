import {
  Show,
  TextField,
  DateField,
  TabbedShowLayout,
  useRecordContext,
  SimpleShowLayout,
  ArrayField,
  Datagrid,
  NumberField,
} from "react-admin";

const AllocationsCount = (): number => {
  const record = useRecordContext();
  return record.allocations ? record.allocations.length : 0;
};

export const RaffleShow = () => (
  <Show>
    <SimpleShowLayout>
      <TextField source="name" />
      <TextField source="prize" />
      <TextField source="status" />
      <DateField source="createdAt" showTime />
      <TextField source="createdBy" />
    </SimpleShowLayout>

    <TabbedShowLayout>
      <TabbedShowLayout.Tab label="started">
        <DateField source="startAt" showTime />
        <DateField source="startedAt" showTime />
        <TextField source="startedBy" />
      </TabbedShowLayout.Tab>

      <TabbedShowLayout.Tab label="allocations" count={<AllocationsCount />}>
        <TextField source="totalTickets" />
        <TextField source="remainingTickets" />
        <ArrayField source="allocations">
          <Datagrid bulkActionButtons={false} rowClick={false}>
            <DateField source="allocatedAt" showTime />
            <TextField source="allocatedTo" />
            <NumberField source="quantity" />
          </Datagrid>
        </ArrayField>
      </TabbedShowLayout.Tab>

      <TabbedShowLayout.Tab label="closed">
        <DateField source="closeAt" showTime />
        <DateField source="closedAt" showTime />
        <TextField source="closedBy" />
      </TabbedShowLayout.Tab>

      <TabbedShowLayout.Tab label="drawn">
        <DateField source="drawAt" showTime />
        <DateField source="drawnAt" showTime />
        <TextField source="drawnBy" />
        <TextField source="winningAllocation" />
        <TextField source="winningTicketNumber" />
        <TextField source="wonBy" />
      </TabbedShowLayout.Tab>

      <TabbedShowLayout.Tab label="ended">
        <DateField source="endedAt" showTime />
        <TextField source="endedBy" />
        <TextField source="endedReason" />
      </TabbedShowLayout.Tab>
    </TabbedShowLayout>
  </Show>
);
