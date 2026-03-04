import React from "react";
import { TouchableOpacity, Text, View, ActivityIndicator } from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import { useThemeColours } from "@/hooks/useThemeColours";
import { useTheme } from "@/context/ThemeContext";
import { Sun, Moon, Monitor } from 'lucide-react-native';


interface TIconButtonProps {
  title?: string;
  className?: string;
  disabled?: boolean;
  loading?: boolean;
  variant?: "primary" | "secondary" | "tertiary";
  onPress: () => void;
  children?: React.ReactNode;
  Icon?: React.ComponentType<any>;
}

const THRDIconButton: React.FC<TIconButtonProps> = ({
    title,
    className = "",
    disabled = false,
    loading = false,
    onPress,
    children,
    Icon,
}) => { 
    const { currentTheme } = useTheme();
    const colors = useThemeColours();
    const bgColor = colors.background;
    const textColor = colors.text;
    const subtitleColor = colors.secondaryText;
    const accentColor = colors.accent;
    const borderColor = colors.border;
    
    // Use disabled state to determine if this button is selected/active
    const isSelected = disabled;
    
    return (
       <TouchableOpacity
              onPress={onPress}
              disabled={disabled}
              style={{
                borderWidth: 2,
                borderColor: isSelected ? accentColor : borderColor,
                backgroundColor: isSelected ? accentColor : colors.card,
                borderRadius: 32,
                paddingVertical: 16,
                paddingHorizontal: 24,
                marginBottom: 16,
                flexDirection: 'row',
                alignItems: 'center',
                opacity: disabled ? 1 : 1,
              }}
            >
              <View style={{
                width: 40,
                height: 40,
                borderRadius: 12,
                backgroundColor: isSelected ? colors.background : colors.card,
                alignItems: 'center',
                justifyContent: 'center',
                marginRight: 16
              }}>
                {/* Icon */}
                {Icon ? <Icon size={20} color={isSelected ? accentColor : colors.secondaryText} strokeWidth={2} /> : null}
                
              </View>
              <Text style={{ 
                color: isSelected ? colors.background : textColor,
                fontSize: 16,
                fontWeight: '600'
              }}>
                { title }
              </Text>
        </TouchableOpacity>
    );
}; 

export default THRDIconButton;