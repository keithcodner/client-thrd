import React from "react";
import { Platform, Text, View, Alert } from "react-native";
import { useSession } from "@/context/AuthContext";
import { Button } from "@react-navigation/elements";
import { API_BASE_URL } from "@/config/env";
import axios from "axios";

const Main = () => {
  const { user, signOut } = useSession();


  return (
    <View>
      
    </View>
  );
};

export default Main;
