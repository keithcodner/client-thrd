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
import { router } from "expo-router";

interface HelpCentreSectionProps {
  headTitle: string;
  styles: ReturnType<typeof getStyles>;
  colors: any;
  openSections: Record<string, boolean>;
  toggle: (key: string) => void;
  toggleKey1: string;
  toggleKey2: string;
  icon1: string;
  icon2: string;
  title1: string;
  title2: string;
  description1: string;
  description2: string;
}

const HelpCentreSection = ({
  headTitle,
  styles,
  colors,
  openSections,
  toggle,
  toggleKey1,
  toggleKey2,
  icon1,
  icon2,
  title1,
  title2,
  description1,
  description2
}: HelpCentreSectionProps) => {


  return (
    <>
        <Text style={styles.sectionTitle}>{headTitle}</Text>

        <View style={styles.card}>
          <Accordion
            icon={icon1}
            title={title1}
            open={openSections[toggleKey1]}
            onPress={() => toggle(toggleKey1)}
            colors={colors}
          >
            {description1}
          </Accordion>

          <Divider colors={colors} />

          <Accordion
            icon={icon2}
            title={title2}
            open={openSections[toggleKey2]}
            onPress={() => toggle(toggleKey2)}
            colors={colors}
          >
            {description2}
          </Accordion>
        </View>
    </>

  );

}

const HelpCentreScreen = () => {
  const { currentTheme } = useTheme();
  const colors = colours[currentTheme]; // ✅ correct usage with your context

  const [openSections, setOpenSections] = useState<Record<string, boolean>>({
    thrd: false,
    who: false,
    circle: false,
    create: false,

    how_does_thrd: false,
    what_does_connecting: false,
    finding_new_spaces: false,
    what_is_mind: false,
    who_can_see: false,
    is_my_data: false,
  });

  const handleClose = () => {
    router.push(`/(app)/(tabs)/(home)`); 
  };

  const handleReflectionsOnpress = () => {
    router.push(`/(app)/(help-center)/reflections`); 
  };

  const toggle = (key: string) => {
    setOpenSections((prev) => ({ ...prev, [key]: !prev[key] }));
  };

  const styles = getStyles(colors);

  return (
    <SafeAreaView style={styles.container} className="b-4"> 
      
      <View style={styles.header} className="pb-4 pt-8">
        <Text style={styles.title}>Help Centre</Text>
        <TouchableOpacity style={styles.closeBtn} onPress={handleClose}>
          <Ionicons name="close" size={16} color={colors.secondaryText} />
        </TouchableOpacity>
      </View>

      <View style={styles.tabs}>
        <TouchableOpacity onPress={handleReflectionsOnpress}>
          <Text style={styles.tabInactive}>REFLECTIONS</Text>
        </TouchableOpacity>
        <View style={styles.tabActiveWrapper}>
          <Text style={styles.tabActive}>GUIDANCE</Text>
          <View style={styles.activeUnderline} />
        </View>
      </View>

      <ScrollView showsVerticalScrollIndicator={false}>

        <HelpCentreSection 
          headTitle="GETTING STARTED"
          styles={styles}
          colors={colors} 
          openSections={openSections}
          toggle={toggle}
          toggleKey1="thrd"
          toggleKey2="who"
          icon1="information-circle-outline"
          title1="What is THRD?"
          description1="THRD is a social coordination platform designed to remove the friction from seeing people you trust."
          icon2="people-outline"
          title2="Who is THRD for?"
          description2="THRD supports three profile types: Individual, Runner, and Business."
        />

        <HelpCentreSection 
          headTitle="CIRCLES & PLANNING"
          styles={styles}
          colors={colors} 
          openSections={openSections}
          toggle={toggle}
          toggleKey1="circle"
          toggleKey2="create"
          icon1="information-circle-outline"
          title1="What is a Circle?"
          description1="A Circle is a group of people you trust and want to coordinate with."
          icon2="add"
          title2="How do I create a Circle?"
          description2="You can create a Circle by inviting people you trust to join."
        />

        {/* Scheduling & Availability  */}
        <HelpCentreSection 
          headTitle="SCHEDULING & AVAILABILITY"
          styles={styles}
          colors={colors} 
          openSections={openSections}
          toggle={toggle}
          toggleKey1="how_does_thrd"
          toggleKey2="what_does_connecting"
          icon1="sparkles"
          title1="How does THRD find time?"
          description1="Our AI Scheduler looks for overlaps in members' 'Busy Slots'. It doesn't share your private calendar details, just the times you're unavailable, helping the group pick a slot that works for everyone without the back-and-forth."
          icon2="calendar"
          title2="What does connecting calendars do?"
          description2="Connecting your Google or Apple calendar automatically updates your 'Busy' slots. This keeps your Circle's scheduling suggestions accurate without you having to manually block off time."
        />

        {/* Explore & Mindspace  */}
        <HelpCentreSection 
          headTitle="EXPLORE & MINDSPACE"
          styles={styles}
          colors={colors} 
          openSections={openSections}
          toggle={toggle}
          toggleKey1="finding_new_spaces"
          toggleKey2="what_is_mind"
          icon1="navigate"
          title1="Finding new spaces and events"
          description1="Use the 'Explore' tab to discover local venues and community gatherings. You can 'Heart' spaces to save them to your private collection for future planning."
          icon2="cloud"
          title2="What is Mind Space?"
          description2="Mind Space is a quiet sanctuary within the app for reflecting on your social health. It offers grounding content on topics like social anxiety, overstimulation, and the importance of solitude."
        />

        {/* Privacy & Control  */}
        <HelpCentreSection 
          headTitle="PRIVACY & CONTROL"
          styles={styles}
          colors={colors} 
          openSections={openSections}
          toggle={toggle}
          toggleKey1="privacy_settings"
          toggleKey2="is_my_data"
          icon1="lock-closed-outline"
          title1="Who can see my profile?"
          description1="You have full control. You can set your profile to Public (visible in Explore), Friends Only, or Invisible. These settings can be changed instantly in your Profile menu."
          icon2="shield-checkmark-outline"
          title2="Is my data secure?"
          description2="THRD uses end-to-end encryption for all Circle messages and never shares the specific details of your linked calendars with other users."
        />

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
      backgroundColor: colors.stone950,
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
      borderColor: colors.accent,
      borderRadius: 26,
      paddingVertical: 10,
      paddingHorizontal: 16,
      marginBottom: 26,
      backgroundColor: colors.stone950,
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