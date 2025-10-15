import { List, Datagrid, TextField } from "react-admin";

export const RaffleList = () => (
  <List empty={false} exporter={false}>
    <Datagrid>
      <TextField source="id" />
      <TextField source="name" />
      <TextField source="prize" />
      <TextField source="createdAt" />
      <TextField source="createdBy" />
      <TextField source="startAt" />
      <TextField source="closeAt" />
      <TextField source="drawAt" />
    </Datagrid>
  </List>
);
