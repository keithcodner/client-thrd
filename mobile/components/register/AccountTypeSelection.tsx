import React from "react";
import { Text, View, TouchableOpacity, Platform } from "react-native";
import { User, Calendar, Building2 } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { AccountType } from "./constants";

type AccountTypeSelectionProps = {
  selectedType: AccountType | null;
  onTypeSelect: (type: AccountType) => void;
};

export const AccountTypeSelection: React.FC<AccountTypeSelectionProps> = ({
  selectedType,
  onTypeSelect,
}) => {
  const colors = useThemeColours();

  const options = [
    { type: "personal" as AccountType, label: "Personal", icon: User },
    { type: "community" as AccountType, label: "Community Host", icon: Calendar },
    { type: "business" as AccountType, label: "Business Venue", icon: Building2 },
  ];

  return (
    <View className="w-full px-8">
      {options.map((option) => {
        const Icon = option.icon;
        const isSelected = selectedType === option.type;
        
        return (
          <TouchableOpacity
            key={option.type}
            onPress={() => onTypeSelect(option.type)}
            style={{
              backgroundColor: colors.background,
              borderWidth: 2,
              borderColor: isSelected ? '#ADC178' : colors.stone800,
              borderRadius: 24,
              paddingVertical: 20,
              paddingHorizontal: 24,
              marginBottom: 16,
              flexDirection: 'row',
              alignItems: 'center',
              shadowColor: isSelected ? '#ADC178' : 'transparent',
              shadowOffset: { width: 0, height: 2 },
              shadowOpacity: isSelected ? 0.3 : 0,
              shadowRadius: isSelected ? 8 : 0,
              elevation: isSelected ? 4 : 0,
            }}
          >
            <View 
              style={{
                backgroundColor: isSelected ? '#ADC178' : colors.card,
                borderRadius: 12,
                width: 48,
                height: 48,
                alignItems: 'center',
                justifyContent: 'center',
                marginRight: 16,
              }}
            >
              <Icon 
                size={24} 
                color={isSelected ? '#000' : colors.text}
                strokeWidth={2}
              />
            </View>
            <Text
              style={{
                color: colors.text,
                fontSize: 18,
                fontWeight: '600',
              }}
            >
              {option.label}
            </Text>
          </TouchableOpacity>
        );
      })}
    </View>
  );
};
