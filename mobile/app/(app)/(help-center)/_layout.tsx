import React from "react";
import { Stack } from "expo-router";

export default function HelpCenterLayout() {
  return (
    <Stack
      screenOptions={{
        headerShown: false, // <- hide the native header completely
      }}
    >
      <Stack.Screen name="index" />
      <Stack.Screen name="[id]" />
    </Stack>
  );
}