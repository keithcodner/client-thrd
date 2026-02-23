import React, { useState } from "react";
import { Text, View, Image, Alert, ActivityIndicator } from "react-native";

import Input from "@/components/core/Input";
import Button from "@/components/core/Button";

import axios from "axios";
import axiosInstance from "../config/axiosConfig";
import { useRouter, Link, router } from "expo-router";
import { useSession } from "@/context/AuthContext";
import { API_BASE_URL } from "@/config/env";
import { useTheme } from "@/context/ThemeContext";

const Login = () => {

  const { signIn } = useSession();
  const { currentTheme } = useTheme();
  const [loading, setIsLoading] = useState(false);
  const [successMessage, setSuccessMessage] = useState("");

  const [data, setData] = useState({
      email: "",  
      password: "",
  });
  const [errors, setErrors] = useState({
      email: "",  
      password: "",
  });

  const handleChange = (key: string, value: string | any) => {
        setData({ ...data, [key]: value });
        setErrors({ ...errors, [key]: "" }); // Clear error for this field on change
    };

  const handleLogin = async() => {
      setIsLoading(true);
      setErrors({
          email: "",
          password: "",
      });

      try {
          const response = await axios.post(`${API_BASE_URL}/login`, data);
          await signIn(response.data.token, response.data.user);
          
          router.replace("/");
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
              console.log("Error", error);
              Alert.alert("Error", 'Unable to conntct to the server');
          }

      } finally {
          setIsLoading(false);
      }
  };

  return(
    <View className={`flex-1 justify-center items-center px-6 ${currentTheme === "dark" ? "bg-gray-900" : "bg-white"}`}>
      <View className="items-center mb-8">
          <Text className={`text-2xl font-bold mt-4 ${currentTheme === "dark" ? "bg-gray-900" : "bg-white"}`}>Login Test Text goes here</Text>
      </View>
      <Text className={`text-3xl font-bold mt-5 ${currentTheme === "dark" ? "bg-gray-900" : "bg-white"}`}> Login</Text>
      
      {!!successMessage &&  <Text className="bg-emerald-600 text-white rounded-lg py-3 px-4 mb-4">{successMessage}</Text>}

      <Input
          value={data.email}
          onChange={(value) => handleChange("email", value)}
          placeholder="Email"
          keyboardType="email-address"
          autoCapitalize="none"
          error={errors.email}
      />
      <Input
          value={data.password}
          onChange={(value) => handleChange("password", value)}
          placeholder="Password"
          secureTextEntry
          error={errors.password}
      />

      <Button
          title="Login"
          onPress={handleLogin}
          loading={loading} // show loading indicator when loading
          disabled={loading} // disabled when loading
          className="w-full mt-4"
      >
          <View className="flex-row items-center justify-center">
              {loading && <ActivityIndicator size="small" color="#ffffff" className="mr-2" />}
              <Text className="text-white font-semibold">Login</Text>
          </View>
      </Button>
      <Text className={`text-lg ${currentTheme === "dark" ? "text-gray-400" : "text-gray-600"} mt-5`}>
          Don't have an account?{" "}
          <Link href="/signup" className="text-blue-500 font-medium">
              <Text>Sign-up</Text>
          </Link>
      </Text>
    </View>
  );
};

export default Login;