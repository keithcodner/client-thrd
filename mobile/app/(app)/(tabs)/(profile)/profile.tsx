import React from "react";
import {
  Modal,
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
} from "react-native";
import { BlurView } from "expo-blur";
import { Edit2, X, ListTodo, Clock, Bookmark, HelpCircle, MessageSquare, Shield, ChevronRight } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { useSession } from "@/context/AuthContext";

interface ProfileOverlayProps {
  visible: boolean;
  onClose: () => void;
}

export const ProfileOverlay = ({
  visible,
  onClose,
}: ProfileOverlayProps) => {
  const colors = useThemeColours();
  const { user } = useSession();

  const userName = user?.name || "James";
  const userHandle = user?.email?.split("@")[0] || "jbond";
  const initials = userName
    .split(" ")
    .map((n) => n[0])
    .join("")
    .toUpperCase();

  const isDark = colors.background === "#15110fff";

  return (
    <Modal transparent animationType="slide" visible={visible}>
      <BlurView intensity={90} tint={isDark ? "dark" : "light"} style={styles.blurContainer}>
        <View style={styles.card}>
          {/* Top Buttons */}
          <View style={styles.topButtons}>
            <TouchableOpacity style={[styles.iconButton, { backgroundColor: colors.card }]}>
              <Edit2 size={18} color={colors.text} />
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.iconButton, { backgroundColor: colors.card }]}
              onPress={onClose}
            >
              <X size={20} color={colors.text} />
            </TouchableOpacity>
          </View>

          <ScrollView showsVerticalScrollIndicator={false} bounces={false} style={{ backgroundColor: colors.background, borderTopLeftRadius: 30, borderTopRightRadius: 30 }}>
            {/* Profile Header */}
            <View style={styles.profileSection}>
              <View style={[styles.avatar, { borderColor: colors.border }]}>
                <Text style={[styles.avatarText, { color: colors.text }]}>
                  {initials}
                </Text>
              </View>

              <Text style={[styles.name, { color: colors.text }]}>{userName}</Text>
              <Text style={[styles.username, { color: colors.secondaryText }]}>
                @{userHandle}
              </Text>

              <Text style={[styles.member, { color: colors.secondaryText }]}>
                MEMBER
              </Text>

              <TouchableOpacity
                style={[
                  styles.exploreButton,
                  { backgroundColor: colors.card, borderColor: colors.border },
                ]}
              >
                <Text style={[styles.exploreText, { color: colors.primary }]}>
                  EXPLORE NOW
                </Text>
              </TouchableOpacity>
            </View>

            {/* Action Buttons */}
            <View style={styles.actionsRow}>
              <TouchableOpacity
                style={[
                  styles.actionButton,
                  { borderColor: colors.border, backgroundColor: colors.card },
                ]}
              >
                <ListTodo size={20} color={colors.primary} />
                <Text style={[styles.actionText, { color: colors.secondaryText }]}>
                  MY PLANNER
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.actionButton,
                  { borderColor: colors.border, backgroundColor: colors.card },
                ]}
              >
                <Clock size={20} color={colors.info} />
                <Text style={[styles.actionText, { color: colors.secondaryText }]}>
                  SUPPORT
                </Text>
              </TouchableOpacity>
            </View>

            {/* Saved Collection */}
            <View style={styles.sectionHeader}>
              <Text style={[styles.sectionTitle, { color: colors.secondaryText }]}>
                SAVED COLLECTION
              </Text>
              <Text style={[styles.discovery, { color: colors.primary }]}>
                DISCOVERY ›
              </Text>
            </View>

            <View
              style={[
                styles.collectionCard,
                {
                  borderColor: colors.border,
                  backgroundColor: colors.card,
                },
              ]}
            >
              <Bookmark size={30} color={colors.secondaryText} />
              <Text style={[styles.collectionText, { color: colors.secondaryText }]}>
                NOTHING IN COLLECTION YET
              </Text>

              <TouchableOpacity
                style={[
                  styles.exploreButton,
                  { backgroundColor: colors.background, borderColor: colors.border },
                ]}
              >
                <Text style={[styles.exploreText, { color: colors.primary }]}>
                  EXPLORE NOW
                </Text>
              </TouchableOpacity>
            </View>

            {/* Control Center */}
            <Text
              style={[
                styles.controlTitle,
                { color: colors.secondaryText },
              ]}
            >
              CONTROL CENTER
            </Text>

            <View
              style={[
                styles.controlBox,
                { borderColor: colors.border, backgroundColor: colors.card },
              ]}
            >
              <TouchableOpacity style={styles.controlItem}>
                <HelpCircle size={20} color={colors.info} />
                <Text style={[styles.controlText, { color: colors.text }]}>
                  Help Centre
                </Text>
                <ChevronRight size={18} color={colors.secondaryText} />
              </TouchableOpacity>

              <View style={[styles.controlDivider, { borderColor: colors.border }]} />

              <TouchableOpacity style={styles.controlItem}>
                <MessageSquare size={20} color={colors.primary} />
                <Text style={[styles.controlText, { color: colors.text }]}>
                  Share feedback
                </Text>
                <ChevronRight size={18} color={colors.secondaryText} />
              </TouchableOpacity>

              <View style={[styles.controlDivider, { borderColor: colors.border }]} />

              <TouchableOpacity style={styles.controlItem}>
                <Shield size={20} color={colors.warning} />
                <Text style={[styles.controlText, { color: colors.text }]}>
                  Submit a Claim
                </Text>
                <ChevronRight size={18} color={colors.secondaryText} />
              </TouchableOpacity>
            </View>

            <TouchableOpacity style={styles.signOut}>
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
    justifyContent: "flex-end",
  },

  card: {
    width: "100%",
    maxHeight: "85%",
    backgroundColor: "transparent",
    borderTopLeftRadius: 30,
    borderTopRightRadius: 30,
    paddingHorizontal: 20,
    paddingTop: 16,
    paddingBottom: 12,
  },

  topButtons: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 20,
  },

  iconButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: "center",
    alignItems: "center",
  },

  profileSection: {
    alignItems: "center",
    marginVertical: 20,
  },

  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    borderWidth: 2,
    justifyContent: "center",
    alignItems: "center",
  },

  avatarText: {
    fontSize: 28,
    fontWeight: "600",
  },

  name: {
    fontSize: 22,
    fontWeight: "600",
    marginTop: 16,
  },

  username: {
    marginTop: 4,
    fontSize: 14,
  },

  member: {
    marginTop: 8,
    fontSize: 11,
    letterSpacing: 1,
    fontWeight: "600",
  },

  exploreButton: {
    marginTop: 16,
    paddingHorizontal: 24,
    paddingVertical: 10,
    borderRadius: 20,
    borderWidth: 1,
  },

  exploreText: {
    fontSize: 12,
    fontWeight: "700",
    letterSpacing: 0.8,
  },

  actionsRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    gap: 12,
    marginTop: 28,
  },

  actionButton: {
    flex: 1,
    borderRadius: 20,
    borderWidth: 1,
    padding: 20,
    alignItems: "center",
    gap: 8,
  },

  actionText: {
    fontSize: 11,
    fontWeight: "700",
    letterSpacing: 0.8,
  },

  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 32,
    marginBottom: 12,
    paddingHorizontal: 4,
  },

  sectionTitle: {
    fontSize: 11,
    fontWeight: "800",
    letterSpacing: 1.2,
  },

  discovery: {
    fontSize: 12,
    fontWeight: "600",
  },

  collectionCard: {
    borderWidth: 1,
    borderRadius: 20,
    paddingVertical: 40,
    paddingHorizontal: 24,
    alignItems: "center",
    marginBottom: 28,
  },

  collectionText: {
    marginTop: 16,
    marginBottom: 20,
    fontSize: 12,
    fontWeight: "600",
    letterSpacing: 0.8,
  },

  controlTitle: {
    fontSize: 11,
    fontWeight: "800",
    letterSpacing: 1.2,
    marginBottom: 12,
    paddingHorizontal: 4,
  },

  controlBox: {
    borderWidth: 1,
    borderRadius: 20,
    marginBottom: 28,
    overflow: "hidden",
  },

  controlItem: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 18,
    paddingVertical: 18,
    gap: 12,
  },

  controlDivider: {
    height: 1,
    marginHorizontal: 18,
  },

  controlText: {
    flex: 1,
    fontSize: 14,
    fontWeight: "500",
  },

  signOut: {
    paddingVertical: 16,
    alignItems: "center",
    marginBottom: 12,
  },

  signOutText: {
    fontWeight: "700",
    fontSize: 12,
    letterSpacing: 0.8,
  },
});

export default ProfileOverlay;