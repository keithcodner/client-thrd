import React from 'react';
import {
  View,
  Text,
  ActivityIndicator
} from 'react-native';
import { useThemeColours } from '@/hooks/useThemeColours';

// This component is used to show a processing overlay when the AI is working on the image. It can be reused across different screens where image processing occurs.
interface ProcessingOverlayProps {
  visible: boolean;
message?: string;
}

// A simple overlay component to indicate processing state
export default function ProcessingOverlay({
  visible,
  message = 'Processing your image...'
}: ProcessingOverlayProps) {
  const colors = useThemeColours();

  if (!visible) return null;

//   The overlay is a semi-transparent layer that covers the entire screen, with a centered message and activity indicator to show that processing is underway.
  return (
        <View className="absolute inset-0 bg-black/30 flex items-center justify-center rounded-xl">
            <View className="bg-white dark:bg-gray-800 p-4 rounded-xl flex-row items-center">
            <ActivityIndicator size="small" color={colors.primary} />
            <Text className="ml-3 text-gray-700 dark:text-gray-300">{message}</Text>
            </View>
        </View>
    );
}