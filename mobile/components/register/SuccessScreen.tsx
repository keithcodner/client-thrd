import React from "react";
import { Text, View } from "react-native";
import { Check } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

export const SuccessScreen: React.FC = () => {
  const colors = useThemeColours();

  return (
    <View className="w-full px-8 items-center">
      <View
        style={{
          width: 120,
          height: 120,
          borderRadius: 60,
          backgroundColor: colors.card,
          alignItems: 'center',
          justifyContent: 'center',
          marginBottom: 24,
        }}
      >
        <Check size={60} color="#C4F547" strokeWidth={3} />
      </View>

      <Text
        style={{
          color: colors.text,
          fontSize: 14,
          textAlign: 'center',
        }}
      >
        Your THRD is ready for connection.
      </Text>
    </View>
  );
};
