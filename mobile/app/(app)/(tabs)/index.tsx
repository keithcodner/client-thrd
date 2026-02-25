import React from "react";
import { Platform, Text, View, Alert } from "react-native";
import { useSession } from "@/context/AuthContext";
import { Button } from "@react-navigation/elements";
import { API_BASE_URL } from "@/config/env";
import axios from "axios";

const Main = () => {
  const { user, signOut } = useSession();

  const handleLogout = async () => {
    if (Platform.OS === 'web') {
      if (window.confirm('Are you sure you want to log out?')) {
        try {
          if (signOut) await signOut();
        } catch (error) {
          console.error("Logout failed", error);
        }
      }
    } else {
      Alert.alert(
        'Confirm Logout',
        'Are you sure you want to log out?',
        [
          { text: 'Cancel', style: 'cancel' },
          { text: 'Log Out', style: 'destructive', onPress: async () => {
              try {
                if (signOut) await signOut();
              } catch (error) {
                console.error("Logout failed", error);
              }
            } 
          },
        ],
        { cancelable: true }
      )
    }
    
  };

  return (
    <View>
      <Text>Welcome, {user?.name}</Text>
      <Button onPress={handleLogout} >
        Log out
      </Button>
    </View>
  );
};

export default Main;
