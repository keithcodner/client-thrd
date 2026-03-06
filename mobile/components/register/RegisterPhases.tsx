import React, { useRef, useEffect } from "react";
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
  onSkip: () => void;
  requiresSkip: boolean;
  canHideContinue: boolean;
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
  onSkip,
  canHideContinue,
  requiresSkip,
  canGoNext,
  isLastPhase,
  children,
}) => {
  const colors = useThemeColours();
  const progress = ((currentPhase + 1) / phases.length) * 100;
  const progressAnim = useRef(new Animated.Value(progress)).current;

  useEffect(() => {
    Animated.timing(progressAnim, {
      toValue: progress,
      duration: 400,
      useNativeDriver: false,
    }).start();
  }, [currentPhase]);

  return (
    <View style={{ flex: 1, backgroundColor: colors.background }}>
        {/* Progress Bar */}
        <View 
          style={{ 
            height: 4,
            backgroundColor: colors.card,
            width: '100%',
            overflow: 'hidden',
            marginTop: 48,
          }}
        >
          <Animated.View 
            style={{ 
              height: '100%',
              backgroundColor: '#C4F547',
              width: progressAnim.interpolate({
                inputRange: [0, 100],
                outputRange: ['0%', '100%'],
              }),
            }}
          />
        </View>

        {/* Header with Back Button */}
        <View className="flex-row items-center justify-between px-6 py-4">
          <TouchableOpacity 
            onPress={onBack} 
            className="p-2"
          >
            <Text className="" style={{ color: colors.text, fontSize: 28, fontWeight: '300' }}>←</Text>
          </TouchableOpacity>
          <TouchableOpacity onPress={goToLogin} className="p-2">
            <Text className="" style={{ color: colors.secondaryText, fontSize: 12, letterSpacing: 1 }}>LOGIN</Text>
          </TouchableOpacity>
        </View>

        {/* Content */}
        <View className="flex-1 justify-center px-0 -mt-24">
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
             { !canHideContinue &&(
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
                    {isLastPhase ? 'Enter your space' : 'Continue'}
                  </Text>
                </TouchableOpacity>

              )
            }

            {/* Skip Button */}
            { requiresSkip && (
                <TouchableOpacity
                  onPress={onSkip}
                  className="pt-8"
                  style={{
                    paddingVertical: 12,
                  }}
                >
                  <Text
                    style={{
                      color: colors.secondaryText,
                      fontSize: 12,
                      fontWeight: '600',
                      letterSpacing: 1,
                    }}
                  >
                    SKIP FOR NOW
                  </Text>
                </TouchableOpacity>
              )

            }
          </View>
        </View>
    </View>
  );
};

