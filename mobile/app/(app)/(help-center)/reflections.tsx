import React from "react";
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
import { colours } from "@/constants/colours";
import { router } from "expo-router";

const MindSpaceScreen = () => {
    const { currentTheme } = useTheme();
    const colors = colours[currentTheme];
    const styles = getStyles(colors);

    const handleClose = () => {
        router.push(`/(app)/(tabs)/(home)`); 
    };

    const handleGuidanceOnpress = () => {
        router.push(`/(app)/(help-center)`); 
    };

    return (
        <SafeAreaView style={styles.container} className="b-4">
        
        {/* HEADER */}
        <View style={styles.header} className="pb-4 pt-8">
            <Text style={styles.title}>Mind Space</Text>
            <TouchableOpacity style={styles.closeBtn} onPress={handleClose}>
            <Ionicons name="close" size={16} color={colors.secondaryText} />
            </TouchableOpacity>
        </View>

        {/* TABS */}
        <View style={styles.tabs}>
            <View style={styles.tabActiveWrapper}>
                <Text style={styles.tabActive}>REFLECTIONS</Text>
                <View style={styles.activeUnderline} />
            </View>

            <TouchableOpacity onPress={handleGuidanceOnpress}>
                <Text style={styles.tabInactive}>GUIDANCE</Text>
            </TouchableOpacity>
        </View>

        <ScrollView showsVerticalScrollIndicator={false}>

            {/* HERO QUOTE */}
            <View style={styles.heroCard}>
            <Ionicons name="heart-outline" size={25} color={colors.accent} />
            <Text style={styles.quote}>
                "THRD works around you. You're always in control of your social energy."
            </Text>
            </View>

            {/* ARTICLE CARD 1 */}
            <View style={styles.articleCard}>
            <View style={styles.tagRow}>
                <Text style={styles.tag}>SOCIAL ANXIETY</Text>
                <Text style={styles.readTime}>3 MIN READ</Text>
            </View>

            <Text style={styles.articleTitle}>
                When You Want Connection, But Not Pressure
            </Text>

            <Text style={styles.articleSubtitle}>
                Navigating the gap between loneliness and social exhaustion.
            </Text>

            <Text style={styles.articleBody}>
                It’s common to feel a pull toward people while simultaneously fearing
                the energy it might take to show up. You aren't "broken" for wanting
                to be included but dreading the actual event. Real connection doesn't
                always have to be high-stakes. Sometimes, just knowing you're welcome
                is enough.
            </Text>
            </View>

            {/* ARTICLE CARD 2 */}
            <View style={styles.articleCard}>
            <View style={styles.tagRow}>
                <Text style={styles.tag}>FINDING TIME</Text>
                <Text style={styles.readTime}>4 MIN READ</Text>
            </View>

            <Text style={styles.articleTitle}>
                Making Time Without Burning Out
            </Text>

            <Text style={styles.articleSubtitle}>
                Balancing connection with your personal energy.
            </Text>

            <Text style={styles.articleBody}>
                Social energy is finite. The goal isn’t to say yes to everything —
                it’s to say yes to the right things. Protect your time, communicate
                your limits, and remember that meaningful connection doesn’t require
                constant availability.
            </Text>
            </View>

        </ScrollView>
        </SafeAreaView>
    );
};

const getStyles = (colors: any) =>
  StyleSheet.create({
    container: {
      flex: 1,
      backgroundColor: colors.stone950,
      paddingHorizontal: 20,
    },

    header: {
      flexDirection: "row",
      justifyContent: "space-between",
      alignItems: "center",
      marginBottom: 20,
      //marginTop: 10,
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
      marginBottom: 24,
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

    /* HERO */
    heroCard: {
      borderWidth: 1,
      borderColor: colors.border,
      borderRadius: 28,
      padding: 40,
      marginBottom: 26,
      alignItems: "center",
      gap: 12,
      backgroundColor: colors.surface,
    },

    quote: {
      textAlign: "center",
      fontSize: 16,
      lineHeight: 22,
      color: colors.stone50,
      fontStyle: "italic",
    },

    /* ARTICLE CARD */
    articleCard: {
      borderWidth: 2,
      borderColor: colors.border,
      borderRadius: 28,
      padding: 20,
      marginBottom: 26,
      backgroundColor: colors.stone950,
    },

    tagRow: {
      flexDirection: "row",
      justifyContent: "space-between",
      marginBottom: 10,
      alignItems: "center",
    },

    tag: {
      fontSize: 10,
      fontWeight: "700",
      letterSpacing: 1,
      color: colors.primary,
      backgroundColor: colors.primary + "20",
      paddingHorizontal: 10,
      paddingVertical: 4,
      borderRadius: 10,
    },

    readTime: {
      fontSize: 10,
      color: colors.textLight,
      letterSpacing: 1,
    },

    articleTitle: {
      fontSize: 18,
      fontWeight: "700",
      color: colors.text,
      marginBottom: 6,
    },

    articleSubtitle: {
      fontSize: 13,
      fontStyle: "italic",
      color: colors.textLight,
      marginBottom: 12,
    },

    articleBody: {
      fontSize: 14,
      lineHeight: 22,
      color: colors.secondaryText,
    },
  });

export default MindSpaceScreen;
