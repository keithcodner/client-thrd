import React, { useState } from "react";
import { useRouter } from "expo-router";
import { Platform, Alert } from "react-native";
import { useSession } from "@/context/AuthContext";
import { ProfileOverlay } from "./profile";

const Profile = () => {
  const router = useRouter();
  const { signOut } = useSession();

  const handleCloseProfileOverlay = () => {
    router.back();
  };

  const handleSignOut = () => {
    if (Platform.OS === "web") {
      if (window.confirm("Are you sure you want to logout?")) {
        signOut?.();
      }
    } else {
      Alert.alert(
        "Logout",
        "Are you sure you want to logout?",
        [
          {
            text: "Cancel",
            style: "cancel",
          },
          {
            text: "Logout",
            style: "destructive",
            onPress: () => {
              signOut?.();
            },
          },
        ]
      );
    }
  };

  return (
    <ProfileOverlay
      visible={true}
      onClose={handleCloseProfileOverlay}
      onSignOut={handleSignOut}
    />
  );
};

export default Profile;
