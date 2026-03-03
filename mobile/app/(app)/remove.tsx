import {
  View,
  Text,
  Alert,
  ScrollView,
  TouchableOpacity,
  Modal,
  TextInput,
  Dimensions
} from 'react-native';
import React, { useState } from 'react';
import Button from '../../components/core/Button';
import { useSession } from '../../context/AuthContext';
import axiosInstance from '../../config/axiosConfig';
import axios from 'axios';
import { MaterialIcons } from '@expo/vector-icons';
import { Stack } from 'expo-router';
import { useThemeColours } from '../../hooks/useThemeColours';
import ColorPicker from 'react-native-wheel-color-picker';


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

export default function Remove() {
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
    const [colorPickerVisible, setColorPickerVisible] = useState(false);
    const [currentColor, setCurrentColor] = useState<string>('#B85C44');
    const [selectedColor, setSelectedColor] = useState<string>('#B85C44');
    const [prompt, setPrompt] = useState<string>('');


    // Predefined colors for quick selection ─ more dimmed and less vibrant
    const predefinedColors = [
    '#B85C44', // Dimmed Red-Orange
    '#5A9367', // Dimmed Green
    '#4A5D9E', // Dimmed Blue
    '#C4B454', // Dimmed Yellow
    '#A06A8C', // Dimmed Magenta/Purple
    '#5A9E9E', // Dimmed Teal
    '#6A5A87', // Dimmed Purple
    '#B17A55', // Dimmed Orange
    '#3A3A3A', // Dark Gray (instead of Black)
    '#E5E5E5', // Light Gray (instead of White)
    ];

    const onColorChange = (color: string) => {
        setCurrentColor(color);
    };

    const confirmColorSelection = () => {
        setSelectedColor(currentColor);
        setColorPickerVisible(false);
    };

    const selectPredefinedColor = (color: string) => {
        setSelectedColor(color);
        setCurrentColor(color);
    };

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

        //formData.append('colour', selectedColor.replace('#', '')); // Send color without the '#' symbol
        formData.append('prompt', prompt);

        try {
            const response = await axiosInstance.post('/image/remove', formData, {
                headers: {
                'Content-Type': 'multipart/form-data',
                },
            });

            if (response.status === 200) {
                Alert.alert('Success', 'You have successfully removed the object from your image!');
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
                Alert.alert('Error', 'Failed to remove the object from the image. Please try again.');
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
                'remove-object-image.jpg',
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
                    title: 'Remove Object',
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
                        placeholder="Select an image for object removal"
                    />
                    {/* Display the image selection area */}
                    {selectedImage &&  (
                        <View className="w-full">
                            {/* Target part input field */}
                            <View className="flex-row items-center mb-3 mt-2">
                                <MaterialIcons name="edit" size={24} color={colours.primary} />
                                <Text className="text-lg font-bold ml-2 text-gray-800 dark:text-white">
                                    Specify Target Part
                                </Text>
                            </View>
                            <View className="w-full mb-4 bg-gray-100 dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                                <Text className="text-gray-700 dark:text-gray-300 mb-2">
                                    What part of the image do you want to remove?
                                </Text>
                                <View className="bg-white dark:bg-gray-700 rounded-lg border border-gray-300 dark:border-gray-600 p-3">
                                    <TextInput
                                    value={prompt}
                                    onChangeText={setPrompt}
                                    placeholder="e.g. shirt, car, background, etc."
                                    placeholderTextColor={colours.secondaryText}
                                    className="text-gray-800 dark:text-white"
                                    />
                                </View>
                                <Text className="text-gray-500 dark:text-gray-400 text-xs mt-2">
                                    Be specific about which part of the image you want to remove.
                                </Text>

                                <View className="flex-row items-center mb-3">
                                    <MaterialIcons name="palette" size={24} color={colours.primary} />
                                    <Text className="text-lg font-bold ml-2 text-gray-800 dark:text-white">
                                        Select Color
                                    </Text>
                                </View>

                            
                                {/* Color picker modal */}
                                <Modal
                                visible={colorPickerVisible}
                                transparent={true}
                                animationType="slide"
                                onRequestClose={() => setColorPickerVisible(false)}
                                >
                                    <View className="flex-1 justify-end">
                                        <View className="bg-white dark:bg-gray-900 p-4 rounded-t-3xl shadow-lg">
                                            <View className="flex-row justify-between items-center mb-4">
                                                <Text className="text-xl font-bold text-gray-800 dark:text-white">
                                                Choose a Color
                                                </Text>
                                                <TouchableOpacity onPress={() => setColorPickerVisible(false)}>
                                                    <MaterialIcons name="close" size={24} color={colours.text} />
                                                </TouchableOpacity>
                                            </View>

                                            {/* Color picker */}
                                            <View style={{ height: 300 }}>
                                                <ColorPicker
                                                    color={currentColor}
                                                    onColorChange={onColorChange}
                                                    thumbSize={30}
                                                    sliderSize={30}
                                                    noSnap={true}
                                                    row={false}
                                                />
                                            </View>
                                        </View>
                                    </View>
                                </Modal>

                                {/* end */}
                            </View>
                            
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
                                <Button
                                    onPress={handleUpload}
                                    className="w-full mt-2 mb-16"
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

