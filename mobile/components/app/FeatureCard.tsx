import React from "react";
import { Text, View } from "react-native";
import { MaterialIcons } from "@expo/vector-icons";
import GradientCard from "../core/GradientCard";
import { useThemeColours } from "@/hooks/useThemeColours";


export interface FeatureCardProps {
    title: string;
    description: string;
    icon: keyof typeof MaterialIcons.glyphMap;
    credits?: number | string;
    onPress?: () => void;
    gradient?: readonly [string, string, ...string[]];
    disabled?: boolean;
}

const FeatureCard: React.FC<FeatureCardProps> = ({
   title,
   description,
    icon,
    credits,
    onPress,
    gradient,
    disabled = false
}) => {
    const colours = useThemeColours();

    return(
        <GradientCard
            onPress={onPress}
            gradientColours={gradient || [colours.primary, colours.accent] as readonly [string, string, ...string[]]}
            disabled={disabled} 
            style={{ width: '48%', marginBottom: 16 }}
            >
                <View className="items-center mb-4">
                    <MaterialIcons name={icon} size={40} color={colours.primary}/>
                </View>
                <View className="items-center">
                    <Text className="text-lg font-bold text-white">{title}</Text>
                    <Text className="text-sm text-white">{description}</Text>
                    {credits !== undefined && (
                        <Text className="text-sm text-white mt-2">
                            {credits} credits
                        </Text>
                    )}
                </View>

        </GradientCard>
    );
};

export default FeatureCard;