import React from "react";
import { Text, View, TouchableOpacity } from "react-native";
import { Calendar } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

type CalendarSyncProps = {
  onConnect: () => void;
  onSkip: () => void;
};

export const CalendarSync: React.FC<CalendarSyncProps> = ({
  onConnect,
  onSkip,
}) => {
  const colors = useThemeColours();

  return (
    <View className="w-full px-8 items-center">
      <View
        style={{
          width: 120,
          height: 120,
          borderRadius: 60,
          backgroundColor: 'rgba(59, 130, 246, 0.15)',
          alignItems: 'center',
          justifyContent: 'center',
          marginBottom: 32,
        }}
      >
        <View
          style={{
            width: 80,
            height: 80,
            borderRadius: 40,
            backgroundColor: 'rgba(59, 130, 246, 0.3)',
            alignItems: 'center',
            justifyContent: 'center',
          }}
        >
          <Calendar size={40} color="#3b82f6" strokeWidth={2} />
        </View>
      </View>

      <Text
        style={{
          color: colors.text,
          fontSize: 14,
          textAlign: 'center',
          marginBottom: 32,
          lineHeight: 22,
        }}
      >
        Let THRD analyze your busy slots to find the{'\n'}
        best times for your plans automatically.
      </Text>

      <TouchableOpacity
        onPress={onConnect}
        style={{
          backgroundColor: colors.text,
          width: '100%',
          borderRadius: 24,
          paddingVertical: 16,
          paddingHorizontal: 32,
          marginBottom: 16,
        }}
      >
        <Text
          style={{
            color: colors.background,
            textAlign: 'center',
            fontSize: 16,
            fontWeight: '700',
          }}
        >
          Connect Calendar
        </Text>
      </TouchableOpacity>

      <TouchableOpacity
        onPress={onSkip}
        style={{
          paddingVertical: 12,
        }}
      >
        <Text
          style={{
            color: colors.secondaryText,
            fontSize: 14,
            fontWeight: '600',
          }}
        >
          Skip for now
        </Text>
      </TouchableOpacity>
    </View>
  );
};
