import React, { useState, useEffect, useCallback } from "react";
import { TouchableOpacity, Text, View, Alert, ScrollView, ActivityIndicator } from "react-native";
import { useSession } from "@/context/AuthContext";
import { router } from "expo-router";
import { MaterialIcons } from "@expo/vector-icons";
import axiosInstance from "@/config/axiosConfig";
import FeatureCard from "@/components/app/FeatureCard";
import SplitImageCard from "@/components/app/SplitImageCard";
import { useThemeColours } from "@/hooks/useThemeColours";

interface Operation {
    id: number;
    user_id: number;
    original_image: string;
    generated_image: string;
    operation_type: string;
    operation_metadata: any;
    created_at: string;
    updated_at: string;
}

const Index = () => {
  const colours = useThemeColours();
  const { user } = useSession();
  const [ operationCredits, setOperationCredits ] = useState<Record<string, number>>({});
  const [latestOperations, setLatestOperations] = useState<Operation[]>([]);
  const [loading, setLoading] = useState<boolean>(false);

  const handleOperationPress = useCallback((operationId: number) => {
    router.push({pathname: '/operation/[id]', params: { id: operationId }});
  }, []);

  useEffect(() => {
    const fetchOperationCredits = async () => {
      try {
        const response = await axiosInstance.get("/operations/credits");
        setOperationCredits(response.data.operations);
      } catch (error: any) {
        Alert.alert("Error", error.message || "Failed to fetch operation credits.");
      }
    };

    const fetchLatestOperation = async () => {
      setLoading(true);
      try {
        const response = await axiosInstance.get("/image/latest-operations", {
          params: { page: 1, per_page: 3 }
        });

        setLatestOperations(response.data.operations);
      } catch (error: any) {
        Alert.alert("Error", error.message || "Failed to fetch operation credits.");
      } finally {
        setLoading(false);
      }
    };

    fetchOperationCredits();
    fetchLatestOperation();
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

      {/* Page content */}
      <ScrollView className="flex-1">
        
        <View className='p-4 mt-10'>
          {/* Welcome Section */}
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

          {/* Feature Cards Section */}
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

          {/* Latest Operations */}
          <View className="mb-4">
            <Text className="text-2xl font-bold text-gray-600 dark:text-white mb-4">
              Latest Operations
            </Text>

            {loading ? (
              <View className="items-center justify-center py-8">
                <ActivityIndicator size="large" color={colours.primary} />
              </View>
            ) : latestOperations.length > 0 ? (
              <View className="items-center">
                {latestOperations.map((operation) => (
                  <TouchableOpacity
                    key={operation.id}
                    onPress={() => handleOperationPress(operation.id)}
                    activeOpacity={0.9}
                  >
                    <SplitImageCard
                      originalImage={operation.original_image}
                      generatedImage={operation.generated_image}
                      operationType={operation.operation_type}
                      createdAt={operation.created_at}
                    />
                  </TouchableOpacity>
                ))}
              </View>
            ) : (
              <View className="items-center justify-center py-8 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <MaterialIcons name="image-not-supported" size={48} color={colours.secondaryText} />
                <Text className="text-gray-500 dark:text-gray-400 mt-2 text-center">
                  You haven't created any operations yet.
                </Text>
              </View>
            )}
          </View>
        </View>
      </ScrollView>
    </View>
  );
};

export default Index;
