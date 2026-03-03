import { View, ActivityIndicator, Text } from 'react-native';
import { StripeProvider } from '@stripe/stripe-react-native';
import { STRIPE_PUBLISHABLE_KEY } from '@/config/env';
import CreditPackageCard from '@/components/app/CreditPackageCard';
import { CreditPackage } from '@/components/app/CreditPackageCard';
import { useThemeColours } from '@/hooks/useThemeColours';
import { MaterialIcons } from '@expo/vector-icons';
import { useSession } from '@/context/AuthContext';
import { useCredits } from '@/hooks/useCredits';

// Define credit packages -- gradients will be applied in the component based on theme
const CREDIT_PACKAGES: Omit<CreditPackage, 'gradient'>[] = [
    {
        credits: 10,
        price: 2,
        popular: false,
    },
    {
        credits: 50,
        price: 10,
        popular: true,
    },
    {
        credits: 200,
        price: 30,
        popular: false,
    },
];

function CreditsContent() {
    const { user, isLoading, handlePurchase } = useCredits();
    const colors = useThemeColours();

    return (
        <View className="flex-1 bg-white dark:bg-gray-900 p-4 mt-8">
            <View className="items-center mb-8">
                <MaterialIcons
                name="stars"
                size={60}
                color={colors.primary}
                />
                <Text className="text-4xl font-bold text-gray-800 dark:text-white my-2">
                {user?.credits || 0}
                </Text>
                <Text className="text-lg text-gray-600 dark:text-gray-300">
                Available Credits
                </Text>
            </View>
            <Text className="text-xl font-bold dark:text-white text-gray-800 mb-4">
                Get More Credits
            </Text>

            <View>
                {CREDIT_PACKAGES.map((pkg) => {
                    // Create a complete package with theme-aware gradient
                    const completePackage: CreditPackage = {
                        ...pkg,
                        gradient: [colors.card, colors.surface] as readonly [string, string, ...string[]]
                    };
                    return (
                        <CreditPackageCard
                            key={completePackage.credits}
                            package={completePackage}
                            onPress={handlePurchase}
                            disabled={isLoading}
                        />
                    );
                })}
            </View>
            {isLoading && (
                <View className="absolute inset-0 flex justify-center items-center bg-black/30 dark:bg-black/50">
                    <ActivityIndicator size="large" color={colors.primary} />
                </View>
            )}
        </View>
    );
}

// Main Credits screen component
export default function Credits() {
  return (
    <StripeProvider publishableKey={STRIPE_PUBLISHABLE_KEY}>
        <CreditsContent />
    </StripeProvider>
    );
}