import React from 'react';
import { View, Text, Pressable } from 'react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { DropdownMenuItem } from './types';

interface DropdownMenuProps {
  visible: boolean;
  items: DropdownMenuItem[];
}

/**
 * DropdownMenu Component
 * 
 * Renders a dropdown menu with configurable menu items.
 * Each item can have an icon, label, color, and onPress handler.
 * 
 * @param visible - Whether the dropdown menu is visible
 * @param items - Array of menu items to display
 */
export const DropdownMenu: React.FC<DropdownMenuProps> = ({ visible, items }) => {
  const colours = useThemeColours();

  if (!visible) return null;

  return (
    <View
      style={{ backgroundColor: colours.card }}
      className="mx-5 mt-2 rounded-lg overflow-hidden"
    >
      {items.map((item, index) => {
        const Icon = item.icon;
        const isLastItem = index === items.length - 1;
        
        return (
          <Pressable
            key={item.id}
            onPress={item.onPress}
            className={`flex-row items-center px-4 py-3 ${!isLastItem ? 'border-b' : ''}`}
            style={({ pressed }) => ({
              backgroundColor: pressed ? colours.surface : colours.card,
              borderBottomColor: colours.border,
            })}
          >
            <Icon size={20} color={item.color || colours.text} />
            <Text 
              className="ml-3 text-base" 
              style={{ 
                color: item.id === 'delete-circle' ? colours.error : colours.text 
              }}
            >
              {item.label}
            </Text>
          </Pressable>
        );
      })}
    </View>
  );
};
