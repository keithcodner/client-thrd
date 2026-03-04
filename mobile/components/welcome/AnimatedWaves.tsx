import React from "react";
import { View, Animated, Dimensions } from "react-native";
import Svg, { Path } from "react-native-svg";

const { width } = Dimensions.get("window");
const AnimatedPath = Animated.createAnimatedComponent(Path);

type AnimatedWavesProps = {
  wave1Y1: Animated.Value;
  wave1Y2: Animated.Value;
  wave2Y1: Animated.Value;
  wave2Y2: Animated.Value;
  accentColor: string;
  borderColor: string;
};

export const AnimatedWaves: React.FC<AnimatedWavesProps> = ({
  wave1Y1,
  wave1Y2,
  wave2Y1,
  wave2Y2,
  accentColor,
  borderColor,
}) => {
  return (
    <View className="items-center mt-20">
      <Svg height="200" width={width - 64}>
        <AnimatedPath
          d={wave1Y1.interpolate({
            inputRange: [0, 200],
            outputRange: [
              `M 0 100 Q ${width / 4} 0, ${width / 2} 100 T ${width - 64} 100`,
              `M 0 100 Q ${width / 4} 200, ${width / 2} 100 T ${width - 64} 100`
            ]
          }) as any}
          stroke={accentColor}
          strokeWidth="3"
          fill="none"
        />
        <AnimatedPath
          d={wave2Y1.interpolate({
            inputRange: [0, 200],
            outputRange: [
              `M 0 120 Q ${width / 4} 0, ${width / 2} 120 T ${width - 64} 120`,
              `M 0 120 Q ${width / 4} 200, ${width / 2} 120 T ${width - 64} 120`
            ]
          }) as any}
          stroke={borderColor}
          strokeWidth="2"
          fill="none"
          opacity="0.5"
        />
      </Svg>
    </View>
  );
};
