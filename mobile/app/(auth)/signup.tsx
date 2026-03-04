import React, { useState } from "react";
import { Text, View, Image, Alert, ActivityIndicator } from "react-native";

import Input from "@/components/core/Input";
import Button from "@/components/core/Button";

import axios from "axios";
import { useRouter, Link } from "expo-router";
import { API_BASE_URL } from "@/config/env";
import { useTheme } from "@/context/ThemeContext";

const SignUp = () => {
    const { currentTheme } = useTheme();
    const [data, setData] = useState({
        name: "",
        email: "",  
        password: "",
        password_confirmation: "",
    });
    const [errors, setErrors] = useState({
        name: "",
        email: "",  
        password: "",
        password_confirmation: "",
    });
    const [loading, setIsLoading] = useState(false);
    const [successMessage, setSuccessMessage] = useState("");

    const handleChange = (key: string, value: string) => {
        setData({ ...data, [key]: value });
    };

    const handleSignup = async() => {
        setIsLoading(true);
        setErrors({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        });

        try {
            await axios.post(`${API_BASE_URL}/register`, data);
            resetForm();
            setSuccessMessage("Account created successfully! Please log in.");
        } catch (error) {
            if (axios.isAxiosError(error)) {
                const responseData = error.response?.data;
                if (responseData?.errors) {
                    // responseData.errors is often an object (map) of arrays — convert to readable string
                    try {
                        const firstError = Object.values(responseData.errors).flat()[0];
                        Alert.alert("Error", String(firstError));
                    } catch (e) {
                        Alert.alert("Error", JSON.stringify(responseData.errors));
                    }
                } else if (responseData?.message) {
                    // message may be a string or object — ensure we pass a string
                    const msg = typeof responseData.message === "string" ? responseData.message : JSON.stringify(responseData.message);
                    Alert.alert("Error", msg);
                } else {
                    Alert.alert("Error", 'An unexpected error occurred. Please try again.');
                }
            } else {
                console.log("An unexpected error occurred", error);
            }

        } finally {
            setIsLoading(false);
        }
    };

    const resetForm = () => {
        setData({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        });
        setErrors({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
        });
    };

    return(
        <View className={`flex-1 justify-center items-center px-6 ${currentTheme === "dark" ? "bg-gray-900" : "bg-white"}`}>
            <View className="items-center mb-8">
                <Text className={`text-2xl font-bold mt-4 ${currentTheme === "dark" ? "bg-gray-900" : "bg-white"}`}> Sign up Test Text goes here</Text>
            </View>

            <Text className={`text-3xl font-bold mt-5 ${currentTheme === "dark" ? "bg-gray-900" : "bg-white"}`}> Sign up</Text>

            {!!successMessage &&  <Text className="bg-emerald-600 text-white rounded-lg py-3 px-4 mb-4">{successMessage}</Text>}

            <Input
                value={data.name}
                placeholder="Name"
                onChangeText={(value) => handleChange("name", value)}
                error={errors.name}
            />

            <Input
                value={data.email}
                placeholder="Email" 
                keyboardType="email-address"
                onChangeText={(value) => handleChange("email", value)}
                error={errors.email}
            />
            <Input
                value={data.password}
                placeholder="Password"
                secureTextEntry
                onChangeText={(value) => handleChange("password", value)}
                error={errors.password}
            />
            <Input
                value={data.password_confirmation}
                placeholder="Confirm Password"
                secureTextEntry
                onChangeText={(value) => handleChange("password_confirmation", value)}
                error={errors.password_confirmation}
            />

            <Button
                title="Sign Up"
                onPress={handleSignup}
                loading={loading} // show loading indicator when loading
                disabled={loading} // disabled when loading
                className="w-full mt-4"
            >
                <View className="flex-row items-center justify-center">
                    
                    <Text className="text-base font-medium text-white">Sign Up</Text>
                </View>
            </Button>
            <Text className={`text-lg ${currentTheme === "dark" ? "text-gray-400" : "text-gray-600"} mt-5`}>
                Already have an account?{" "}
                <Link href="/sign-in" className="text-blue-500 font-medium">
                    <Text>Sign-in</Text>
                </Link>
            </Text>
        </View>
    );
};

export default SignUp;