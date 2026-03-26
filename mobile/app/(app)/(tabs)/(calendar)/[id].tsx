import React from "react";
import { View, Text, ScrollView, StyleSheet } from "react-native";
import { useLocalSearchParams } from "expo-router";
import { useThemeColours } from "@/hooks/useThemeColours";

const CalendarDetail = () => {
  const colours = useThemeColours();
  const { id } = useLocalSearchParams();

  return (
    <View style={[styles.container, { backgroundColor: colours.background }]}>
      <ScrollView style={styles.scroll}>
        <View style={styles.content}>
          <Text style={[styles.title, { color: colours.text }]}>
            Calendar Item #{id}
          </Text>
          <Text style={[styles.subtitle, { color: colours.secondaryText }]}>
            Details for calendar item
          </Text>
        </View>
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: { flex: 1 },
  scroll:    { flex: 1 },
  content:   { padding: 16, marginTop: 40 },
  title:     { fontSize: 22, fontWeight: '700' },
  subtitle:  { marginTop: 8, fontSize: 14 },
});

export default CalendarDetail;
