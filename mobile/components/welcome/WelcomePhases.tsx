import React from "react";
import { Text, View, TouchableOpacity, Animated } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { AnimatedWaves } from "./AnimatedWaves";
import { ProgressDots } from "./ProgressDots";
import { Phase } from "@/types/welcome/welcome";

type WelcomePhasesProps = {
  phases: Phase[];
  currentPhase: number;
  fadeAnim: Animated.Value;
  wave1Y1: Animated.Value;
  wave1Y2: Animated.Value;
  wave2Y1: Animated.Value;
  wave2Y2: Animated.Value;
  onNext: () => void;
  onBack: () => void;
  onSkipIntro: () => void;
};

export const WelcomePhases: React.FC<WelcomePhasesProps> = ({
  phases,
  currentPhase,
  fadeAnim,
  wave1Y1,
  wave1Y2,
  wave2Y1,
  wave2Y2,
  onNext,
  onBack,
  onSkipIntro,
}) => {
  const colors = useThemeColours();

  return (
    <View style={{ flex: 1, backgroundColor: colors.background }}>
      {/* Header with Back and Skip Buttons */}
      <View className="flex-row items-center justify-between px-6 py-2">
        <TouchableOpacity 
          onPress={onBack} 
          className="p-2"
          disabled={currentPhase === 0}
          style={{ opacity: currentPhase === 0 ? 0.3 : 1 }}
        >
          <Text className="pt-12" style={{ color: colors.text, fontSize: 28 }}>←</Text>
        </TouchableOpacity>
        <TouchableOpacity onPress={onSkipIntro} className="p-2">
          <Text className="pt-12" style={{ color: colors.secondaryText, fontSize: 12, letterSpacing: 1 }}>SKIP INTRO</Text>
        </TouchableOpacity>
      </View>

      <View className="flex-1 justify-between px-8 py-8">
        {/* Animated Wave Lines */}
        <AnimatedWaves
          wave1Y1={wave1Y1}
          wave1Y2={wave1Y2}
          wave2Y1={wave2Y1}
          wave2Y2={wave2Y2}
          accentColor={colors.accent}
          borderColor={colors.border}
        />

        {/* Content */}
        <Animated.View style={{ opacity: fadeAnim }} className="items-center">
          <Text
            style={{ color: colors.text }}
            className="text-4xl font-bold text-center mb-4"
          >
            {phases[currentPhase].title}
          </Text>
          <Text
            style={{ color: colors.secondaryText }}
            className="text-lg text-center italic"
          >
            {phases[currentPhase].subtitle}
          </Text>
        </Animated.View>

        {/* Progress Dots & Button */}
        <View className="items-center">
          <ProgressDots
            total={phases.length}
            currentIndex={currentPhase}
            accentColor={colors.accent}
            borderColor={colors.border}
          />

          <TouchableOpacity
            onPress={onNext}
            style={{ backgroundColor: colors.text }}
            className="w-16 h-16 rounded-full items-center justify-center"
          >
            <Text style={{ color: colors.background }} className="text-2xl">
              →
            </Text>
          </TouchableOpacity>
        </View>
      </View>
    </View>
  );
};
