import React, { useState } from "react";
import { Tabs } from "expo-router";
import { Feather } from "@expo/vector-icons";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ProfileOverlay } from "@/app/(app)/(tabs)/(profile)/profile";

const TabsLayout = () => {
  const colors = useThemeColours();
  const [profileOverlayVisible, setProfileOverlayVisible] = useState(false);

  return (
    <>
      <ProfileOverlay
        visible={profileOverlayVisible}
        onClose={() => setProfileOverlayVisible(false)}
      />
      <Tabs
        initialRouteName="(home)"
        screenOptions={{
            tabBarActiveTintColor: colors.primary,
            tabBarInactiveTintColor: colors.secondaryText,
            tabBarStyle: {
              backgroundColor: colors.background,
              borderTopColor: colors.border,
            },
            headerShown: false,
        }}>
        <Tabs.Screen
          name="(calendar)"
          options={{
            tabBarLabel: "Calendar",
            tabBarIcon: ({ color, size }) => (  
              <Feather name="calendar" size={size} color={color} />
            ),
          }}
        />
        <Tabs.Screen
          name="(explore)"
          options={{
            tabBarLabel: "Explore",
            tabBarIcon: ({ color, size }) => (  
              <Feather name="search" size={size} color={color} />
            ),
          }}
        />
        <Tabs.Screen
          name="(home)"
          options={{
            tabBarLabel: "Home",
            tabBarIcon: ({ color, size }) => (  
              <Feather name="home" size={size} color={color} />
            ),
          }}
        />
        
        <Tabs.Screen
          name="(chat)"
          options={{
            tabBarLabel: "Chat",
            tabBarIcon: ({ color, size }) => (  
              <Feather name="message-circle" size={size} color={color} />
            ),
          }}
        />
        <Tabs.Screen
          name="(profile)"
          options={{
            tabBarLabel: "Profile",
            tabBarIcon: ({ color, size }) => (  
              <Feather name="user" size={size} color={color} />
            ),
          }}
        />
      </Tabs>
    </>
    
  );
};

export default TabsLayout;
