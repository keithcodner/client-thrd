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
} from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { useSession } from "@/context/AuthContext";

interface ProfileOverlayProps {
  visible: boolean;
  onClose: () => void;
}

export const ProfileOverlay = ({ visible, onClose }: ProfileOverlayProps) => {
  const colors = useThemeColours();
  const { user, signOut } = useSession();

  const userName = user?.name || "James";
  const userHandle = user?.email?.split("@")[0] || "jbond";

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

  return (
    <Modal transparent animationType="fade" visible={visible}>
      <BlurView intensity={100} tint="dark" style={styles.blurContainer}>
        <View style={styles.modalContent}>
          {/* Top Buttons */}
          <View style={styles.topButtons}>
            <TouchableOpacity style={styles.iconButton}>
              <Edit2 size={18} color="#fff" />
            </TouchableOpacity>

            <TouchableOpacity style={styles.iconButton} onPress={onClose}>
              <X size={20} color="#fff" />
            </TouchableOpacity>
          </View>

          <ScrollView showsVerticalScrollIndicator={false} bounces={false}>
            {/* Profile Header */}
            <View style={styles.profileSection}>
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{initials}</Text>
              </View>

              <Text style={styles.name}>{userName}</Text>
              <Text style={styles.username}>@{userHandle}</Text>

              <Text style={styles.member}>MEMBER</Text>
            </View>

            {/* Action Buttons */}
            <View style={styles.actionsRow}>
              <TouchableOpacity style={styles.actionButton}>
                <View style={styles.iconCircleGreen}>
                  <ListTodo size={18} color="#34D399" />
                </View>
                <Text style={styles.actionText}>MY PLANNER</Text>
              </TouchableOpacity>

              <TouchableOpacity style={styles.actionButton}>
                <View style={styles.iconCircleBlue}>
                  <Clock size={18} color="#3BA6FF" />
                </View>
                <Text style={styles.actionText}>SUPPORT</Text>
              </TouchableOpacity>
            </View>

            {/* Saved Collection */}
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionTitle}>SAVED COLLECTION</Text>
              <Text style={styles.discovery}>DISCOVERY ›</Text>
            </View>

            <View style={styles.collectionCard}>
              <Bookmark size={28} color="#aaa" />

              <Text style={styles.collectionText}>
                NOTHING IN COLLECTION YET
              </Text>

              <TouchableOpacity style={styles.exploreButton}>
                <Text style={styles.exploreText}>EXPLORE NOW</Text>
              </TouchableOpacity>
            </View>

            {/* Control Center */}
            <Text style={styles.controlTitle}>CONTROL CENTER</Text>

            <View style={styles.controlBox}>
              <TouchableOpacity style={styles.controlItem}>
                <HelpCircle size={20} color="#3BA6FF" />
                <Text style={styles.controlText}>Help Centre</Text>
                <ChevronRight size={18} color="#888" />
              </TouchableOpacity>

              <View style={styles.controlDivider} />

              <TouchableOpacity style={styles.controlItem}>
                <MessageSquare size={20} color="#34D399" />
                <Text style={styles.controlText}>Share feedback</Text>
                <ChevronRight size={18} color="#888" />
              </TouchableOpacity>

              <View style={styles.controlDivider} />

              <TouchableOpacity style={styles.controlItem}>
                <Shield size={20} color="#F59E0B" />
                <Text style={styles.controlText}>Submit a Claim</Text>
                <ChevronRight size={18} color="#888" />
              </TouchableOpacity>
            </View>

            {/* Sign Out */}
            <TouchableOpacity style={styles.signOut} onPress={handleSignOut}>
              <Text style={styles.signOutText}>SIGN OUT</Text>
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

    backgroundColor: "rgba(18,18,18,0.92)",
    borderWidth: 1,
    borderColor: "rgba(255,255,255,0.06)",
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
    borderColor: "rgba(255,255,255,0.12)",
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
    color: "#fff",
  },

  username: {
    marginTop: 2,
    fontSize: 13,
    color: "#aaa",
  },

  member: {
    marginTop: 8,
    fontSize: 10,
    letterSpacing: 1.6,
    fontWeight: "700",
    color: "#777",
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
    borderColor: "rgba(255,255,255,0.08)",
    paddingVertical: 22,
    alignItems: "center",
    justifyContent: "center",
    gap: 10,
    backgroundColor: "rgba(255,255,255,0.02)",
  },

  actionText: {
    fontSize: 11,
    fontWeight: "700",
    letterSpacing: 1,
    color: "#aaa",
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
    color: "#777",
  },

  discovery: {
    fontSize: 11,
    fontWeight: "600",
    color: "#A3E635",
  },

  collectionCard: {
    borderWidth: 1,
    borderStyle: "dashed",
    borderColor: "rgba(255,255,255,0.12)",
    borderRadius: 22,
    paddingVertical: 42,
    paddingHorizontal: 24,
    alignItems: "center",
    marginBottom: 28,
    backgroundColor: "rgba(255,255,255,0.02)",
  },

  collectionText: {
    marginTop: 16,
    marginBottom: 18,
    fontSize: 12,
    fontWeight: "600",
    letterSpacing: 1,
    color: "#aaa",
  },

  exploreButton: {
    paddingHorizontal: 26,
    paddingVertical: 10,
    borderRadius: 22,
    backgroundColor: "#0E0E0E",
    borderWidth: 1,
    borderColor: "rgba(255,255,255,0.06)",
  },

  exploreText: {
    fontSize: 11,
    fontWeight: "700",
    letterSpacing: 1,
    color: "#fff",
  },

  controlTitle: {
    fontSize: 10,
    fontWeight: "700",
    letterSpacing: 1.8,
    color: "#777",
    marginBottom: 12,
  },

  controlBox: {
    borderWidth: 1,
    borderColor: "rgba(255,255,255,0.06)",
    borderRadius: 22,
    marginBottom: 28,
    overflow: "hidden",
    backgroundColor: "rgba(255,255,255,0.02)",
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
    backgroundColor: "rgba(255,255,255,0.06)",
    marginLeft: 52,
  },

  controlText: {
    flex: 1,
    fontSize: 14,
    color: "#fff",
  },

  signOut: {
    paddingVertical: 16,
    alignItems: "center",
  },

  signOutText: {
    fontWeight: "700",
    fontSize: 12,
    letterSpacing: 1,
    color: "#ef4444",
  },
});

export default ProfileOverlay;