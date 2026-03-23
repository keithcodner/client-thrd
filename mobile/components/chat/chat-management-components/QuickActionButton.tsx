import React from 'react';
import { Pressable } from 'react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { QuickActionButtonData } from './types';

interface QuickActionButtonProps {
  button: QuickActionButtonData;
  isActive?: boolean;
}

/**
 * QuickActionButton Component
 * 
 * Renders individual quick action buttons with consistent styling.
 * Handles special styling for buttons like "more" that change based on state.
 * 
 * @param button - Button configuration with icon, color, and onPress handler
 * @param isActive - Whether the button is in active state (for special styling)
 */
export const QuickActionButton: React.FC<QuickActionButtonProps> = ({ 
  button, 
  isActive = false 
}) => {
  const colours = useThemeColours();
  const Icon = button.icon;
  
  const backgroundColor = button.specialStyle && isActive 
    ? colours.primary 
    : colours.surface;

  return (
    <Pressable
      onPress={button.onPress}
      className="w-10 h-10 items-center justify-center rounded-full"
      style={{ backgroundColor }}
    >
      <Icon size={18} color={button.color} />
    </Pressable>
  );
};
