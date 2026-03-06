import React from "react";
import { Text, View, TouchableOpacity } from "react-native";
import { User, Camera } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

type PhotoUploadProps = {
  photo: string | null;
  onPhotoSelect: () => void;
  onSkip: () => void;
};

export const PhotoUpload: React.FC<PhotoUploadProps> = ({
  photo,
  onPhotoSelect,
  onSkip,
}) => {
  const colors = useThemeColours();

  return (
    <View className="w-full px-8 items-center">
      <TouchableOpacity
        onPress={onPhotoSelect}
        style={{
          width: 120,
          height: 120,
          borderRadius: 60,
          backgroundColor: colors.card,
          alignItems: 'center',
          justifyContent: 'center',
          marginBottom: 32,
          position: 'relative',
        }}
      >
        <User size={48} color={colors.secondaryText} strokeWidth={1.5} />
        <View
          style={{
            position: 'absolute',
            bottom: 0,
            right: 0,
            backgroundColor: colors.text,
            borderRadius: 20,
            width: 40,
            height: 40,
            alignItems: 'center',
            justifyContent: 'center',
          }}
        >
          <Camera size={20} color={colors.background} strokeWidth={2} />
        </View>
      </TouchableOpacity>
    </View>
  );
};
