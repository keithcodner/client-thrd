import React, { useEffect, useState, useRef } from 'react';
import {
  View,
  Text,
  ActivityIndicator,
  TouchableOpacity,
  Alert,
  Image,
  Dimensions,
  Platform,
  Share
} from 'react-native';
import { useLocalSearchParams, Stack, router } from 'expo-router';
import { MaterialIcons } from '@expo/vector-icons';
import { useThemeColours } from '@/hooks/useThemeColours';
import axiosInstance from '@/config/axiosConfig';
import * as FileSystem from 'expo-file-system/legacy';
import * as MediaLibrary from 'expo-media-library';
import PagerView from 'react-native-pager-view';
import FullScreenViewer from '@/components/images/FullScreenViewer';

const { width, height } = Dimensions.get('window');

interface Operation {
  id: number;
  user_id: number;
  original_image: string;
  generated_image: string;
  operation_type: string;
  operation_metadata: any;
  created_at: string;
  updated_at: string;
  credits_used: number;
}

const formatOperationType = (type: string): string => {
  return type.split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

export default function OperationDetail() {
    const { id } = useLocalSearchParams();
    const colors = useThemeColours();
    const [operation, setOperation] = useState<Operation | null>(null);
    const [loading, setLoading] = useState(true);
    const [imageViewerVisible, setImageViewerVisible] = useState(false);
    const [currentImageIndex, setCurrentImageIndex] = useState(0);
    const [savingImage, setSavingImage] = useState(false);
    const [mediaPermission, setMediaPermission] = useState(false);

    const pagerRef = useRef<PagerView>(null);

    useEffect(() => {
        // Check for media library permissions
        (async () => {
            const { status } = await MediaLibrary.requestPermissionsAsync();
            setMediaPermission(status === 'granted');
        })();

        // Fetch operation details
        const fetchOperation = async () => {
            try {
                const response = await axiosInstance.get(`/image/operation/${id}`);
                setOperation(response.data.operation);
            } catch (error) {
                console.error('Failed to fetch operation:', error);
                Alert.alert('Error', 'Failed to load operation details');
            } finally {
                setLoading(false);
            }
        };

        fetchOperation();
    }, [id]);

    const handleDeleteOperation = () => {
        Alert.alert(
            'Delete Operation',
            'Are you sure you want to delete this operation? This action cannot be undone.',
            [
                { text: 'Cancel', style: 'cancel' },
                {
                    text: 'Delete',
                    style: 'destructive',
                    onPress: async () => {
                        try {
                            setLoading(true);
                            await axiosInstance.delete(`/image/operation/${id}`);
                            Alert.alert('Success', 'Operation deleted successfully');
                            router.back();
                        } catch (error) {
                            console.error('Failed to delete operation:', error);
                            Alert.alert('Error', 'Failed to delete operation');
                            setLoading(false);
                        }
                    }
                }
            ]
        );
    };

    const saveImage = async () => {
        if (!operation) return;

        if (!mediaPermission) {
            const { status } = await MediaLibrary.requestPermissionsAsync();
            if (status !== 'granted') {
            Alert.alert('Permission Required', 'Please grant media library permissions to save images');
            return;
            }
            setMediaPermission(true);
        }

        // continue with save logic
        try {
            setSavingImage(true);

            // Download the image
            const fileUri = FileSystem.documentDirectory + 'generated-image.jpg';
            const downloadResult = await FileSystem.downloadAsync(
                operation.generated_image,
                fileUri
            );

            if (downloadResult.status === 200) {
                const asset = await MediaLibrary.createAssetAsync(fileUri);
                await MediaLibrary.createAlbumAsync('AI Generated Images', asset, false);
                Alert.alert('Success', 'Image saved to your gallery');
            } else {
                Alert.alert('Error', 'Failed to download image');
            }
        } catch (error) {
            // handle error
            console.error('Failed to save image:', error);
            Alert.alert('Error', 'Failed to save image');
        } finally {
            setSavingImage(false);
        }
    };

    const openImageViewer = (index: number) => {
        setCurrentImageIndex(index);
        setImageViewerVisible(true);

        // Set timeout to allow modal to open before changing page
        setTimeout(() => {
            if (pagerRef.current) {
            pagerRef.current.setPage(index);
            }
        }, 100);
    };

    const getOperationIcon = (type: string) => {
        switch (type) {
        case 'generative_fill':
            return 'auto-fix-high';
        case 'restore':
            return 'restore';
        case 'recolor':
            return 'palette';
        case 'remove_object':
            return 'content-cut';
        default:
            return 'image';
        }
    };

    if (loading) {
        return (
            <View className="flex-1" style={{ backgroundColor: colors.background }}>
                <Stack.Screen
                    options={{
                    title: 'Operation Details',
                    headerTintColor: colors.text,
                    headerStyle: { backgroundColor: colors.background }
                    }}
                />
                <View className="flex-1 justify-center items-center">
                    <ActivityIndicator size="large" color={colors.primary} />
                </View>
            </View>
        );
    }

    if (!operation) {
        return (
            <View className="flex-1" style={{ backgroundColor: colors.background }}>
                <Stack.Screen
                    options={{
                    title: 'Operation Details',
                    headerTintColor: colors.text,
                    headerStyle: { backgroundColor: colors.background }
                    }}
                />

                {/* Operation not found */}
                <View className="flex-1 justify-center items-center p-5">
                    <MaterialIcons name="error-outline" size={64} color={colors.error} />
                    <Text className="text-lg mt-4 mb-6 text-center" style={{ color: colors.text }}>
                        Operation not found
                    </Text>
                </View>

                {/* Go Back Button */}
                <TouchableOpacity
                className="px-5 py-2.5 rounded-lg"
                style={{ backgroundColor: colors.primary }}
                onPress={() => router.back()}
                >
                    <Text className="text-base font-semibold" style={{ color: colors.text }}>
                        Go Back
                    </Text>
                </TouchableOpacity>
            </View>
        );
    }

    return (
        // component JSX here
        <View className="flex-1" style={{ backgroundColor: colors.background }} >
            <Stack.Screen
                options={{
                title: formatOperationType(operation.operation_type),
                headerTintColor: colors.text,
                headerStyle: { backgroundColor: colors.background },
                
            }} />
            <View className="flex-1 p-4">
                <View className="rounded-xl p-4 mb-4 border" style={{ backgroundColor: colors.card, borderColor: colors.border }}>
                    <View className="flex-row items-center mb-3">
                        <MaterialIcons
                            name={getOperationIcon(operation.operation_type)}
                            size={28}
                            color={colors.primary}
                        />

                        {/* Operation Details */}
                        <View className="ml-3 flex-1">
                            <Text className="text-lg font-bold" style={{ color: colors.text }}>
                                {formatOperationType(operation.operation_type)}
                            </Text>
                            <Text className="text-sm mt-1" style={{ color: colors.secondaryText }}>
                                {formatDate(operation.created_at)}
                            </Text>
                        </View>
                    </View>

                    {/* Credits Used */}
                    <View className="flex-row items-center pt-3 border-t" style={{ borderTopColor: '#e5e7eb' }}>
                        <MaterialIcons name="stars" size={20} color={colors.primary} />
                        <Text className="text-base font-semibold ml-2" style={{ color: colors.text }}>
                            {operation.credits_used} credits used
                        </Text>
                    </View>
                </View>

                {/* Before & After */}
                <View className="mb-4">
                    <Text className="text-lg font-bold mb-3" style={{ color: colors.text }}>
                        Before & After
                    </Text>
                    <View className="flex-row justify-between">

                        {/* Original Image */}
                        <TouchableOpacity
                            className="w-[48%] rounded-xl overflow-hidden border"
                            style={{ backgroundColor: colors.card, borderColor: colors.border }}
                            onPress={() => openImageViewer(0)}
                            activeOpacity={0.9}
                        >
                            {/* content goes here */}
                            <Image
                            source={{ uri: operation.original_image }}
                            className="w-full h-[180px]"
                            resizeMode="cover"
                            />
                            <View className="absolute bottom-2 left-2 px-2 py-1 rounded" style={{ backgroundColor: colors.surface }}>
                                <Text className="text-xs font-semibold" style={{ color: colors.text }}>
                                    Original
                                </Text>
                            </View>
                        </TouchableOpacity>


                        {/* Generated Image */}
                        <TouchableOpacity
                        className="w-[48%] rounded-xl overflow-hidden border"
                        style={{ backgroundColor: colors.card, borderColor: colors.border }}
                        onPress={() => openImageViewer(1)}
                        activeOpacity={0.9}
                        >
                        <Image
                            source={{ uri: operation.generated_image }}
                            className="w-full h-[180px]"
                            resizeMode="cover"
                        />
                        <View className="absolute bottom-2 left-2 px-2 py-1 rounded" style={{ backgroundColor: colors.surface }}>
                                <Text className="text-xs font-semibold" style={{ color: colors.text }}>
                                    Generated
                                </Text>
                        </View>
                        </TouchableOpacity>
                    </View>
                </View>

                {/* Save Button */}
                <View className="flex-row justify-between mb-4">
                    <TouchableOpacity
                        className="flex-row items-center justify-center py-3 rounded-lg flex-[0.48]"
                        style={{ backgroundColor: colors.primary }}
                        onPress={saveImage}
                        disabled={savingImage}
                    >
                        {/* content goes here */}
                        {savingImage ? (
                            <ActivityIndicator size="small" color="#fff" />
                            ) : (
                            <>
                                <MaterialIcons name="save-alt" size={20} color="#fff" />
                                <Text className="text-white font-semibold ml-2">Save Image</Text>
                            </>
                        )}
                    </TouchableOpacity>
                </View>

                {/* Delete Button */}
                <TouchableOpacity
                className="flex-row items-center justify-center py-3 rounded-lg mt-auto"
                style={{ backgroundColor: colors.error }}
                onPress={handleDeleteOperation}
                >
                    <MaterialIcons name="delete" size={20} color="#fff" />
                    <Text className="text-white font-semibold ml-2">Delete Operation</Text>
                </TouchableOpacity>
            </View>

            {/* Full-screen image viewer */}
            <FullScreenViewer
            visible={imageViewerVisible}
            onClose={() => setImageViewerVisible(false)}
            originalImage={operation?.original_image || null}
            processedImage={operation?.generated_image || null}
            initialPage={currentImageIndex}
            processedLabel="Generated"
            onSave={saveImage}
            savingImage={savingImage}
            />
        </View>
    );
}