import React from "react";
import { View, Image, Pressable, Text, StyleSheet } from "react-native";
import { ArrowRight } from "lucide-react-native";

interface DiscoverCardProps {
  colors: any;
  trendingSpace?: any;
  onPress?: () => void;
}

export const DiscoverCard = ({
  colors,
  trendingSpace,
  onPress = () => {},
}: DiscoverCardProps) => {
  return (
    <Pressable style={styles.discoverCard} onPress={onPress}>
      <Image
        source={require("@/assets/main-image-home.png")}
        style={styles.discoverImage}
      />

      <View
        style={[
          styles.discoverOverlay,
          {
            backgroundColor: `rgba(0, 0, 0, ${
              colors.background === "#FFFFFF" ? 0.3 : 0.45
            })`,
          },
        ]}
      />

      <View style={styles.discoverContent}>
        <Text style={[styles.discoverLabel, { color: colors.secondaryText }]}>
          FEATURED SPOT
        </Text>
        <Text style={[styles.discoverTitle, { color: colors.text }]}>
          Discover what's on this week.
        </Text>
        <Text style={[styles.discoverSubtitle, { color: colors.text }]}>
          {trendingSpace
            ? `Check out ${trendingSpace.name}...`
            : "See local spaces"}
        </Text>
      </View>

      <Pressable
        style={[
          styles.discoverArrowButton,
          {
            backgroundColor:
              colors.background === "#FFFFFF"
                ? "rgba(255, 255, 255, 0.5)"
                : "rgba(0, 0, 0, 0.5)",
          },
        ]}
      >
        <ArrowRight size={20} color={colors.text} />
      </Pressable>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  discoverCard: {
    height: 220,
    borderRadius: 24,
    marginHorizontal: 16,
    marginTop: 8,
    marginBottom: 24,
    overflow: "hidden",
    backgroundColor: "#1a1a1a",
  },

  discoverImage: {
    position: "absolute",
    width: "100%",
    height: "100%",
  },

  discoverOverlay: {
    ...StyleSheet.absoluteFillObject,
  },

  discoverContent: {
    flex: 1,
    justifyContent: "flex-end",
    padding: 20,
  },

  discoverLabel: {
    fontSize: 10,
    fontWeight: "700",
    letterSpacing: 1.2,
    marginBottom: 8,
  },

  discoverTitle: {
    fontSize: 24,
    fontWeight: "600",
    marginBottom: 8,
    lineHeight: 30,
  },

  discoverSubtitle: {
    fontSize: 13,
  },

  discoverArrowButton: {
    position: "absolute",
    bottom: 16,
    right: 16,
    width: 44,
    height: 44,
    borderRadius: 22,
    justifyContent: "center",
    alignItems: "center",
  },
});
