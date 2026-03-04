import React from "react";
import { Text, View, TouchableOpacity, Platform } from "react-native";
import { Sun, Moon } from "lucide-react-native";
import THRDIconButton from "@/components/core/THRDIconButton";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ThemeMode } from "./types";

type ThemeSelectionProps = {
  selectedTheme: ThemeMode;
  onThemeSelect: (theme: ThemeMode) => void;
  onBack: () => void;
  onContinue: () => void;
};

export const ThemeSelection: React.FC<ThemeSelectionProps> = ({
  selectedTheme,
  onThemeSelect,
  onBack,
  onContinue,
}) => {
  const colors = useThemeColours();

  return (
    <View style={{ flex: 1, backgroundColor: colors.background }}>
      {/* Header with Back Button */}
      <View className="flex-row items-center justify-between px-6 py-4">
        <TouchableOpacity onPress={onBack} className="p-2">
          <Text className="pt-10" style={{ color: colors.text, fontSize: 28, fontWeight: '300' }}>←</Text>
        </TouchableOpacity>
        <View style={{ width: 40 }} />
      </View>

      <View className="flex-1 justify-center items-center px-8">
        <Text
          style={{ 
            color: colors.text,
            fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
            fontSize: 28,
            fontWeight: '500',
            marginBottom: 8,
            textAlign: 'center'
          }}
        >
          Make THRD feel like yours.
        </Text>
        <Text
          style={{ 
            color: colors.secondaryText,
            fontSize: 14,
            textAlign: 'center',
            marginBottom: 48
          }}
        >
          Choose how you want your space to feel.
        </Text>

        <View className="w-full mb-8">
          <THRDIconButton 
            title="Light Mode"
            Icon={Sun}
            onPress={() => onThemeSelect("light")}
            className="mb-4"
            disabled={selectedTheme === "light"}
          />

          <THRDIconButton 
            title="Dark Mode"
            Icon={Moon}
            onPress={() => onThemeSelect("dark")}
            className="mb-4"
            disabled={selectedTheme === "dark"}
          />
        </View>

        <TouchableOpacity
          onPress={onContinue}
          style={{
            backgroundColor: colors.text,
            width: '100%',
            borderRadius: 24,
            paddingVertical: 16,
            paddingHorizontal: 32,
          }}
        >
          <Text style={{ 
            color: colors.background,
            textAlign: 'center',
            fontSize: 16,
            fontWeight: '700'
          }}>
            Continue
          </Text>
        </TouchableOpacity>
      </View>
    </View>
  );
};
