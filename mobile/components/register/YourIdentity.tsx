import React from "react";
import { Text, View, TextInput } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

type YourIdentityProps = {
  fullName: string;
  onFullNameChange: (name: string) => void;
};

export const YourIdentity: React.FC<YourIdentityProps> = ({
  fullName,
  onFullNameChange,
}) => {
  const colors = useThemeColours();

  return (
    <View className="w-full px-8">
      <Text
        style={{
          color: colors.secondaryText,
          fontSize: 12,
          fontWeight: '600',
          letterSpacing: 1,
          marginBottom: 12,
        }}
      >
        FULL NAME
      </Text>
      <TextInput
        value={fullName}
        onChangeText={onFullNameChange}
        placeholder="Alex Rivera"
        placeholderTextColor={colors.secondaryText}
        style={{
          backgroundColor: colors.card,
          color: colors.text,
          fontSize: 16,
          paddingVertical: 16,
          paddingHorizontal: 16,
          borderRadius: 16,
        }}
      />
    </View>
  );
};
