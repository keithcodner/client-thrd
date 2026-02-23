import React from "react";
import { Text, View } from "react-native";
import { useSession } from "@/context/AuthContext";

const Main = () => {
  const { user } = useSession();
  return (
    <View>
      <Text>Welcome, {user?.name}</Text>
    </View>
  );
};

export default Main;
