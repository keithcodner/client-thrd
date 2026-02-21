import React from "react";
import { Text, View, TouchableOpacity, ScrollView, Image } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { router } from "expo-router";
import { LinearGradient } from "expo-linear-gradient";

type Feature = {
  icon: string;
  title: string;
  subtitle: string;
};

const features: Feature[] = [
  {
    icon: "🎨",
    title: "Recolor Images",
    subtitle: "Choose Arbitrary Color",
  },
  {
    icon: "📸",
    title: "Restore Photos",
    subtitle: "In Excellent Quality",
  },
  {
    icon: "✨",
    title: "Generative Fill",
    subtitle: "Smart Expand",
  },
  {
    icon: "✂️",
    title: "Remove Objects",
    subtitle: "Clean Removal",
  },
];

const WelcomeScreen = () => {
  return (
    <SafeAreaView className="flex-1 bg-[#1a1a2e]">
      <ScrollView className="flex-1 px-6">
        {/* Header */}
        <View className="items-center pt-16 pb-8">
          <Image 
            source={require('@/assets/landing.png')} 
            className="w-[120px] h-[120px] mb-6" 
            resizeMode="contain" 
          />
          <Text className="text-2xl font-bold text-white mb-3">Imaginary</Text>
          <Text className="text-center text-gray-400 text-sm px-4">
            Transform your images with powerful AI tools - recolor, restore, fill, and remove objects with just a few taps
          </Text>
        </View>

        {/* Features Grid */}
        <View className="flex-row flex-wrap justify-between mb-8">
          {features.map((feature, index) => (
            <View
              key={index}
              className="w-[48%] bg-[#252541] rounded-2xl p-6 mb-4"
            >
              <Text className="text-4xl mb-3">{feature.icon}</Text>
              <Text className="text-white font-semibold text-base mb-1">
                {feature.title}
              </Text>
              <Text className="text-gray-400 text-xs">{feature.subtitle}</Text>
            </View>
          ))}
        </View>

        {/* Buttons */}
        <View className="pb-8">
          <TouchableOpacity
            className="border-2 border-teal-500 rounded-xl py-4 mb-4"
            onPress={() => router.push("/sign-in")}
            activeOpacity={0.7}
          >
            <Text className="text-teal-500 text-center font-semibold text-base">
              Log In
            </Text>
          </TouchableOpacity>

          <TouchableOpacity 
            onPress={() => router.push("/signup")}
            activeOpacity={0.7}
          >
            <LinearGradient
              colors={["#7c3aed", "#a855f7"]}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 1 }}
              className="rounded-xl py-4"
            >
              <Text className="text-white text-center font-semibold text-base">
                Create Account
              </Text>
            </LinearGradient>
          </TouchableOpacity>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
};

export default WelcomeScreen;
