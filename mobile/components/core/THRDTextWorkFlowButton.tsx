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

const THRDTextWorkFlowButton: React.FC<ButtonProps> = ({
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
       
    );
}; 

export default THRDTextWorkFlowButton;