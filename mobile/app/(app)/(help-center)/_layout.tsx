import { Stack } from "expo-router";
import { useThemeColours } from "@/hooks/useThemeColours";

export default function CalendarLayout() {
  const colours = useThemeColours();

  return (
    <Stack
      screenOptions={{
        headerShown: false,
        contentStyle: {
          backgroundColor: colours.background,
        },
      }}
    >
      <Stack.Screen name="index" options={{ headerShown: false }} />
      <Stack.Screen name="[id]" options={{ headerShown: false }} />
    </Stack>
  );
}
