import React, { useState, useRef, useEffect, useMemo, useCallback } from "react";
import { Text, View, TouchableOpacity, ScrollView, Dimensions, Platform, Animated } from "react-native";
import { router } from "expo-router";
import { useTheme } from "@/context/ThemeContext";
import { useThemeColours } from "@/hooks/useThemeColours";
import Svg, { Path } from "react-native-svg";
import ButtoTHRDIconButtonn from "@/components/core/THRDIconButton";
import { Sun, Moon, Monitor } from 'lucide-react-native';
import THRDIconButton from "@/components/core/THRDIconButton";

const { width } = Dimensions.get("window");

type Phase = {
  title: string;
  subtitle: string;
};

const phases: Phase[] = [
  {
    title: "Planning made lighter.",
    subtitle: "Find time, without the friction.",
  },
  {
    title: "Smart coordination.",
    subtitle: "AI finds the gaps so you can just show up.",
  },
  {
    title: "Your community, synced.",
    subtitle: "Friends. Circles. Third Spaces.",
  },
];

type ThemeMode = "light" | "dark" | "system";

// Animated SVG Path component
const AnimatedPath = Animated.createAnimatedComponent(Path);

const WelcomeScreen = () => {
  const [currentPhase, setCurrentPhase] = useState(0);
  const [showThemeSelection, setShowThemeSelection] = useState(false);
  const [selectedTheme, setSelectedTheme] = useState<ThemeMode>("light");
  const { setTheme, currentTheme } = useTheme();
  
  const lastPressTime = useRef(0);
  const fadeAnim = useRef(new Animated.Value(1)).current;
  
  // Animation values for wave control points
  const wave1Y1 = useRef(new Animated.Value(50)).current;  // First wave Q1 Y
  const wave1Y2 = useRef(new Animated.Value(100)).current; // First wave T Y
  const wave2Y1 = useRef(new Animated.Value(170)).current; // Second wave Q1 Y
  const wave2Y2 = useRef(new Animated.Value(120)).current; // Second wave T Y

  const handlePress = (route: string) => {
    const now = Date.now();
    if (now - lastPressTime.current < 1000) return;
    lastPressTime.current = now;
    router.push(route as any);
  };

  const handleNext = () => {
    if (currentPhase < phases.length - 1) {
      const nextPhase = currentPhase + 1;
      
      // Animate text fade
      Animated.sequence([
        Animated.timing(fadeAnim, {
          toValue: 0,
          duration: 300,
          useNativeDriver: true,
        }),
        Animated.timing(fadeAnim, {
          toValue: 1,
          duration: 300,
          useNativeDriver: true,
        }),
      ]).start();

      // Animate wave lines smoothly
      const duration = 800;
      const config = {
        duration,
        useNativeDriver: false,
      };

      // Define target positions for each phase
      const waveTargets = [
        { wave1Y1: 50, wave1Y2: 100, wave2Y1: 170, wave2Y2: 120 },  // Phase 0
        { wave1Y1: 150, wave1Y2: 100, wave2Y1: 70, wave2Y2: 120 },  // Phase 1
        { wave1Y1: 100, wave1Y2: 50, wave2Y1: 120, wave2Y2: 170 },  // Phase 2
      ];

      Animated.parallel([
        Animated.spring(wave1Y1, {
          toValue: waveTargets[nextPhase].wave1Y1,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
        Animated.spring(wave1Y2, {
          toValue: waveTargets[nextPhase].wave1Y2,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
        Animated.spring(wave2Y1, {
          toValue: waveTargets[nextPhase].wave2Y1,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
        Animated.spring(wave2Y2, {
          toValue: waveTargets[nextPhase].wave2Y2,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
      ]).start();

      setCurrentPhase(nextPhase);
    } else {
      setShowThemeSelection(true);
    }
  };

  const handleThemeSelect = (theme: ThemeMode) => {
    setSelectedTheme(theme);
    // Immediately apply the theme for live preview
    setTheme(theme);
  };

  const handleBack = () => {
    if (showThemeSelection) {
      setShowThemeSelection(false);
      // Reset to last phase
      setCurrentPhase(phases.length - 1);
    } else if (currentPhase > 0) {
      const prevPhase = currentPhase - 1;
      
      // Animate text fade
      Animated.sequence([
        Animated.timing(fadeAnim, {
          toValue: 0,
          duration: 300,
          useNativeDriver: true,
        }),
        Animated.timing(fadeAnim, {
          toValue: 1,
          duration: 300,
          useNativeDriver: true,
        }),
      ]).start();

      // Animate waves back to previous phase
      const waveTargets = [
        { wave1Y1: 50, wave1Y2: 100, wave2Y1: 170, wave2Y2: 120 },  // Phase 0
        { wave1Y1: 150, wave1Y2: 100, wave2Y1: 70, wave2Y2: 120 },  // Phase 1
        { wave1Y1: 100, wave1Y2: 50, wave2Y1: 120, wave2Y2: 170 },  // Phase 2
      ];

      Animated.parallel([
        Animated.spring(wave1Y1, {
          toValue: waveTargets[prevPhase].wave1Y1,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
        Animated.spring(wave1Y2, {
          toValue: waveTargets[prevPhase].wave1Y2,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
        Animated.spring(wave2Y1, {
          toValue: waveTargets[prevPhase].wave2Y1,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
        Animated.spring(wave2Y2, {
          toValue: waveTargets[prevPhase].wave2Y2,
          tension: 40,
          friction: 8,
          useNativeDriver: false,
        }),
      ]).start();

      setCurrentPhase(prevPhase);
    }
  };

  const handleSkipIntro = () => {
    setShowThemeSelection(true);
  };

  const handleContinue = () => {
    // Theme is already set in handleThemeSelect, just proceed
    handlePress("/signup");
  };

  const colors = useThemeColours();
  const bgColor = colors.background;
  const textColor = colors.text;
  const subtitleColor = colors.secondaryText;
  const accentColor = colors.accent;
  const borderColor = colors.border;

  if (showThemeSelection) {
    return (
      <View style={{ flex: 1, backgroundColor: bgColor }}>
        {/* Header with Back Button */}
        <View className="flex-row items-center justify-between px-6 py-4">
          <TouchableOpacity onPress={handleBack} className="p-2">
            <Text className="pt-10" style={{ color: textColor, fontSize: 28, fontWeight: '300' }}>←</Text>
          </TouchableOpacity>
          <View style={{ width: 40 }} />
        </View>

        <View className="flex-1 justify-center items-center px-8">
          <Text
            style={{ 
              color: textColor,
              fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
              fontSize: 28,
              fontWeight: '500',
              marginBottom: 8,
              textAlign: 'center'
            }}
          >
            Make THRD feel like yours.
          </Text>
          <Text
            style={{ 
              color: subtitleColor,
              fontSize: 14,
              textAlign: 'center',
              marginBottom: 48
            }}
          >
            Choose how you want your space to feel.
          </Text>

          <View className="w-full mb-8">

            <THRDIconButton 
              title="Light Mode"
              Icon={Sun}
              onPress={() => handleThemeSelect("light")}
              className="mb-4"
              disabled={selectedTheme === "light"}
            />

            <THRDIconButton 
              title="Dark Mode"
              Icon={Moon}
              onPress={() => handleThemeSelect("dark")}
              className="mb-4"
              disabled={selectedTheme === "dark"}
            />
            
            {/* <TouchableOpacity
              onPress={() => handleThemeSelect("light")}
              style={{
                borderWidth: 2,
                borderColor: selectedTheme === "light" ? accentColor : borderColor,
                backgroundColor: selectedTheme === "light" ? accentColor : colors.card,
                borderRadius: 32,
                paddingVertical: 16,
                paddingHorizontal: 24,
                marginBottom: 16,
                flexDirection: 'row',
                alignItems: 'center'
              }}
            >
              <View style={{
                width: 40,
                height: 40,
                borderRadius: 12,
                backgroundColor: selectedTheme === "light" ? colors.background : colors.card,
                alignItems: 'center',
                justifyContent: 'center',
                marginRight: 16
              }}>
                <Sun 
                  size={20} 
                  color={selectedTheme === "light" ? accentColor : colors.secondaryText}
                  strokeWidth={2}
                />
              </View>
              <Text style={{ 
                color: selectedTheme === "light" ? colors.background : textColor,
                fontSize: 16,
                fontWeight: '600'
              }}>
                Light Mode
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              onPress={() => handleThemeSelect("dark")}
              style={{
                borderWidth: 2,
                borderColor: selectedTheme === "dark" ? accentColor : borderColor,
                backgroundColor: selectedTheme === "dark" ? accentColor : colors.card,
                borderRadius: 32,
                paddingVertical: 16,
                paddingHorizontal: 24,
                marginBottom: 16,
                flexDirection: 'row',
                alignItems: 'center'
              }}
            >
              <View style={{
                width: 40,
                height: 40,
                borderRadius: 12,
                backgroundColor: selectedTheme === "dark" ? colors.background : colors.card,
                alignItems: 'center',
                justifyContent: 'center',
                marginRight: 16
              }}>
                <Moon 
                  size={20} 
                  color={selectedTheme === "dark" ? accentColor : colors.secondaryText}
                  strokeWidth={2}
                />
              </View>
              <Text style={{ 
                color: selectedTheme === "dark" ? colors.background : textColor,
                fontSize: 16,
                fontWeight: '600'
              }}>
                Dark Mode
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              onPress={() => handleThemeSelect("system")}
              style={{
                borderWidth: 2,
                borderColor: selectedTheme === "system" ? accentColor : borderColor,
                backgroundColor: selectedTheme === "system" ? accentColor : colors.card,
                borderRadius: 32,
                paddingVertical: 16,
                paddingHorizontal: 24,
                flexDirection: 'row',
                alignItems: 'center'
              }}
            >
              <View style={{
                width: 40,
                height: 40,
                borderRadius: 12,
                backgroundColor: selectedTheme === "system" ? colors.background : colors.card,
                alignItems: 'center',
                justifyContent: 'center',
                marginRight: 16
              }}>
                <Monitor 
                  size={20} 
                  color={selectedTheme === "system" ? accentColor : colors.secondaryText}
                  strokeWidth={2}
                />
              </View>
              <Text style={{ 
                color: selectedTheme === "system" ? colors.background : textColor,
                fontSize: 16,
                fontWeight: '600'
              }}>
                System Default
              </Text>
            </TouchableOpacity> */}
          </View>

          <TouchableOpacity
            onPress={handleContinue}
            style={{
              backgroundColor: colors.text,
              width: '100%',
              borderRadius: 24,
              paddingVertical: 16,
              paddingHorizontal: 32,
            }}
          >
            <Text style={{ 
              color: colors.background,
              textAlign: 'center',
              fontSize: 16,
              fontWeight: '700'
            }}>
              Continue
            </Text>
          </TouchableOpacity>
        </View>
      </View>
    );
  }

  return (
    <View style={{ flex: 1, backgroundColor: bgColor }}>
      {/* Header with Back and Skip Buttons */}
      <View className="flex-row items-center justify-between px-6 py-2">
        <TouchableOpacity 
          onPress={handleBack} 
          className="p-2"
          disabled={currentPhase === 0}
          style={{ opacity: currentPhase === 0 ? 0.3 : 1 }}
        >
          <Text className="pt-12" style={{ color: textColor, fontSize: 28 }}>←</Text>
        </TouchableOpacity>
        <TouchableOpacity onPress={handleSkipIntro} className="p-2">
          <Text className="pt-12" style={{ color: subtitleColor, fontSize: 12, letterSpacing: 1 }}>SKIP INTRO</Text>
        </TouchableOpacity>
      </View>

      <View className="flex-1 justify-between px-8 py-8">
        {/* Animated Wave Lines */}
        <View className="items-center mt-20">
          <Svg height="200" width={width - 64}>
            <AnimatedPath
              d={wave1Y1.interpolate({
                inputRange: [0, 200],
                outputRange: [
                  `M 0 100 Q ${width / 4} 0, ${width / 2} 100 T ${width - 64} 100`,
                  `M 0 100 Q ${width / 4} 200, ${width / 2} 100 T ${width - 64} 100`
                ]
              }) as any}
              stroke={accentColor}
              strokeWidth="3"
              fill="none"
            />
            <AnimatedPath
              d={wave2Y1.interpolate({
                inputRange: [0, 200],
                outputRange: [
                  `M 0 120 Q ${width / 4} 0, ${width / 2} 120 T ${width - 64} 120`,
                  `M 0 120 Q ${width / 4} 200, ${width / 2} 120 T ${width - 64} 120`
                ]
              }) as any}
              stroke={borderColor}
              strokeWidth="2"
              fill="none"
              opacity="0.5"
            />
          </Svg>
        </View>

        {/* Content */}
        <Animated.View style={{ opacity: fadeAnim }} className="items-center">
          <Text
            style={{ color: textColor }}
            className="text-4xl font-bold text-center mb-4"
          >
            {phases[currentPhase].title}
          </Text>
          <Text
            style={{ color: subtitleColor }}
            className="text-lg text-center italic"
          >
            {phases[currentPhase].subtitle}
          </Text>
        </Animated.View>

        {/* Progress Dots & Button */}
        <View className="items-center">
          <View className="flex-row mb-8">
            {phases.map((_, index) => (
              <View
                key={index}
                style={{
                  backgroundColor:
                    index === currentPhase ? accentColor : borderColor,
                  width: index === currentPhase ? 32 : 8,
                }}
                className="h-2 rounded-full mx-1"
              />
            ))}
          </View>

          <TouchableOpacity
            onPress={handleNext}
            style={{ backgroundColor: textColor }}
            className="w-16 h-16 rounded-full items-center justify-center"
          >
            <Text style={{ color: bgColor }} className="text-2xl">
              →
            </Text>
          </TouchableOpacity>
        </View>
      </View>
    </View>
  );
};

export default WelcomeScreen;
