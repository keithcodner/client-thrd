import React, { useState, useEffect } from "react";
import { TouchableOpacity, Text, View, Alert, ScrollView } from "react-native";
import { useSession } from "@/context/AuthContext";
import { router } from "expo-router";
import { MaterialIcons } from "@expo/vector-icons";
import axiosInstance from "@/config/axiosConfig";
import FeatureCard from "@/components/app/FeatureCard";
import { useThemeColours } from "@/hooks/useThemeColours";

const Index = () => {
  const colours = useThemeColours();
  const { user } = useSession();
  const [ operationCredits, setOperationCredits ] = useState<Record<string, number>>({});

  useEffect(() => {
    const fetchOperationCredits = async () => {
      try {
        const response = await axiosInstance.get("/operations/credits");
        setOperationCredits(response.data.operations);
      } catch (error: any) {
        Alert.alert("Error", error.message || "Failed to fetch operation credits.");
      }
    };

    fetchOperationCredits();
  }, []);

  const cards = [
    {
      title: "Generative Fill",
      description: "Expand your images with AI-powered generative filling.",
      icon: "chat",
      credits: '/generative-fill',
      operationType: 'generative-fill',
    },
    {
      title: "Restore Images",
      description: "Restore your images to their original hi-res quality.",
      icon: "restore",
      credits: '/restore',
      operationType: 'restore',
    },
    {
      title: "Recolour Images",
      description: "Change the colors of your images with AI recoloring.",
      icon: "palette",
      credits: '/recolour',
      operationType: 'recolour',
    },
    {
      title: "Remove Objects",
      description: "Remove unwanted objects from your images!",
      icon: "content-cut",
      credits: '/remove',
      operationType: 'remove_objects',
    }
  ];

  return (
    <View className="flex-1 bg-white dark:bg-gray-900">
      <ScrollView className="flex-1">
        <View className='p-4 mt-10'>
          <View className="flex flex-row justify-between items-center mb-6">
            <Text className="text-2xl font-bold text-gray-800 dark:text-white">Welcome, {user?.name || "User"}!</Text>
            <TouchableOpacity 
              onPress={() => router.push('/credits')}
              className="flex-row items-center"
            >
              <MaterialIcons name="stars" size={24} color={colours.primary} style={{marginRight:8 }} />
              <Text className="text-2xl font-bold text-gray-800 dark:text-white">
                {user?.credits ?? 0} Credits
              </Text>
            </TouchableOpacity>
          </View>

          <View className="flex-row flex-wrap justify-between mb-6">
            {cards.map((card) => (
              <FeatureCard
                key={card.title}
                title={card.title}
                description={card.description}
                icon={card.icon as keyof typeof MaterialIcons.glyphMap}
                gradient={[colours.card, colours.surface ] as readonly [string, string, ...string[]]}
                credits={operationCredits[card.operationType]}
                onPress={() => router.push(card.credits as any)}
              />
            ))}
          </View>
        </View>
      </ScrollView>
    </View>
  );
};

export default Index;
