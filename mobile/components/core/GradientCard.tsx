import React, { ReactNode } from "react";
import { Platform, Text, View, Alert, ViewStyle, StyleSheet, TouchableOpacity } from "react-native";
import { LinearGradient } from "expo-linear-gradient";
import { useThemeColours } from "@/hooks/useThemeColours";


interface GradientCardProps {
    onPress?: () => void;
    gradientColours?: readonly [string, string, ...string[]];
    children: ReactNode;
    badgeText?: string;
    badgeVisible?: boolean;
    style?: ViewStyle;
    disabled?: boolean;
    shadowColour?: string;
}

const GradientCard: React.FC<GradientCardProps> = ({
    onPress,
    gradientColours,
    children,
    badgeText = 'Popular',
    badgeVisible = false,
    style,
    disabled = false,
    shadowColour
}) => {
    const colors = useThemeColours();

    // Use theme colors as fallback for gradient
    const defaultGradient:readonly [string, string, ...string[]] = [colors.card, colors.surface];
    const cardGradient = gradientColours || defaultGradient;
    const cardShadowColour = shadowColour|| colors.primary;

    return (
        <TouchableOpacity
            onPress={onPress}
            disabled={disabled || !onPress}
            className="mb-4"
            style={[styles.container, { 
                shadowColor: cardShadowColour,
                opacity: disabled ? 0.5 : 1
            }, style]}
        >
            <LinearGradient
                colors={cardGradient}
                start={{ x:0, y:0 }}
                end={{ x:0, y:0 }}
                className="p-6 border border-gray-200 dark:border-gray-700"
                style={styles.gradient}
            >
                {badgeVisible && (
                    <View 
                    className="absolute top-0 right-0 px-3 py-1 rounded-b1-2xl"
                    style={{ backgroundColor: colors.primary }}>
                        <Text className="text-white text-sm font-bold">{badgeText}</Text>
                    </View>
                )}
                {children}
            </LinearGradient>

        </TouchableOpacity>
    );

};

const styles = StyleSheet.create({
    container:{
        shadowOffset: { width: 0, height: 0 },
        shadowOpacity: 0.3,
        shadowRadius: 10,
        elevation: 5,
    },
    gradient: {
        borderRadius: 20,
        overflow: "hidden",
    }
});

export default GradientCard;