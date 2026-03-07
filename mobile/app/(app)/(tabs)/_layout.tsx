import React from "react";
import { Tabs } from "expo-router";
import { Feather } from "@expo/vector-icons";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ProfileOverlay } from "@/app/(app)/(tabs)/(profile)/profile";
import { ProfileOverlayProvider, useProfileOverlay } from "@/context/ProfileOverlayContext";

const TabsLayoutContent = () => {
  const colors = useThemeColours();
  const { isVisible, openProfileOverlay, closeProfileOverlay } = useProfileOverlay();

  return (
    <>
      <ProfileOverlay
        visible={isVisible}
        onClose={closeProfileOverlay}
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
        }}
        screenListeners={{
          tabPress: (e) => {
            // Intercept profile tab press
            if (e.target?.includes('(profile)')) {
              e.preventDefault();
              openProfileOverlay();
            }
          },
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

const TabsLayout = () => {
  return (
    <ProfileOverlayProvider>
      <TabsLayoutContent />
    </ProfileOverlayProvider>
  );
};

export default TabsLayout;
