import React, { useState } from "react";
import {
  View,
  Text,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
} from "react-native";
import { useRouter } from 'expo-router';
import { Ionicons } from "@expo/vector-icons";
import { useTheme } from "@/context/ThemeContext";

const HelpCentreScreen = () => {
  const { currentTheme } = useTheme();
  const isDark = currentTheme === "dark";

  // State to track which sections are open
  const [openSections, setOpenSections] = useState<Record<string, boolean>>({
    thrd: true,
    who: true,
    circle: false,
    create: false,
  });

  // Function to toggle section open state
  const toggle = (key: string) => {
    // Toggle the open state of the specified section
    setOpenSections((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  // Generate styles based on theme
  const styles = getStyles(isDark);

  return (
    <View style={styles.container}>
      {/* Header */}
      <View style={styles.header}>
        <Text style={styles.title}>Help Centre</Text>
        <TouchableOpacity>
          <Ionicons name="close" size={24} color={styles.icon.color} />
        </TouchableOpacity>
      </View>

      {/* Tabs */}
      <View style={styles.tabs}>
        <Text style={styles.tabInactive}>REFLECTIONS</Text>
        <Text style={styles.tabActive}>GUIDANCE</Text>
      </View>

      <ScrollView contentContainerStyle={{ paddingBottom: 40 }}>
        {/* Getting Started */}
        <Text style={styles.sectionTitle}>GETTING STARTED</Text>

        <View style={styles.card}>
          {/* What is THRD */}
          <Accordion
            title="What is THRD?"
            open={openSections.thrd}
            onPress={() => toggle("thrd")}
            styles={styles}
          >
            THRD is a social coordination platform designed to remove the friction from seeing people you trust. We replace chaotic group chats with organized 'Circles' that share a common sense of availability.
          </Accordion>

          <Divider styles={styles} />

          {/* Who is THRD for */}
          <Accordion
            title="Who is THRD for?"
            open={openSections.who}
            onPress={() => toggle("who")}
            styles={styles}
          >
            THRD supports three profile types: Individual (personal coordination), Runner (community hosts & event organizers), and Business (physical venues like cafes or studios). You can switch your account type anytime in your Profile settings.
          </Accordion>
        </View>

        {/* Circles */}
        <Text style={styles.sectionTitle}>CIRCLES & PLANNING</Text>
        <View style={styles.card}>
          <Accordion
            title="What is a Circle?"
            open={openSections.circle}
            onPress={() => toggle("circle")}
            styles={styles}
          />

          <Divider styles={styles} />

          <Accordion
            title="How do I create a Circle?"
            open={openSections.create}
            onPress={() => toggle("create")}
            styles={styles}
          />
        </View>
      </ScrollView>
    </View>
  );
};

const Accordion = ({ title, open, onPress, children, styles }: any) => {
  return (
    <View>
      <TouchableOpacity style={styles.accordionHeader} onPress={onPress}>
        <View style={styles.accordionTitleRow}>
          <Ionicons name="information-circle-outline" size={18} color={styles.icon.color} />
          <Text style={styles.accordionTitle}>{title}</Text>
        </View>
        <Ionicons
          name={open ? "chevron-up" : "chevron-down"}
          size={18}
          color={styles.icon.color}
        />
      </TouchableOpacity>

      {open && children && (
        <Text style={styles.accordionContent}>{children}</Text>
      )}
    </View>
  );
};

const Divider = ({ styles }: any) => (
  <View style={styles.divider} />
);

const getStyles = (dark: boolean) =>
  StyleSheet.create({
    container: {
      flex: 1,
      backgroundColor: dark ? "#0B0F0E" : "#FFFFFF",
      paddingHorizontal: 16,
      paddingTop: 50,
    },
    header: {
      flexDirection: "row",
      justifyContent: "space-between",
      alignItems: "center",
      marginBottom: 16,
    },
    title: {
      fontSize: 22,
      fontWeight: "600",
      color: dark ? "#E5E7EB" : "#111827",
    },
    icon: {
      color: dark ? "#9CA3AF" : "#6B7280",
    },
    tabs: {
      flexDirection: "row",
      gap: 20,
      marginBottom: 20,
    },
    tabActive: {
      color: "#A3E635",
      fontWeight: "600",
      borderBottomWidth: 2,
      borderBottomColor: "#A3E635",
      paddingBottom: 6,
    },
    tabInactive: {
      color: dark ? "#6B7280" : "#9CA3AF",
    },
    sectionTitle: {
      fontSize: 12,
      letterSpacing: 1,
      marginBottom: 10,
      marginTop: 10,
      color: dark ? "#6B7280" : "#9CA3AF",
    },
    card: {
      borderWidth: 1,
      borderColor: dark ? "#1F2937" : "#E5E7EB",
      borderRadius: 20,
      padding: 16,
      marginBottom: 20,
      backgroundColor: dark ? "#0F172A" : "#FFFFFF",
    },
    accordionHeader: {
      flexDirection: "row",
      justifyContent: "space-between",
      alignItems: "center",
      paddingVertical: 8,
    },
    accordionTitleRow: {
      flexDirection: "row",
      alignItems: "center",
      gap: 8,
    },
    accordionTitle: {
      fontSize: 14,
      fontWeight: "600",
      color: dark ? "#E5E7EB" : "#111827",
    },
    accordionContent: {
      marginTop: 10,
      fontSize: 13,
      lineHeight: 18,
      color: dark ? "#9CA3AF" : "#4B5563",
    },
    divider: {
      height: 1,
      backgroundColor: dark ? "#1F2937" : "#E5E7EB",
      marginVertical: 12,
    },
  });

export default HelpCentreScreen;
