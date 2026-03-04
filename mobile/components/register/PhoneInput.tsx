import React, { useState } from "react";
import { Text, View, TextInput } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

type PhoneInputProps = {
  phone: string;
  onPhoneChange: (phone: string) => void;
};

export const PhoneInput: React.FC<PhoneInputProps> = ({
  phone,
  onPhoneChange,
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
        PHONE NUMBER (REQUIRED)
      </Text>
      <View 
        style={{
          flexDirection: 'row',
          backgroundColor: colors.card,
          borderRadius: 16,
          overflow: 'hidden',
        }}
      >
        <View
          style={{
            backgroundColor: colors.stone800,
            paddingVertical: 16,
            paddingHorizontal: 16,
            justifyContent: 'center',
          }}
        >
          <Text style={{ color: colors.text, fontSize: 16 }}>+1</Text>
        </View>
        <TextInput
          value={phone}
          onChangeText={onPhoneChange}
          placeholder="22222222222222"
          placeholderTextColor={colors.secondaryText}
          keyboardType="phone-pad"
          style={{
            flex: 1,
            color: colors.text,
            fontSize: 16,
            paddingVertical: 16,
            paddingHorizontal: 16,
          }}
        />
      </View>
    </View>
  );
};
