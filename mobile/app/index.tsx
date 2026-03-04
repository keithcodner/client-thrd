import React, { useState, useRef } from "react";
import { Animated } from "react-native";
import { router } from "expo-router";
import { useTheme } from "@/context/ThemeContext";
import { 
  ThemeSelection, 
  WelcomePhases, 
  phases, 
  waveTargets,
  type ThemeMode 
} from "@/components/welcome";

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
    // Theme is already set in handleThemeSelect, just proceed to register wizard
    handlePress("/(auth)/register-wizard");
  };

  if (showThemeSelection) {
    return (
      <ThemeSelection
        selectedTheme={selectedTheme}
        onThemeSelect={handleThemeSelect}
        onBack={handleBack}
        onContinue={handleContinue}
      />
    );
  }

  return (
    <WelcomePhases
      phases={phases}
      currentPhase={currentPhase}
      fadeAnim={fadeAnim}
      wave1Y1={wave1Y1}
      wave1Y2={wave1Y2}
      wave2Y1={wave2Y1}
      wave2Y2={wave2Y2}
      onNext={handleNext}
      onBack={handleBack}
      onSkipIntro={handleSkipIntro}
    />
  );
};

export default WelcomeScreen;
