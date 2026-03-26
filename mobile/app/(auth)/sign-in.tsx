import React, { useState } from "react";
import { Text, View, TextInput, TouchableOpacity, Alert, ActivityIndicator, Platform } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Link } from "expo-router";
import axios from "axios";
import axiosInstance from "@/config/axiosConfig";
import { useRouter, router } from "expo-router";
import { useSession } from "@/context/AuthContext";
import { useThemeColours } from "@/hooks/useThemeColours";
import { API_BASE_URL } from "@/config/env";

const Login = () => {
  const colors = useThemeColours();
  const { signIn } = useSession();
  const [loading, setIsLoading] = useState(false);

  const [data, setData] = useState({
      username: "",  
      password: "",
  });
  const [errors, setErrors] = useState({
      username: "",  
      password: "",
  });

  const handleChange = (key: string, value: string) => {
    setData({ ...data, [key]: value });
  };

  const handleLogin = async() => {
    setIsLoading(true);
    setErrors({
        username: "",
        password: "",
    });

    try {
        const response = await axiosInstance.post(`${API_BASE_URL}/login`, {
          email: data.username,
          password: data.password
        });
        await signIn(response.data.token, response.data.user);
        
        router.replace("/");
    } catch (error) {
        if (axios.isAxiosError(error)) {
            const responseData = error.response?.data;
            if (responseData?.errors) {
                try {
                    const firstError = Object.values(responseData.errors).flat()[0];
                    Alert.alert("Error", String(firstError));
                } catch (e) {
                    Alert.alert("Error", JSON.stringify(responseData.errors));
                }
            } else if (responseData?.message) {
                const msg = typeof responseData.message === "string" ? responseData.message : JSON.stringify(responseData.message);
                Alert.alert("Error", msg);
            } else {
                Alert.alert("Error", 'An unexpected error occurred. Please try again.');
            }
        } else {
            console.log("Error", error);
            Alert.alert("Error", 'Unable to connect to the server');
        }
    } finally {
        setIsLoading(false);
    }
  };

  return(
    <SafeAreaView style={{ flex: 1, backgroundColor: colors.background }}>
      <View style={{ flex: 1, justifyContent: 'center', paddingHorizontal: 32 }}>
        {/* Title */}
        <Text
          style={{
            color: colors.text,
            fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
            fontSize: 32,
            fontWeight: '400',
            textAlign: 'center',
            marginBottom: 32,
          }}
        >
          Login
        </Text>

        {/* Email Input */}
        <View style={{ marginBottom: 16 }}>
          <Text
            style={{
              color: colors.secondaryText,
              fontSize: 12,
              fontWeight: '600',
              letterSpacing: 1,
              marginBottom: 8,
            }}
          >
            USERNAME
          </Text>
          <TextInput
            testID="login-email-input"
            value={data.username}
            onChangeText={(value) => handleChange("username", value)}
            placeholder="Enter your username"
            placeholderTextColor={colors.secondaryText}
            autoCapitalize="none"
            style={{
              backgroundColor: colors.card,
              color: colors.text,
              fontSize: 16,
              paddingVertical: 16,
              paddingHorizontal: 16,
              borderRadius: 16,
            }}
          />
        </View>

        {/* Password Input */}
        <View style={{ marginBottom: 32 }}>
          <Text
            style={{
              color: colors.secondaryText,
              fontSize: 12,
              fontWeight: '600',
              letterSpacing: 1,
              marginBottom: 8,
            }}
          >
            PASSWORD
          </Text>
          <TextInput
            testID="login-password-input"
            value={data.password}
            onChangeText={(value) => handleChange("password", value)}
            placeholder="Enter your password"
            placeholderTextColor={colors.secondaryText}
            secureTextEntry
            style={{
              backgroundColor: colors.card,
              color: colors.text,
              fontSize: 16,
              paddingVertical: 16,
              paddingHorizontal: 16,
              borderRadius: 16,
            }}
          />
        </View>

        {/* Login Button */}
        <TouchableOpacity
          testID="login-submit-button"
          onPress={handleLogin}
          disabled={loading}
          style={{
            backgroundColor: colors.text,
            width: '100%',
            borderRadius: 24,
            paddingVertical: 16,
            paddingHorizontal: 32,
            opacity: loading ? 0.7 : 1,
          }}
        >
          <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'center' }}>
            {loading && <ActivityIndicator size="small" color={colors.background} style={{ marginRight: 8 }} />}
            <Text style={{
              color: colors.background,
              textAlign: 'center',
              fontSize: 16,
              fontWeight: '700'
            }}>
              {loading ? 'Logging in...' : 'Login'}
            </Text>
          </View>
        </TouchableOpacity>

        {/* Sign Up Link */}
        <View style={{ marginTop: 24, alignItems: 'center' }}>
          <Text style={{ color: colors.secondaryText, fontSize: 14 }}>
            Don't have an account?{" "}
            <Link href="/(auth)/register-wizard">
              <Text style={{ color: colors.text, fontWeight: '600', textDecorationLine: 'underline' }}>Sign up</Text>
            </Link>
          </Text>
        </View>
      </View>
    </SafeAreaView>
  );
};

export default Login;