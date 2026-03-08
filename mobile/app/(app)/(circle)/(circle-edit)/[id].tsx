import React from "react";
import { View, Text, ScrollView } from "react-native";
import { useLocalSearchParams } from "expo-router";
import { useThemeColours } from "@/hooks/useThemeColours";

const CalendarDetail = () => {
  const colours = useThemeColours();
  const { id } = useLocalSearchParams();

  return (
    <View className="flex-1 bg-white dark:bg-gray-900">
      <ScrollView className="flex-1">
        <View className="p-4 mt-10">
          <Text className="text-2xl font-bold text-gray-800 dark:text-white">
            Calendar Item #{id}
          </Text>
          <Text className="text-gray-600 dark:text-gray-400 mt-2">
            Details for calendar item
          </Text>
        </View>
      </ScrollView>
    </View>
  );
};

export default CalendarDetail;
