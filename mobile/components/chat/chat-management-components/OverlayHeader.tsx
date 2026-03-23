import React from 'react';
import { View, Text, Pressable } from 'react-native';
import { X } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { QuickActionButton } from './QuickActionButton';
import { QuickActionButtonData } from './types';

interface OverlayHeaderProps {
  chatName: string;
  onClose: () => void;
  quickActionButtons: QuickActionButtonData[];
  isDropdownActive?: boolean;
}

/**
 * OverlayHeader Component
 * 
 * Renders the header section of the chat management overlay.
 * Includes a back button, chat name, and quick action buttons.
 * 
 * @param chatName - Name of the chat/circle
 * @param onClose - Handler for the close/back button
 * @param quickActionButtons - Array of quick action buttons to display
 * @param isDropdownActive - Whether the dropdown is currently active
 */
export const OverlayHeader: React.FC<OverlayHeaderProps> = ({
  chatName,
  onClose,
  quickActionButtons,
  isDropdownActive = false,
}) => {
  const colours = useThemeColours();

  return (
    <View 
      style={{ 
        backgroundColor: colours.background,
        borderBottomColor: colours.border,
        borderBottomWidth: 1,
      }} 
      className="pt-12 pb-4 px-5"
    >
      <View className="flex-row items-center justify-between mb-3">
        {/* Back Button */}
        <Pressable
          onPress={onClose}
          className="w-10 h-10 items-center justify-center rounded-full"
          style={{ backgroundColor: colours.surface }}
        >
          <X size={20} color={colours.text} />
        </Pressable>

        <Text className="text-lg font-semibold flex-1 text-center" style={{ color: colours.text }}>
          {chatName}
        </Text>

        {/* Quick Action Buttons */}
        <View className="flex-row gap-2">
          {quickActionButtons.map(button => (
            <QuickActionButton 
              key={button.id} 
              button={button}
              isActive={button.id === 'more' ? isDropdownActive : false}
            />
          ))}
        </View>
      </View>
    </View>
  );
};
