import React, { useState, useRef, useEffect, useMemo, useCallback } from "react";
import { Text, View, Image, ScrollView, Alert, TouchableOpacity, Dimensions, Modal, ActivityIndicator } from "react-native";

import { MaterialIcons } from "@expo/vector-icons";
import PagerView from "react-native-pager-view";
import { GestureHandlerRootView } from 'react-native-gesture-handler';

import Button from "@/components/core/Button";
import AspectRatioSelector, { AspectRatio } from "@/components/app/AspectRatioSelector";
import { StatusBar }from "expo-status-bar";

const { width } = Dimensions.get("window");

interface FullscreenViewerProps {
    visible: boolean;
    onClose: () => void;
    originalImage: string | null;
    processedImage: string | null;
    initialPage: number;
    processedLabel?: string;
    onSave?: (imageUri: string) => void;
    savingImage?: boolean;
}

export default function FullscreenViewer({
    visible,
    onClose,
    originalImage,
    processedImage,
    initialPage,
    processedLabel = "Processed Image",
    onSave,
    savingImage = false 
}: FullscreenViewerProps) {

    const fullscreenPagerRef = useRef<PagerView | null>(null);
    const [currentIndex, setCurrentIndex] = useState(initialPage);

    return (
        <Modal
            visible={visible}
            transparent={true}
            animationType="fade"
            onRequestClose={onClose}
        >
            <GestureHandlerRootView style={{ flex: 1 }}>
                <View className="flex-1 bg-black">
                    <StatusBar style="light" />

                    <TouchableOpacity
                    className="absolute top-10 right-4 z-10 bg-black/50 rounded-full p-2"
                    onPress={onClose}
                    >
                        <MaterialIcons name="close" size={24} color="#fff" />
                    </TouchableOpacity>

                    <PagerView
                        style={{ flex: 1 }}
                        initialPage={initialPage}
                        ref={fullscreenPagerRef}
                        onPageSelected={(e) => setCurrentIndex(e.nativeEvent.position)}
                    >
                        {/* Pages go here */}
                        {originalImage && (
                            <View key="original" className="flex-1 justify-center">
                                <Image
                                source={{ uri: originalImage }}
                                style={{ width: '100%', height: width * 1.2 }}
                                resizeMode="contain"
                                />
                                <View className="absolute bottom-20 self-center bg-black/50 rounded-full px-3 py-1.5">
                                    <Text className="text-white">Original</Text>
                                </View>
                            </View>
                        )}

                        {processedImage && (
                            <View key="processed" className="flex-1 justify-center">
                                <Image
                                source={{ uri: processedImage }}
                                style={{ width: '100%', height: width * 1.2 }}
                                resizeMode="contain"
                                />
                                <View className="absolute bottom-20 self-center bg-black/50 rounded-full px-3 py-1.5 flex-row items-center">
                                    <MaterialIcons name="auto-fix-high" size={16} color="#fff" />
                                    <Text className="text-white ml-1">{processedLabel}</Text>
                                </View>
                            </View>
                        )}
                    </PagerView>

                    {/* Pagination indicator */}
                    <View className="absolute bottom-10 left-0 right-0 flex-row justify-center">
                        <View className="flex-row">
                            <View className={`w-2 h-2 rounded-full mx-1 ${currentIndex === 0 ? 'bg-white' : 'bg-gray-500'}`} />
                            <View className={`w-2 h-2 rounded-full mx-1 ${currentIndex === 1 ? 'bg-white' : 'bg-gray-500'}`} />
                        </View>
                    </View>

                    {/* Save button */}
                    {onSave && processedImage && currentIndex === 1 && (
                        <View className="absolute bottom-20 right-4">
                            <TouchableOpacity
                            className="bg-primary rounded-full p-3 mb-3"
                            onPress={() => onSave(processedImage)}
                            disabled={savingImage}
                            >
                            {savingImage ? (
                                <ActivityIndicator size="small" color="#fff" />
                            ) : (
                                <MaterialIcons name="save-alt" size={24} color="#fff" />
                            )}
                            </TouchableOpacity>
                        </View>
                    )}

                </View>
            </GestureHandlerRootView>
        </Modal>
    );
}