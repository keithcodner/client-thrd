import React from "react";
import {
  Modal,
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  Platform,
  Alert,
} from "react-native";
import { BlurView } from "expo-blur";
import {
  Edit2,
  X,
  ListTodo,
  Clock,
  Bookmark,
  HelpCircle,
  MessageSquare,
  Shield,
  ChevronRight,
  Sun,
  Moon,
  Monitor,
} from "lucide-react-native";
import { useRouter } from 'expo-router';
import { useThemeColours } from "@/hooks/useThemeColours";
import { useTheme } from "@/context/ThemeContext";
import { useSession } from "@/context/AuthContext";

interface ProfileOverlayProps {
  visible: boolean;
  onClose: () => void;
}

export const ProfileOverlay = ({ visible, onClose }: ProfileOverlayProps) => {
  const colors = useThemeColours();
  const router = useRouter();
  const { theme, setTheme } = useTheme();
  const { user, signOut } = useSession();

  const userName = user?.name || "";
  const userHandle = user?.email?.split("@")[0] || "";

  const initials = userName
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase();

  const handleSignOut = () => {
    if (Platform.OS === "web") {
      if (window.confirm("Are you sure you want to logout?")) {
        signOut?.();
      }
    } else {
      Alert.alert("Logout", "Are you sure you want to logout?", [
        { text: "Cancel", style: "cancel" },
        { text: "Logout", style: "destructive", onPress: () => signOut?.() },
      ]);
    }
  };

  const handleSupportClick = () => {
    //router.push(`/(app)/(help-center)`);
    router.push('/(app)/(help-center)');
    onClose();
  };

  return (
    <Modal transparent animationType="fade" visible={visible}>
      <BlurView intensity={100} tint="dark" style={styles.blurContainer}>
        <View style={[styles.modalContent, { backgroundColor: colors.background, borderColor: colors.border }]}>
          {/* Top Buttons */}
          <View style={styles.topButtons}>
            <TouchableOpacity style={styles.iconButton}>
              <Edit2 size={18} color="#9c9797" />
            </TouchableOpacity>

            <TouchableOpacity style={styles.iconButton} onPress={onClose}>
              <X size={20} color="#9c9797" />
            </TouchableOpacity>
          </View>

          <ScrollView showsVerticalScrollIndicator={false} bounces={false}>
            {/* Profile Header */}
            <View style={styles.profileSection}>
              <View style={[styles.avatar, { backgroundColor: colors.primary, borderColor: colors.border }]}>
                <Text style={styles.avatarText}>{initials}</Text>
              </View>

              <Text style={[styles.name, { color: colors.text }]}>{userName}</Text>
              <Text style={[styles.username, { color: colors.secondaryText }]}>@{userHandle}</Text>

              <Text style={[styles.member, { color: colors.secondaryText }]}>MEMBER</Text>
            </View>

            {/* Action Buttons */}
            <View style={styles.actionsRow}>
              <TouchableOpacity style={[styles.actionButton, { borderColor: colors.border, backgroundColor: colors.card }]}>
                <View style={styles.iconCircleGreen}>
                  <ListTodo size={18} color="#34D399" />
                </View>
                <Text style={[styles.actionText, { color: colors.secondaryText }]}>MY PLANNER</Text>
              </TouchableOpacity>

              <TouchableOpacity style={[styles.actionButton, { borderColor: colors.border, backgroundColor: colors.card }]} onPress={handleSupportClick}>
                <View style={styles.iconCircleBlue}>
                  <Clock size={18} color="#3BA6FF" />
                </View>
                <Text style={[styles.actionText, { color: colors.secondaryText }]}>SUPPORT</Text>
              </TouchableOpacity>
            </View>

            {/* Saved Collection */}
            <View style={styles.sectionHeader}>
              <Text style={[styles.sectionTitle, { color: colors.secondaryText }]}>SAVED COLLECTION</Text>
              <Text style={[styles.discovery, { color: colors.success }]}>DISCOVERY ›</Text>
            </View>

            <View style={[styles.collectionCard, { borderColor: colors.border, backgroundColor: colors.card }]}>
              <Bookmark size={28} color={colors.secondaryText} />

              <Text style={[styles.collectionText, { color: colors.secondaryText }]}>
                NOTHING IN COLLECTION YET
              </Text>

              <TouchableOpacity style={[styles.exploreButton, { backgroundColor: colors.surface, borderColor: colors.border }]}>
                <Text style={[styles.exploreText, { color: colors.text }]}>EXPLORE NOW</Text>
              </TouchableOpacity>
            </View>

            {/* Appearance */}
            <Text style={[styles.controlTitle, { color: colors.secondaryText }]}>APPEARANCE</Text>

            <View style={[styles.themeRow, { backgroundColor: colors.card, borderColor: colors.border }]}>
              {([ 
                { id: 'light',  label: 'LIGHT',  Icon: Sun },
                { id: 'dark',   label: 'DARK',   Icon: Moon },
                { id: 'system', label: 'SYSTEM', Icon: Monitor },
              ] as const).map(({ id, label, Icon }) => {
                const isActive = theme === id;
                return (
                  <TouchableOpacity
                    key={id}
                    onPress={() => setTheme(id)}
                    style={[
                      styles.themeOption,
                      isActive && { backgroundColor: colors.text },
                    ]}
                  >
                    <Icon size={15} color={isActive ? colors.background : colors.secondaryText} />
                    <Text style={[styles.themeLabel, { color: isActive ? colors.background : colors.secondaryText }]}>
                      {label}
                    </Text>
                  </TouchableOpacity>
                );
              })}
            </View>

            {/* Control Center */}
            <Text style={[styles.controlTitle, { color: colors.secondaryText }]}>CONTROL CENTER</Text>

            <View style={[styles.controlBox, { borderColor: colors.border, backgroundColor: colors.card }]}>
              <TouchableOpacity style={styles.controlItem}>
                <HelpCircle size={20} color="#3BA6FF" />
                <Text style={[styles.controlText, { color: colors.text }]}>Help Centre</Text>
                <ChevronRight size={18} color={colors.secondaryText} />
              </TouchableOpacity>

              <View style={[styles.controlDivider, { backgroundColor: colors.border }]} />

              <TouchableOpacity style={styles.controlItem}>
                <MessageSquare size={20} color="#34D399" />
                <Text style={[styles.controlText, { color: colors.text }]}>Share feedback</Text>
                <ChevronRight size={18} color={colors.secondaryText} />
              </TouchableOpacity>

              <View style={[styles.controlDivider, { backgroundColor: colors.border }]} />

              <TouchableOpacity style={styles.controlItem}>
                <Shield size={20} color="#F59E0B" />
                <Text style={[styles.controlText, { color: colors.text }]}>Submit a Claim</Text>
                <ChevronRight size={18} color={colors.secondaryText} />
              </TouchableOpacity>
            </View>

            {/* Sign Out */}
            <TouchableOpacity style={styles.signOut} onPress={handleSignOut}>
              <Text style={[styles.signOutText, { color: colors.error }]}>SIGN OUT</Text>
            </TouchableOpacity>

            <View style={{ height: 40 }} />
          </ScrollView>
        </View>
      </BlurView>
    </Modal>
  );
};

const styles = StyleSheet.create({
  blurContainer: {
    flex: 1,
    backgroundColor: "rgba(0,0,0,0.65)",
    padding: 16,
  },

  modalContent: {
    flex: 1,
    borderRadius: 28,
    paddingHorizontal: 22,
    paddingTop: 36,
    paddingBottom: 20,
    overflow: "hidden",
    borderWidth: 1,
  },

  topButtons: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 12,
  },

  iconButton: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: "rgba(255,255,255,0.05)",
    justifyContent: "center",
    alignItems: "center",
  },

  profileSection: {
    alignItems: "center",
    marginVertical: 20,
  },

  avatar: {
    width: 82,
    height: 82,
    borderRadius: 41,
    borderWidth: 1,
    justifyContent: "center",
    alignItems: "center",
    marginBottom: 10,
  },

  avatarText: {
    fontSize: 30,
    fontWeight: "500",
    color: "#fff",
  },

  name: {
    fontSize: 26,
    fontWeight: "500",
    marginTop: 6,
  },

  username: {
    marginTop: 2,
    fontSize: 13,
  },

  member: {
    marginTop: 8,
    fontSize: 10,
    letterSpacing: 1.6,
    fontWeight: "700",
  },

  actionsRow: {
    flexDirection: "row",
    gap: 12,
    marginTop: 20,
  },

  actionButton: {
    flex: 1,
    borderRadius: 26,
    borderWidth: 1,
    paddingVertical: 22,
    alignItems: "center",
    justifyContent: "center",
    gap: 10,
  },

  actionText: {
    fontSize: 11,
    fontWeight: "700",
    letterSpacing: 1,
  },

  iconCircleGreen: {
    width: 34,
    height: 34,
    borderRadius: 10,
    backgroundColor: "rgba(52,211,153,0.15)",
    alignItems: "center",
    justifyContent: "center",
  },

  iconCircleBlue: {
    width: 34,
    height: 34,
    borderRadius: 10,
    backgroundColor: "rgba(59,166,255,0.15)",
    alignItems: "center",
    justifyContent: "center",
  },

  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 30,
    marginBottom: 12,
  },

  sectionTitle: {
    fontSize: 10,
    fontWeight: "700",
    letterSpacing: 1.8,
  },

  discovery: {
    fontSize: 11,
    fontWeight: "600",
  },

  collectionCard: {
    borderWidth: 1,
    borderStyle: "dashed",
    borderRadius: 22,
    paddingVertical: 42,
    paddingHorizontal: 24,
    alignItems: "center",
    marginBottom: 28,
  },

  collectionText: {
    marginTop: 16,
    marginBottom: 18,
    fontSize: 12,
    fontWeight: "600",
    letterSpacing: 1,
  },

  exploreButton: {
    paddingHorizontal: 26,
    paddingVertical: 10,
    borderRadius: 22,
    borderWidth: 1,
  },

  exploreText: {
    fontSize: 11,
    fontWeight: "700",
    letterSpacing: 1,
  },

  controlTitle: {
    fontSize: 10,
    fontWeight: "700",
    letterSpacing: 1.8,
    marginBottom: 12,
  },

  controlBox: {
    borderWidth: 1,
    borderRadius: 22,
    marginBottom: 28,
    overflow: "hidden",
  },

  controlItem: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 18,
    paddingVertical: 18,
    gap: 14,
  },

  controlDivider: {
    height: 1,
    marginLeft: 52,
  },

  controlText: {
    flex: 1,
    fontSize: 14,
  },

  signOut: {
    paddingVertical: 16,
    alignItems: "center",
  },

  signOutText: {
    fontWeight: "700",
    fontSize: 12,
    letterSpacing: 1,
  },

  themeRow: {
    flexDirection: "row",
    borderRadius: 10,
    borderWidth: 1,
    padding: 3,
    gap: 2,
    marginBottom: 28,
  },

  themeOption: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 5,
    paddingVertical: 9,
    borderRadius: 7,
  },

  themeLabel: {
    fontSize: 10,
    fontWeight: "700",
    letterSpacing: 0.8,
  },
});

export default ProfileOverlay;