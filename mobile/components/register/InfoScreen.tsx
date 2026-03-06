import React from "react";
import { Text, View } from "react-native";
import { Sparkles, Calendar as CalendarIcon, Edit, Heart } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

export const InfoScreen: React.FC = () => {
  const colors = useThemeColours();

  const features = [
    {
      icon: Sparkles,
      text: "Use AI scheduling — or plan things manually.",
    },
    {
      icon: CalendarIcon,
      text: "Add your own calendar blocks any time.",
    },
    {
      icon: Edit,
      text: "Edit your profile and settings whenever you want.",
    },
    {
      icon: Heart,
      text: "Mind Space and Help are always available.",
    },
  ];

  return (
    <View className="w-full px-8">
      {features.map((feature, index) => {
        const Icon = feature.icon;
        return (
          <View
            key={index}
            style={{
              flexDirection: 'row',
              alignItems: 'flex-start',
              marginBottom: 28,
            }}
          >
            <View
              style={{
                width: 48,
                height: 48,
                borderRadius: 12,
                backgroundColor: colors.card,
                alignItems: 'center',
                justifyContent: 'center',
                marginRight: 16,
              }}
            >
              <Icon size={24} color={colors.accent} strokeWidth={2} />
            </View>
            <Text
              style={{
                flex: 1,
                color: colors.text,
                fontSize: 16,
                lineHeight: 24,
                paddingTop: 12,
              }}
            >
              {feature.text}
            </Text>
          </View>
        );
      })}
    </View>
  );
};
