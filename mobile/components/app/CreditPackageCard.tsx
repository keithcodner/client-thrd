import React from 'react';
import { View, Text } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import GradientCard from '../core/GradientCard';
import { useThemeColours } from '@/hooks/useThemeColours';

export interface CreditPackage {
  credits: number;
  price: number;
  gradient: readonly [string, string, ...string[]];
  popular: boolean;
}

interface CreditPackageCardProps {
  package: CreditPackage;
  onPress: (credits: number, price: number) => void;
  disabled?: boolean;
}

const CreditPackageCard: React.FC<CreditPackageCardProps> = ({
    package: pkg,
    onPress,
    disabled = false
}) => {
  const colors = useThemeColours();

  return(
    <GradientCard
    onPress={() => onPress(pkg.credits, pkg.price)}
    gradientColours={pkg.gradient}
    badgeVisible={pkg.popular}
    badgeText="Popular"
    disabled={disabled}
    >
    <View className="flex-row justify-between items-center">
        <View>
            <Text className="text-2xl font-bold dark:text-white text-gray-800 mb-1">
                {pkg.credits} Credits
            </Text>
            <Text className="text-gray-600 dark:text-gray-300 text-lg">
                ${pkg.price}
            </Text>
        </View>
        <MaterialIcons
            name="arrow-forward"
            size={24}
            color={colors.primary}
        />
    </View>
    </GradientCard>
  );
};

export default CreditPackageCard;