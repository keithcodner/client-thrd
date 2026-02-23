import React, { useState } from "react";
import { TextInput, StyleSheet } from "react-native";

import { TextInputProps, Text, View } from "react-native";
import { useTheme } from "@/context/ThemeContext";

interface InputProps extends TextInputProps {
    label?: string;
    error?: string;
}

const Input = ({
    value, 
    placeholder,
    keyboardType,
    secureTextEntry,
    onChangeText,
    error=""
}: InputProps) => {

    const [isFocused, setIsFocused] = useState(false);
    const { currentTheme } = useTheme();

    return (
        <View className="w-full mb-4">
            <TextInput 
                className={`w-full h-12 border routed-lg px-3 mb-1
                    ${currentTheme === "dark" ? "bg-gray-800 border-gray-700 text-white" : "bg-white border-gray-300 text-black"}
                    ${isFocused ? "border-primary" : currentTheme === "dark" ? "border-gray-700" : "border-gray-300"}
                    ${error ? "border-red-500" : ""}`} 
                    value={value}
                    placeholder={placeholder}
                    placeholderTextColor={currentTheme === 'dark' ? 'gray' : '#9ca3af'}
                    keyboardType={keyboardType}
                    secureTextEntry={secureTextEntry}
                    onChangeText={onChangeText}
                    onFocus={() => setIsFocused(true)}
                    onBlur={() => setIsFocused(false)}
            />
            { !!error && <Text className="text-red-500 text-sm">{error}</Text> }
        </View>
    );
}

export default Input;