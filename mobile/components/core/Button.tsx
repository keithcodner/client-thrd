import React from "react";
import { TouchableOpacity, Text, View, ActivityIndicator } from "react-native";
import { LinearGradient } from "expo-linear-gradient";

interface ButtonProps {
  title?: string;
  className?: string;
  disabled?: boolean;
  loading?: boolean;
  variant?: "primary" | "secondary" | "tertiary" | "danger";
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
            case "danger":
                return {
                    gradient: ["#ff4c4c", "#ff1a1a"],
                    textColor: "#fff",
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
            className={`overflow-hidden rounded-2xl  ${disabled ? "opacity-50" : ""} ${className}`}
            style={{ elevation: 3 }}
        >
            <LinearGradient
                colors={(disabled ? ['#e0e0e0', '#bdbdbd'] : gradient) as [string, string]}
                start={{ x:0, y:0 }}
                end={{ x:1, y:1 }}
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