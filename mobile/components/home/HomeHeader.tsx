import React from "react";
import { View, Text, Pressable, StyleSheet } from "react-native";
import { Bell, Settings, User } from "lucide-react-native";

interface HomeHeaderProps {
  colors: any;
  notificationsCount?: number;
  onNavigate?: (screen: string) => void;
  onOpenNotifications?: () => void;
  onOpenProfile?: () => void;
}

export const HomeHeader = ({
  colors,
  notificationsCount = 0,
  onNavigate = () => {},
  onOpenNotifications = () => {},
  onOpenProfile = () => {},
}: HomeHeaderProps) => {
  return (
    <View style={styles.header}>
      <Pressable style={styles.logo}>
        <View style={[styles.logoBox, { backgroundColor: colors.primary }]}>
          <Text
            style={[styles.logoText, { color: colors.background }]}
            className="font-serif"
          >
            T
          </Text>
        </View>
        <Text style={[styles.logoWord, { color: colors.text }]}>THRD</Text>
      </Pressable>

      <View style={styles.headerActions}>
        <Pressable
          style={[styles.iconButton, { backgroundColor: colors.card }]}
          onPress={() => onNavigate("settings")}
        >
          <Settings size={20} color={colors.secondaryText} />
        </Pressable>

        <Pressable
          style={[styles.iconButton, { backgroundColor: colors.card }]}
          onPress={onOpenNotifications}
        >
          <Bell size={20} color={colors.secondaryText} />
          {notificationsCount > 0 && (
            <View style={styles.notificationBadge}>
              <Text style={styles.badgeText}>
                {notificationsCount > 9 ? "9+" : notificationsCount}
              </Text>
            </View>
          )}
        </Pressable>

        <Pressable
          style={[styles.iconButton, { backgroundColor: colors.card }]}
          onPress={onOpenProfile}
        >
          <User size={20} color={colors.secondaryText} />
        </Pressable>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingTop: 16,
    paddingBottom: 16,
  },

  logo: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
  },

  logoBox: {
    width: 32,
    height: 32,
    justifyContent: "center",
    alignItems: "center",
    borderRadius: 6,
  },

  logoText: {
    fontWeight: "700",
    fontSize: 18,
  },

  logoWord: {
    fontWeight: "700",
    fontSize: 16,
  },

  headerActions: {
    flexDirection: "row",
    gap: 12,
    alignItems: "center",
  },

  iconButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: "center",
    alignItems: "center",
  },

  notificationBadge: {
    position: "absolute",
    top: -4,
    right: -4,
    backgroundColor: "#ADC178",
    borderRadius: 10,
    minWidth: 20,
    height: 20,
    justifyContent: "center",
    alignItems: "center",
  },

  badgeText: {
    color: "#15110fff",
    fontSize: 10,
    fontWeight: "700",
  },
});
