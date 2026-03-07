import React from "react";
import { View, Text, StyleSheet } from "react-native";

interface HomeGreetingProps {
  colors: any;
  greeting: string;
  firstName: string;
}

export const HomeGreeting = ({
  colors,
  greeting,
  firstName,
}: HomeGreetingProps) => {
  return (
    <View style={styles.greetingContainer}>
      <Text style={[styles.greeting, { color: colors.text }]}>
        {greeting},
        {"\n"}
        <Text style={[styles.name, { color: colors.primary }]}>
          {firstName}
        </Text>
      </Text>
    </View>
  );
};

const styles = StyleSheet.create({
  greetingContainer: {
    paddingHorizontal: 20,
    marginTop: 10,
  },

  greeting: {
    fontSize: 34,
    fontWeight: "500",
  },

  name: {},
});
