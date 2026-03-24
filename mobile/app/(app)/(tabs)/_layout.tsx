import React from "react";
import { Tabs } from "expo-router";
import { Feather } from "@expo/vector-icons";
import { View, Text } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { UnreadMessagesProvider, useUnreadMessagesContext } from "@/context/UnreadMessagesContext";
import { ProfileOverlay } from "@/app/(app)/(tabs)/(profile)/profile";
import { ProfileOverlayProvider, useProfileOverlay } from "@/context/ProfileOverlayContext";
import { FABProvider } from "@/context/FABContext";

// Badge component for tab icons
const TabBadge = ({ count }: { count: number }) => {
  if (count === 0) return null;
  
  return (
    <View
      style={{
        position: 'absolute',
        right: -6,
        top: -3,
        backgroundColor: '#FF3B30',
        borderRadius: 10,
        minWidth: 20,
        height: 20,
        justifyContent: 'center',
        alignItems: 'center',
        paddingHorizontal: 4,
      }}
    >
      <Text
        style={{
          color: 'white',
          fontSize: 12,
          fontWeight: '600',
        }}
      >
        {count > 99 ? '99+' : count}
      </Text>
    </View>
  );
};

const TabsLayoutContent = () => {
  const colors = useThemeColours();
  const { isVisible, openProfileOverlay, closeProfileOverlay } = useProfileOverlay();
  const { totalUnread } = useUnreadMessagesContext();

  return (
    <>
      <ProfileOverlay
        visible={isVisible}
        onClose={closeProfileOverlay}
      />
      <Tabs
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
              <View>
                <Feather name="message-circle" size={size} color={color} />
                <TabBadge count={totalUnread} />
              </View>
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
    <FABProvider>
      <ProfileOverlayProvider>
        <UnreadMessagesProvider>
          <TabsLayoutContent />
        </UnreadMessagesProvider>
      </ProfileOverlayProvider>
    </FABProvider>
  );
};

export default TabsLayout;
