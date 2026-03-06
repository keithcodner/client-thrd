import React from "react";
import { View, Button, Text, ScrollView, Platform, Alert, TouchableOpacity } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { useSession } from '@/context/AuthContext';

const Profile = () => {
  const colours = useThemeColours();

  const { user, signOut } = useSession();
  const colors = useThemeColours();

  const handleLogout = () => {
    if (Platform.OS === 'web') {
      // For web browsers
      if (window.confirm('Are you sure you want to logout?')) {
        signOut?.();
      }
    } else {
      // Logic for other platforms goes here
      // For mobile devices
      Alert.alert(
        'Logout',
        'Are you sure you want to logout?',
        [
          {
            text: 'Cancel',
            style: 'cancel',
          },
          {
            text: 'Logout',
            style: 'destructive',
            onPress: () => signOut?.(),
          }
        ],
      );
    }
  };

  return (
    <View className="flex-1 bg-white dark:bg-gray-900">
      <ScrollView className="flex-1">
        <View className="p-4 mt-10">
          <Text className="text-2xl font-bold text-gray-800 dark:text-white">
            Profile
          </Text>
          <Text className="text-gray-600 dark:text-gray-400 mt-2">
            Welcome to THRD Profile Section
          </Text>

          <TouchableOpacity
            onPress={handleLogout}
            className="rounded-2xl shadow-lg"
            >
                <Text className="text-lg font-bold">Logout</Text>
        </TouchableOpacity>
        </View>
      </ScrollView>
    </View>
  );
};

export default Profile;
