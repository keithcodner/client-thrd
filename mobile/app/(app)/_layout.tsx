
import { useThemeColours } from "@/hooks/useThemeColours";
import { Stack } from "expo-router";

/**
 * AppLayout - Main app navigation structure
 * 
 * ⚠️ NO AUTH LOGIC HERE - Auth guard is handled at root level only.
 * This layout should only contain navigation structure and styling.
 * 
 * See: mobile/docs/NAVIGATION_ARCHITECTURE.md
 */
const AppLayout = () => {
  const colors = useThemeColours();

  return (
    <Stack
      screenOptions={{
        headerShown: false,
        headerStyle: {
          backgroundColor: colors.background,
        },
        headerTintColor: colors.primary,
        headerTitleStyle: {
          color: colors.background,
        },
        contentStyle: {
          backgroundColor: colors.background,
        },
      }}
    >
      <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
    </Stack>
  );
};

export default AppLayout;
