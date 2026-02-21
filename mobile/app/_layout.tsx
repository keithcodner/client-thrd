import { Stack } from "expo-router";
import "../global.css"
import { ThemeProvider } from "@react-navigation/native";

export default function RootLayout() {
  return <ThemeProvider>
      <Stack />
  </ThemeProvider>;
}
