import React from "react";
import { useRouter } from "expo-router";
import { ProfileOverlay } from "./profile";

const Profile = () => {
  const router = useRouter();

  const handleCloseProfileOverlay = () => {
    router.push("/(app)/(tabs)/(home)");
  };

  return (
    <ProfileOverlay
      visible={true}
      onClose={handleCloseProfileOverlay}
    />
  );
};

export default Profile;
