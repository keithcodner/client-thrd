import React from "react";
import { View } from "react-native";

type ProgressDotsProps = {
  total: number;
  currentIndex: number;
  accentColor: string;
  borderColor: string;
};

export const ProgressDots: React.FC<ProgressDotsProps> = ({
  total,
  currentIndex,
  accentColor,
  borderColor,
}) => {
  return (
    <View className="flex-row mb-8">
      {Array.from({ length: total }).map((_, index) => (
        <View
          key={index}
          style={{
            backgroundColor: index === currentIndex ? accentColor : borderColor,
            width: index === currentIndex ? 32 : 8,
          }}
          className="h-2 rounded-full mx-1"
        />
      ))}
    </View>
  );
};
