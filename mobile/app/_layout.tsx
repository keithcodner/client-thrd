import { Stack } from "expo-router";
import "../global.css"
import { ThemeProvider, DefaultTheme } from "@react-navigation/native";

export default function RootLayout() {
  return <ThemeProvider value={DefaultTheme}>
      <Stack />
  </ThemeProvider>;
}
