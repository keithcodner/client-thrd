import React from "react";
import { TouchableOpacity, Text, View, ActivityIndicator } from "react-native";
import { LinearGradient } from "expo-linear-gradient";

interface ButtonProps {
  title?: string;
  className?: string;
  disabled?: boolean;
  loading?: boolean;
  variant?: "primary" | "secondary" | "tertiary";
  onPress: () => void;
  children?: React.ReactNode;
}

const Button: React.FC<ButtonProps> = ({
    title,
    className = "",
    disabled = false,
    loading = false,
    variant = "primary",
    onPress,
    children
}) => { 
    const getVarientStyles = () => {
        switch (variant) {
            case "primary":
                return {
                    gradient: ["#4c669f", "#3b5998", "#192f6a"],
                    textColor: "#fff"
                };
            case "secondary":
                return {
                    gradient: ["#e0e0e0", "#bdbdbd"],
                    textColor: "#424242"
                };
            case "tertiary":
                return {
                    gradient: ["#ffffff", "#ffffff"],
                    textColor: "#1e90ff",
                };
            default:
                return {
                    gradient: ["#4c669f", "#3b5998", "#192f6a"],
                    textColor: "#fff"
                };
        }
    };

    const { gradient, textColor } = getVarientStyles();

    return (
        <TouchableOpacity
            onPress={onPress}
            disabled={disabled || loading}
            className={`rounded-lg px-4 py-2 items-center justify-center ${className} ${disabled || loading ? "opacity-50" : ""}`}
            style={{ elevation: disabled || loading ? 0 : 2 }}
        >
            <LinearGradient
                colors={(disabled ? ['#e0e0e0', '#bdbdbd'] : gradient) as [string, string]}
                style={{
                    borderRadius: 8,
                    width: '100%',
                    height: '100%',
                    alignItems: 'center',
                    justifyContent: 'center',
                    flexDirection: 'row',
                    paddingVertical: 8,
                    paddingHorizontal: 16,
                }}
            >
                {loading ? (
                    <ActivityIndicator size="small" color={textColor} />
                ) : (
                    children ? children : <Text className="text-base font-medium" style={{ color: textColor }}>{title}</Text>
                )}
            </LinearGradient>
        </TouchableOpacity>
    );
}; 

export default Button;