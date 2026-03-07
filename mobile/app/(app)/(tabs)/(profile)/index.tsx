import React, { useState } from "react";
import { View, Text, ScrollView, Platform, Alert, TouchableOpacity } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { useSession } from "@/context/AuthContext";
import { ProfileOverlay } from "./profile";

const Profile = () => {
  const colors = useThemeColours();
  const { user, signOut } = useSession();
  const [profileOverlayVisible, setProfileOverlayVisible] = useState(false);

  const handleLogout = () => {
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
            onPress: () => signOut?.(),
          },
        ]
      );
    }
  };

  const handleCloseProfileOverlay = () => {
    setProfileOverlayVisible(false);
  };

  return (
    <>
      <View style={{ flex: 1, backgroundColor: colors.background }}>
        <ScrollView style={{ flex: 1 }}>
          <View style={{ padding: 16, marginTop: 40 }}>
            <Text
              style={[
                {
                  fontSize: 24,
                  fontWeight: "700",
                  color: colors.text,
                  marginBottom: 8,
                },
              ]}
            >
              Profile
            </Text>
            <Text
              style={[
                {
                  fontSize: 14,
                  color: colors.secondaryText,
                  marginBottom: 24,
                },
              ]}
            >
              Welcome to THRD Profile Section
            </Text>

            <TouchableOpacity
              onPress={() => setProfileOverlayVisible(true)}
              style={{
                backgroundColor: colors.card,
                borderColor: colors.border,
                borderWidth: 1,
                borderRadius: 16,
                padding: 16,
              }}
            >
              <Text
                style={{
                  fontSize: 14,
                  fontWeight: "600",
                  color: colors.text,
                }}
              >
                Open Profile
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              onPress={handleLogout}
              style={{
                backgroundColor: colors.card,
                borderColor: colors.error,
                borderWidth: 1,
                borderRadius: 16,
                padding: 16,
                marginTop: 12,
              }}
            >
              <Text
                style={{
                  fontSize: 14,
                  fontWeight: "600",
                  color: colors.error,
                  textAlign: "center",
                }}
              >
                Logout
              </Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
      </View>

      <ProfileOverlay
        visible={profileOverlayVisible}
        onClose={handleCloseProfileOverlay}
      />
    </>
  );
};

export default Profile;
