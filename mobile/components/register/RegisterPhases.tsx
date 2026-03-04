import React from "react";
import { Text, View, TouchableOpacity, Animated, Platform } from "react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { RegisterPhase } from "@/types/register/register";

type RegisterPhasesProps = {
  phases: RegisterPhase[];
  currentPhase: number;
  fadeAnim: Animated.Value;
  onNext: () => void;
  onBack: () => void;
  canGoNext: boolean;
  isLastPhase: boolean;
  children: React.ReactNode;
};

export const RegisterPhases: React.FC<RegisterPhasesProps> = ({
  phases,
  currentPhase,
  fadeAnim,
  onNext,
  onBack,
  canGoNext,
  isLastPhase,
  children,
}) => {
  const colors = useThemeColours();

  return (
    <View style={{ flex: 1, backgroundColor: colors.background }}>
      {/* Header with Back Button */}
      <View className="flex-row items-center justify-between px-6 py-4">
        <TouchableOpacity 
          onPress={onBack} 
          className="p-2"
          disabled={currentPhase === 0}
          style={{ opacity: currentPhase === 0 ? 0.3 : 1 }}
        >
          <Text className="pt-10" style={{ color: colors.text, fontSize: 28, fontWeight: '300' }}>←</Text>
        </TouchableOpacity>
        <View style={{ width: 40 }} />
      </View>

      <View className="flex-1 justify-between px-0 py-8">
        {/* Title */}
        <Animated.View style={{ opacity: fadeAnim }} className="items-center px-8 mb-12">
          <Text
            style={{ 
              color: colors.text,
              fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
              fontSize: 32,
              fontWeight: '400',
              marginBottom: phases[currentPhase].subtitle ? 8 : 0,
              textAlign: 'center'
            }}
          >
            {phases[currentPhase].title}
          </Text>
          {phases[currentPhase].subtitle && (
            <Text
              style={{ 
                color: colors.secondaryText,
                fontSize: 14,
                textAlign: 'center',
                marginTop: 8,
                lineHeight: 20,
              }}
            >
              {phases[currentPhase].subtitle}
            </Text>
          )}
        </Animated.View>

        {/* Content */}
        <Animated.View style={{ opacity: fadeAnim, flex: 1 }} className="justify-center">
          {children}
        </Animated.View>

        {/* Next Button */}
        <View className="items-center px-8 pt-8">
          <TouchableOpacity
            onPress={onNext}
            disabled={!canGoNext}
            style={{
              backgroundColor: canGoNext ? colors.text : colors.card,
              width: '100%',
              borderRadius: 24,
              paddingVertical: 16,
              paddingHorizontal: 32,
            }}
          >
            <Text style={{ 
              color: canGoNext ? colors.background : colors.secondaryText,
              textAlign: 'center',
              fontSize: 16,
              fontWeight: '700'
            }}>
              {isLastPhase ? 'Enter your space' : 'Next'}
            </Text>
          </TouchableOpacity>
        </View>
      </View>
    </View>
  );
};

