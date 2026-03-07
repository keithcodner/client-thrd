import React from "react";
import { View, Text, Pressable, StyleSheet } from "react-native";
import { HeartHandshake, ArrowRight, Plus } from "lucide-react-native";

interface MindSpaceCardProps {
  colors: any;
  onPress?: () => void;
}

export const MindSpaceCard = ({
  colors,
  onPress = () => {},
}: MindSpaceCardProps) => {
  return (
    <Pressable
      style={[styles.mindSpaceCard, { backgroundColor: colors.card }]}
      onPress={onPress}
    >
      <View style={[styles.mindSpaceIcon, { backgroundColor: colors.surface }]}>
        <HeartHandshake size={24} color={colors.primary} />
      </View>

      <View style={styles.mindSpaceContent}>
        <Text style={[styles.mindSpaceTitle, { color: colors.text }]}>
          Mind Space
        </Text>
        <Text style={[styles.mindSpaceSubtitle, { color: colors.secondaryText }]}>
          Quiet support for how time actually feels
        </Text>
      </View>

      <ArrowRight size={20} color={colors.secondaryText} />

      <Pressable style={[styles.mindSpacePlusButton, { backgroundColor: colors.surface }]}>
        <Plus size={20} color={colors.text} />
      </Pressable>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  mindSpaceCard: {
    flexDirection: "row",
    alignItems: "center",
    marginHorizontal: 16,
    marginTop: 12,
    padding: 16,
    borderRadius: 16,
    gap: 12,
  },

  mindSpaceIcon: {
    width: 48,
    height: 48,
    borderRadius: 24,
    justifyContent: "center",
    alignItems: "center",
  },

  mindSpaceContent: {
    flex: 1,
  },

  mindSpaceTitle: {
    fontSize: 16,
    fontWeight: "600",
    marginBottom: 2,
  },

  mindSpaceSubtitle: {
    fontSize: 12,
    lineHeight: 16,
  },

  mindSpacePlusButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    justifyContent: "center",
    alignItems: "center",
    marginLeft: 8,
  },
});
