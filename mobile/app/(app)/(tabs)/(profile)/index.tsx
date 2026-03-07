import React, { useEffect } from "react";
import { View } from "react-native";
import { useRouter } from "expo-router";
import { useProfileOverlay } from "@/context/ProfileOverlayContext";

const Profile = () => {
  const router = useRouter();
  const { openProfileOverlay } = useProfileOverlay();

  useEffect(() => {
    // Open overlay and navigate back to home
    openProfileOverlay();
    router.replace("/(app)/(tabs)/(home)");
  }, []);

  return <View />;
};

export default Profile;
