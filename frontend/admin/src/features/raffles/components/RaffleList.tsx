import {
  List,
  TextField,
  DateField,
  TextInput,
  DatagridConfigurable,
  CreateButton,
  SelectColumnsButton,
  TopToolbar,
  SelectInput,
  RefreshButton,
} from "react-admin";

const Actions = () => (
  <TopToolbar>
    <SelectColumnsButton />
    <RefreshButton />
    <CreateButton />
  </TopToolbar>
);

const Filters = [
  <TextInput
    key={1}
    label="Name"
    name="name"
    source="name"
    defaultValue=""
    alwaysOn={true}
  />,
  <TextInput
    key={2}
    label="Prize"
    name="prize"
    source="prize"
    defaultValue=""
    alwaysOn={true}
  />,
  <SelectInput
    key={3}
    label="Status"
    name="status"
    defaultValue=""
    alwaysOn={true}
    source="status"
    choices={[
      { id: "created", name: "Created" },
      { id: "started", name: "Started" },
      { id: "closed", name: "Closed" },
      { id: "ended", name: "Ended" },
    ]}
  />,
];

const Omit = [
  "id",
  "createdAt",
  "createdBy",
  "startedAt",
  "startedBy",
  "closedAt",
  "closedBy",
  "drawnAt",
  "drawnBy",
  "winningAllocation",
  "winningTicketNumber",
  "wonBy",
  "endedAt",
  "endedBy",
  "endedReason",
];

export const RaffleList = () => (
  <List actions={<Actions />} empty={false} exporter={false} filters={Filters}>
    <DatagridConfigurable omit={Omit}>
      <TextField source="id" sortable={false} />
      <TextField source="name" />
      <TextField source="prize" />
      <TextField source="status" />
      <DateField source="createdAt" showTime />
      <TextField source="createdBy" sortable={false} />
      <DateField source="startAt" showTime />
      <DateField source="startedAt" showTime sortable={false} />
      <TextField source="startedBy" sortable={false} />
      <TextField source="totalTickets" sortable={false} />
      <TextField source="remainingTickets" sortable={false} />
      <DateField source="closeAt" showTime />
      <DateField source="closedAt" showTime sortable={false} />
      <TextField source="closedBy" sortable={false} />
      <DateField source="drawAt" showTime />
      <DateField source="drawnAt" showTime sortable={false} />
      <TextField source="drawnBy" sortable={false} />
      <TextField source="winningAllocation" sortable={false} />
      <TextField source="winningTicketNumber" sortable={false} />
      <TextField source="wonBy" sortable={false} />
      <DateField source="endedAt" showTime sortable={false} />
      <TextField source="endedBy" sortable={false} />
      <TextField source="endedReason" sortable={false} />
    </DatagridConfigurable>
  </List>
);
