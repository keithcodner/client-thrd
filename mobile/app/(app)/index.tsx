import React from "react";
import { Text, View } from "react-native";
import { useSession } from "@/context/AuthContext";
import { Button } from "@react-navigation/elements";
import { API_BASE_URL } from "@/config/env";
import axios from "axios";

const Main = () => {
  const { user } = useSession();

  const handleLogout = async () => {
     try {
      const response = await axios.post(`${API_BASE_URL}/logout`);
     } catch (error) {
      
     } 
  };

  return (
    <View>
      <Text>Welcome, {user?.name}</Text>
      <Button onPress={handleLogout} >
        Log out
      </Button>
    </View>
  );
};

export default Main;
