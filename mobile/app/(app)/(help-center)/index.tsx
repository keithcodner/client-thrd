import React, { useState } from "react";
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
} from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { Ionicons } from "@expo/vector-icons";
import { useTheme } from "@/context/ThemeContext";
import { colours } from "@/constants/colours"; // ✅ import your palette

const HelpCentreScreen = () => {
  const { currentTheme } = useTheme();
  const colors = colours[currentTheme]; // ✅ correct usage with your context

  const [openSections, setOpenSections] = useState<Record<string, boolean>>({
    thrd: true,
    who: true,
    circle: false,
    create: false,
  });

  const toggle = (key: string) => {
    setOpenSections((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  const styles = getStyles(colors);

  return (
    <SafeAreaView style={styles.container} className="b-4"> 
      
      <View style={styles.header} className="pb-4 pt-8">
        <Text style={styles.title}>Help Centre</Text>
        <TouchableOpacity style={styles.closeBtn}>
          <Ionicons name="close" size={16} color={colors.secondaryText} />
        </TouchableOpacity>
      </View>

      <View style={styles.tabs}>
        <Text style={styles.tabInactive}>REFLECTIONS</Text>
        <View style={styles.tabActiveWrapper}>
          <Text style={styles.tabActive}>GUIDANCE</Text>
          <View style={styles.activeUnderline} />
        </View>
      </View>

      <ScrollView showsVerticalScrollIndicator={false}>
        <Text style={styles.sectionTitle}>GETTING STARTED</Text>

        <View style={styles.card}>
          <Accordion
            icon="information-circle-outline"
            title="What is THRD?"
            open={openSections.thrd}
            onPress={() => toggle("thrd")}
            colors={colors}
          >
            THRD is a social coordination platform designed to remove the friction from seeing people you trust.
          </Accordion>

          <Divider colors={colors} />

          <Accordion
            icon="people-outline"
            title="Who is THRD for?"
            open={openSections.who}
            onPress={() => toggle("who")}
            colors={colors}
          >
            THRD supports three profile types: Individual, Runner, and Business.
          </Accordion>
        </View>

        <Text style={styles.sectionTitle}>CIRCLES & PLANNING</Text>

        <View style={styles.card}>
          <Accordion
            icon="chatbubble-outline"
            title="What is a Circle?"
            open={openSections.circle}
            onPress={() => toggle("circle")}
            colors={colors}
          />

          <Divider colors={colors} />

          <Accordion
            icon="add"
            title="How do I create a Circle?"
            open={openSections.create}
            onPress={() => toggle("create")}
            colors={colors}
          />
        </View>
      </ScrollView>
    </SafeAreaView>
  );
};

const Accordion = ({ title, open, onPress, children, icon, colors }: any) => {
  const styles = getStyles(colors);

  return (
    <View>
      <TouchableOpacity style={styles.accordionHeader} onPress={onPress}>
        <View style={styles.accordionLeft}>
          <Ionicons name={icon} size={15} color={colors.secondaryText} />
          <Text style={styles.accordionTitle}>{title}</Text>
        </View>

        <Ionicons
          name={open ? "chevron-up" : "chevron-down"}
          size={16}
          color={colors.secondaryText}
        />
      </TouchableOpacity>

      {open && children && (
        <View style={styles.contentWrapper}>
          <View style={styles.leftBar} />
          <Text style={styles.accordionContent}>{children}</Text>
        </View>
      )}
    </View>
  );
};

const Divider = ({ colors }: any) => (
  <View style={{ height: 1, backgroundColor: colors.border }} />
);

const getStyles = (colors: any) =>
  StyleSheet.create({
    container: {
      flex: 1,
      backgroundColor: colors.background,
      paddingHorizontal: 20,
      paddingTop: 0,
    },

    header: {
      flexDirection: "row",
      justifyContent: "space-between",
      alignItems: "center",
      marginBottom: 20,
    },

    title: {
      fontSize: 26,
      fontWeight: "700",
      color: colors.text,
    },

    closeBtn: {
      width: 34,
      height: 34,
      borderRadius: 17,
      backgroundColor: colors.surface,
      justifyContent: "center",
      alignItems: "center",
    },

    tabs: {
      flexDirection: "row",
      gap: 26,
      marginBottom: 26,
    },

    tabInactive: {
      color: colors.textLight,
      fontSize: 11,
      letterSpacing: 1.2,
    },

    tabActiveWrapper: {
      alignItems: "center",
    },

    tabActive: {
      color: colors.primary,
      fontSize: 11,
      fontWeight: "600",
      letterSpacing: 1.2,
    },

    activeUnderline: {
      marginTop: 6,
      height: 2,
      width: "100%",
      backgroundColor: colors.primary,
    },

    sectionTitle: {
      fontSize: 11,
      letterSpacing: 1.4,
      marginBottom: 12,
      marginTop: 6,
      color: colors.textLight,
    },

    card: {
      borderWidth: 1,
      borderColor: colors.border,
      borderRadius: 26,
      paddingVertical: 10,
      paddingHorizontal: 16,
      marginBottom: 26,
      backgroundColor: colors.card,
    },

    accordionHeader: {
      flexDirection: "row",
      justifyContent: "space-between",
      alignItems: "center",
      paddingVertical: 14,
    },

    accordionLeft: {
      flexDirection: "row",
      alignItems: "center",
      gap: 10,
    },

    accordionTitle: {
      fontSize: 14,
      fontWeight: "600",
      color: colors.text,
    },

    contentWrapper: {
      flexDirection: "row",
      marginTop: 4,
      marginBottom: 14,
      paddingRight: 10,
    },

    leftBar: {
      width: 2,
      backgroundColor: colors.primary,
      marginRight: 12,
      borderRadius: 2,
    },

    accordionContent: {
      flex: 1,
      fontSize: 13,
      lineHeight: 20,
      color: colors.secondaryText,
    },
  });

export default HelpCentreScreen;