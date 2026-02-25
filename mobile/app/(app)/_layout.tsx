
import { useSession } from "@/context/AuthContext";
import { useThemeColours } from "@/hooks/useThemeColours";
import { Redirect, Stack } from "expo-router";
import { ActivityIndicator, Text, View } from "react-native";


const AppLayout = () => {

  const { session, isLoading } = useSession();
  const colors = useThemeColours();

  // Show a loading indicator while checking the session
  if (isLoading) {
    return (
      <View className="flex-1 justify-center items-cetner bg white dark:bg-gray-900">
        <ActivityIndicator size="large" color={colors.primary} />
        <Text className="mt-4 text-gray-500 dark:text-gray-400">Loading...</Text>
      </View>
    );
  }

  // If there's no session, redirect to the login page
  if (!session) {
    return <Redirect href="/sign-in" />;
  }

  return (
    <Stack screenOptions={{
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
    }}/>
  );
};

export default AppLayout;
