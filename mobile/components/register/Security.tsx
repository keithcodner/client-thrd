import React from "react";
import { Text, View, TextInput } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

type SecurityProps = {
  email: string;
  password: string;
  confirmPassword: string;
  onEmailChange: (email: string) => void;
  onPasswordChange: (password: string) => void;
  onConfirmPasswordChange: (confirmPassword: string) => void;
};

export const Security: React.FC<SecurityProps> = ({
  email,
  password,
  confirmPassword,
  onEmailChange,
  onPasswordChange,
  onConfirmPasswordChange,
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
        EMAIL & PASSWORD
      </Text>
      <TextInput
        value={email}
        onChangeText={onEmailChange}
        placeholder="alex@example.com"
        placeholderTextColor={colors.secondaryText}
        keyboardType="email-address"
        autoCapitalize="none"
        style={{
          backgroundColor: colors.card,
          color: colors.text,
          fontSize: 16,
          paddingVertical: 16,
          paddingHorizontal: 16,
          borderRadius: 16,
          marginBottom: 16,
        }}
      />
      <TextInput
        value={password}
        onChangeText={onPasswordChange}
        placeholder="Security Phrase"
        placeholderTextColor={colors.secondaryText}
        secureTextEntry
        style={{
          backgroundColor: colors.card,
          color: colors.text,
          fontSize: 16,
          paddingVertical: 16,
          paddingHorizontal: 16,
          borderRadius: 16,
          marginBottom: 16,
        }}
      />
      <TextInput
        value={confirmPassword}
        onChangeText={onConfirmPasswordChange}
        placeholder="Confirm Security Phrase"
        placeholderTextColor={colors.secondaryText}
        secureTextEntry
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
