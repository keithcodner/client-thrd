import React, { useState, useRef } from "react";
import { Text, View, Image, ScrollView, Alert, Dimensions} from "react-native";

import { useSession } from "@/context/AuthContext";
import { useThemeColours } from "@/hooks/useThemeColours";
import { Stack }from "expo-router";

import Button from "@/components/core/Button";
import { MaterialIcons } from "@expo/vector-icons";
import PagerView from "react-native-pager-view";
import * as ImagePicker from "expo-image-picker";
import * as FileSystem from "expo-file-system/legacy";
import * as MediaLibrary from "expo-media-library";

import axios from "axios";
import axiosInstance from "@/config/axiosConfig";

import {
  ImageSelector,
  ImageComparison,
  FullscreenViewer,
  ImageActions,
  ProcessingOverlay,
  saveImageToGallery,
  requestMediaPermission
} from '@/components/images';


const { width } = Dimensions.get('window');

export default function Restore() {
    const { user, updateUser } = useSession();
    const colours = useThemeColours();
    const [selectedImage, setSelectedImage] = useState<string | null>(null);
    const [generatedImage, setGeneratedImage] = useState<string | null>(null);
    const [isLoading, setIsLoading] = useState(false);
    const [hasPermission, setHasPermission] = useState<boolean | null>(null);
    const [fullscreenVisible, setFullscreenVisible] = useState(false);
    const [currentFullscreenIndex, setCurrentFullscreenIndex] = useState(0);
    const [mediaPermission, setMediaPermission] = useState(false);
    const [savingImage, setSavingImage] = useState(false);

    const handleImageSelected =  () => {
        setGeneratedImage(null);
    };

    // Handle image upload and restore
    const handleUpload = async () => {
        if (!selectedImage) {
        Alert.alert('Error', 'Please select an image');
        return;
        }

        setIsLoading(true);

        // Create form data with proper typing for React Native
        const formData = new FormData();
        // @ts-ignore -- React Native's FormData implementation accepts this object structure
        formData.append('image', {
            uri: selectedImage,
            type: 'image/jpeg',
            name: 'upload.jpg',
        });

        //formData.append('aspectRatio', selectedRatio);

        try {
            const response = await axiosInstance.post('/image/restore', formData, {
                headers: {
                'Content-Type': 'multipart/form-data',
                },
            });

            if (response.status === 200) {
                Alert.alert('Success', 'Image generated successfully!');
                setGeneratedImage(response.data.transformed_url);

                if(user && response.data.credits){
                    const updatedUser = {
                        ...user,
                        credits: response.data.credits,
                    }
                    //Alert.alert(JSON.stringify(updatedUser));
                    await updateUser(updatedUser);
                }
            }
        } catch (error) {
            console.error('Unexpected error:', error);
            if (axios.isAxiosError(error)) {
                Alert.alert('Error', error.response?.data?.message || 'An error occurred while generating the image.');
            } else {
                console.error('Unexpected error:', error);
                Alert.alert('Error', 'Failed to restore the image. Please try again.');
            }
        } finally {
            setIsLoading(false);
        }
    };

    const handleSaveImage = async () => {
        if (!generatedImage) return;

        try {
            setSavingImage(true);

            // Check for permission
            if (!mediaPermission) {
            const hasPermission = await requestMediaPermission();
            if (!hasPermission) return;
            setMediaPermission(true);
            }

            // Save the image
            await saveImageToGallery(
                generatedImage,
                'restore-fill-image.jpg',
                'AI Generated Images'
            );
        } catch (error) {
            Alert.alert('Error', 'Failed to save image. Please try again.');
        } finally {
            setSavingImage(false);
        }
    };

    return(
        <>
            {/* Display the header */}
            <Stack.Screen
                options={{
                    title: 'Restore Image',
                    headerTintColor: colours.text,
                    headerStyle: { backgroundColor: colours.background },
                }}
            />
                {/* Display the main content */}
                <ScrollView className="flex-1 bg-white dark:bg-gray-900">

                {/* Display the image selection area */}
                <View className="flex-1 items-center justify-center p-4">
                    <ImageSelector
                        selectedImage={selectedImage}
                        setSelectedImage={setSelectedImage}
                        onImageSelected={handleImageSelected}
                        placeholder="Select an image for generative fill"
                    />
                    {/* Display the image selection area */}
                    {selectedImage &&  (
                        <View className="w-full">
                            
                                {generatedImage && (
                                    <ImageComparison
                                        originalImage={selectedImage}
                                        processedImage={generatedImage}
                                        processedLabel="AI Generated"
                                        onFullscreenRequest={(index) => {
                                            setCurrentFullscreenIndex(index);
                                            setFullscreenVisible(true);
                                        }}
                                    />
                                )}

                                {/* Saving State */}
                                {/* Saving State */}
                                <Button
                                    onPress={handleUpload}
                                    className="w-full mt-2"
                                    disabled={isLoading}
                                    loading={isLoading}
                                >
                                    <View className="flex-row items-center justify-center">
                                        <MaterialIcons name="auto-fix-high" size={20} color="#fff" style={{ marginRight: 8 }} />
                                        <Text className="text-white text-center font-medium">
                                            {isLoading ? 'Generating Fill...' : 'Generate Fill'}
                                        </Text>
                                    </View>
                                </Button>
                                {/* <ImageActions
                                    onSave={handleUpload}
                                    savingImage={savingImage}
                                /> */}
                        </View>
                            
                    )}

                    <ProcessingOverlay visible={isLoading} message="Applying AI magic..." />
                </View>
            </ScrollView>

            {/* Fullscreen image viewer */}
            <FullscreenViewer
                visible={fullscreenVisible}
                onClose={() => setFullscreenVisible(false)}
                originalImage={selectedImage}
                processedImage={generatedImage}
                initialPage={currentFullscreenIndex}
                processedLabel="AI Generated"
                onSave={handleSaveImage}
                savingImage={savingImage}
            />
        </>
    );
}