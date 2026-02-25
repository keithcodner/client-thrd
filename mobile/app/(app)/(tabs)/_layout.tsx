import { Tabs } from "expo-router";
import { MaterialIcons } from "@expo/vector-icons";
import { useThemeColours } from "@/hooks/useThemeColours";


const TabsLayout = () => {
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
    <Tabs>
      
    </Tabs>
  );
};

export default TabsLayout;
