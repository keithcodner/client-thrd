import React from 'react';
import {
  View,
  Text,
  TouchableOpacity,
  ActivityIndicator
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useThemeColours } from '@/hooks/useThemeColours';

interface ImageActionsProps {
  onSave: () => void;
  savingImage: boolean;
}

export default function ImageActions({
  onSave,
  savingImage
}: ImageActionsProps) {
  const colors = useThemeColours();

  return (
    <View className="flex-row justify-between mb-6">
      <TouchableOpacity
        className="flex-1 bg-gray-100 dark:bg-gray-800 p-3 rounded-xl flex-row justify-center items-center"
        onPress={onSave}
        disabled={savingImage}
      >
        {savingImage ? (
            <ActivityIndicator size="small" color={colors.primary} />
        ) : (
            <>
                <MaterialIcons name="save-alt" size={20} color={colors.primary} />
                <Text className="ml-2 text-gray-800 dark:text-white">Save</Text>
            </>
        )}
      </TouchableOpacity>
    </View>
  );
}