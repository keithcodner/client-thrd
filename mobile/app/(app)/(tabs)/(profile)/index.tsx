import React from "react";
import { View, Text, ScrollView } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

const Profile = () => {
  const colours = useThemeColours();

  return (
    <View className="flex-1 bg-white dark:bg-gray-900">
      <ScrollView className="flex-1">
        <View className="p-4 mt-10">
          <Text className="text-2xl font-bold text-gray-800 dark:text-white">
            Profile
          </Text>
          <Text className="text-gray-600 dark:text-gray-400 mt-2">
            Welcome to THRD Profile Section
          </Text>
        </View>
      </ScrollView>
    </View>
  );
};

export default Profile;
