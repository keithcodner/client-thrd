import React, { useRef, useState } from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  Image,
  Dimensions,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import PagerView from 'react-native-pager-view';

const { width } = Dimensions.get('window');

interface ImageComparisonProps {
    originalImage: string;
    processedImage: string;
    processedLabel?: string;
    onFullscreenRequest: (index: number) => void;
}

export default function ImageComparison({
  originalImage,
  processedImage,
  processedLabel = 'AI Processed',
  onFullscreenRequest
}: ImageComparisonProps) {
  const pagerRef = useRef<PagerView | null>(null);

  return (
    <>
        {/* Image comparison section */}
        <View className="flex-row items-center mb-3">
            <MaterialIcons name="compare" size={24} color="#10B981" />
            <Text className="text-lg font-bold ml-2 text-gray-800 dark:text-white">
            Compare Images
            </Text>
        </View>

        {/* Image comparison with swipe */}
        <View className="relative w-full rounded-xl overflow-hidden mb-4 border border-gray-200 dark:border-gray-700">
            <PagerView
                style={{ width: '100%', height: width * 0.7 }}
                initialPage={0}
                ref={pagerRef}
            >
                {/* Original Image */}
                <View key="original">
                    <TouchableOpacity
                        activeOpacity={0.9}
                        onPress={() => onFullscreenRequest(0)}
                        style={{ width: '100%', height: '100%' }}
                    >
                        <Image
                        source={{ uri: originalImage }}
                        style={{ width: '100%', height: '100%' }}
                        resizeMode="cover"
                        />
                        <View className="absolute top-2 left-2 bg-black/50 rounded-full px-2 py-1 flex-row items-center">
                            <Text className="text-white text-xs font-medium">Original</Text>
                        </View>
                    </TouchableOpacity>
                </View>

                {/* Processed Image */}
                <View key="processed">
                    <TouchableOpacity
                        activeOpacity={0.9}
                        onPress={() => onFullscreenRequest(1)}
                        style={{ width: '100%', height: '100%' }}
                    >
                        <Image
                        source={{ uri: processedImage }}
                        style={{ width: '100%', height: '100%' }}
                        resizeMode="cover"
                        />
                        <View className="absolute top-2 left-2 bg-primary/80 rounded-full px-2 py-1 flex-row items-center">
                            <MaterialIcons name="auto-fix-high" size={16} color="#fff" />
                            <Text className="text-white text-xs ml-1 font-medium">
                                {processedLabel}
                            </Text>
                        </View>
                    </TouchableOpacity>
                </View>
            </PagerView>

            {/* Swipe indicator */}
            <View className="absolute bottom-4 left-0 right-0 flex-row justify-center">
                <View className="flex-row bg-black/30 rounded-full px-3 py-1.5 items-center">
                    <MaterialIcons name="swipe" size={16} color="#fff" />
                    <Text className="text-white text-xs ml-1">Swipe to compare</Text>
                </View>
            </View>
        </View>
    </>
  );
}