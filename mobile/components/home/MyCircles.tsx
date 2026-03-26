import React from "react";
import {
  View,
  Text,
  ScrollView,
  Pressable,
  Image,
  StyleSheet,
} from "react-native";
import { HeartHandshake, MessageCircle } from "lucide-react-native";

interface MyCirclesProps {
  colors: any;
  groups: any[];
  onNavigate?: (screen: string) => void;
  onSelectGroup?: (id: string) => void;
}

export const MyCircles = ({
  colors,
  groups,
  onNavigate = () => {},
  onSelectGroup = () => {},
}: MyCirclesProps) => {
  // Display all groups passed (up to 10), maintaining the order
  const displayGroups = groups.slice(0, 10);

  return (
    <View style={styles.section}>
      <View style={styles.sectionHeader}>
        <Text style={[styles.sectionTitle, { color: colors.textLight }]}>
          MY CIRCLES
        </Text>
        <Pressable onPress={() => onNavigate("explore")}>
          <Text style={[styles.seeAllLink, { color: colors.primary }]}>
            See All
          </Text>
        </Pressable>
      </View>

      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        style={styles.circlesScroll}
      >
        {displayGroups.length > 0 ? (
          displayGroups.map((group, idx) => (
            <Pressable
              key={group.id}
              style={styles.groupItem}
              onPress={() => onSelectGroup(group.id)}
              className="mt-2"
            >
              <View
                style={[
                  styles.groupCircle,
                  {
                    backgroundColor: colors.surface,
                    borderColor: colors.border,
                  },
                ]}
              >
                {group.customization?.headerBanner ? (
                  <Image
                    source={{ uri: group.customization.headerBanner }}
                    style={styles.groupCircleImage}
                  />
                ) : idx === 0 ? (
                  <HeartHandshake size={28} color={colors.primary} />
                ) : (
                  <MessageCircle size={28} color={colors.primary} />
                )}
                {idx === 0 && (
                  <View
                    style={[
                      styles.circleBadge,
                      { backgroundColor: colors.primary },
                    ]}
                  >
                    <Text
                      style={[
                        styles.circleBadgeText,
                        { color: colors.background },
                      ]}
                    >
                      ★
                    </Text>
                  </View>
                )}
              </View>
              <Text
                numberOfLines={1}
                style={[styles.groupName, { color: colors.secondaryText }]}
              >
                {group.name}
              </Text>
            </Pressable>
          ))
        ) : (
          <Text style={[styles.emptyText, { color: colors.secondaryText }]}>
            No circles yet.
          </Text>
        )}
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  section: {
    marginBottom: 28,
  },

  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 20,
    marginBottom: 12,
  },

  sectionTitle: {
    fontSize: 11,
    fontWeight: "800",
    letterSpacing: 1.2,
  },

  seeAllLink: {
    fontSize: 12,
    fontWeight: "600",
  },

  circlesScroll: {
    paddingLeft: 8,
    paddingRight: 12,
  },

  groupItem: {
    alignItems: "center",
    marginHorizontal: 12,
  },

  groupCircle: {
    width: 72,
    height: 72,
    borderRadius: 36,
    justifyContent: "center",
    alignItems: "center",
    borderWidth: 2,
    marginBottom: 8,
  },

  groupCircleImage: {
    width: "100%",
    height: "100%",
    borderRadius: 36,
  },

  circleBadge: {
    position: "absolute",
    top: -4,
    right: -4,
    width: 24,
    height: 24,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
  },

  circleBadgeText: {
    fontSize: 14,
    fontWeight: "700",
  },

  groupName: {
    marginTop: 0,
    fontSize: 11,
    maxWidth: 72,
    textAlign: "center",
    fontWeight: "500",
  },

  emptyText: {
    fontSize: 13,
    marginLeft: 20,
  },
});
