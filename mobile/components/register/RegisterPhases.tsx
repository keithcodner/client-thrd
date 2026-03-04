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
  goToLogin: () => void;
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
  goToLogin,
  canGoNext,
  isLastPhase,
  children,
}) => {
  const colors = useThemeColours();
  const progress = ((currentPhase + 1) / phases.length) * 100;

  return (
    <View style={{ flex: 1, backgroundColor: colors.background }}>
      {/* Progress Bar */}
      <View 
        className="pt-6"
        style={{ 
          height: 6,
          backgroundColor: '#C4F547',
          width: '100%',
          overflow: 'hidden',
        }}
      >
        <View 
          style={{ 
            height: '100%',
            backgroundColor: '#C4F547',
            width: `${progress}%`,
          }}
        />
      </View>

      {/* Header with Back Button */}
      <View className="flex-row items-center justify-between px-6 py-4">
        <TouchableOpacity 
          onPress={onBack} 
          className="p-2"
        >
          <Text className="pt-10" style={{ color: colors.text, fontSize: 28, fontWeight: '300' }}>←</Text>
        </TouchableOpacity>
        <TouchableOpacity onPress={goToLogin} className="p-2">
          <Text className="pt-12" style={{ color: colors.secondaryText, fontSize: 12, letterSpacing: 1 }}>LOGIN</Text>
        </TouchableOpacity>
      </View>

      <View className="flex-1 justify-center px-0">
        {/* Title */}
        <Animated.View style={{ opacity: fadeAnim }} className="items-center px-8 mb-8">
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
        <Animated.View style={{ opacity: fadeAnim }}>
          {children}
        </Animated.View>

        {/* Next Button */}
        <View className="items-center px-8 pt-6">
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

