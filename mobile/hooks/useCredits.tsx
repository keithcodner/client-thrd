import { useState, useCallback } from 'react';
import { Alert } from 'react-native';
import { useStripe } from '@stripe/stripe-react-native';
import { useSession } from '@/context/AuthContext';
import paymentService from '../services/paymentService';

// Custom hook to manage credits and payments
export const useCredits = () => {
  const { user, updateUser } = useSession();
  const [isLoading, setIsLoading] = useState(false);
  const [selectedPackage, setSelectedPackage] = useState<null | {credits: number, price: number}>(null);
  const { initPaymentSheet, presentPaymentSheet } = useStripe();


    // Initialize the payment sheet with parameters from the backend
    const initializePaymentSheet = useCallback(async (credits: number, price: number) => {
        try {
            const { paymentIntent, ephemeralKey, customer } = await paymentService.fetchPaymentSheetParams(credits, price);

            const { error } = await initPaymentSheet({
                merchantDisplayName: 'Image Processing App',
                customerId: customer,
                customerEphemeralKeySecret: ephemeralKey,
                paymentIntentClientSecret: paymentIntent,
                allowsDelayedPaymentMethods: false,
                style: 'automatic',
                returnURL: 'image-processor://stripe-redirect', // Deep link for handling post-payment success
            });

            if (error) {
                console.error('Error initializing payment sheet:', error);
                Alert.alert('Error', error.message);
                return false;
            }

            return true;
        } catch (error) {
            // Error handling here
            console.log('Error initializing payment sheet:', error);
            Alert.alert('Error', 'Failed to initialize payment. Please try again.');
            return false;
        }
    }, [initPaymentSheet]);

    // Handle the purchase process
    const handlePurchase = useCallback(async (credits: number, price: number) => {
        if (isLoading) return;

        try {
            setIsLoading(true);
            setSelectedPackage({ credits, price });

            const initialized = await initializePaymentSheet(credits, price);
            if (!initialized) {
                // Handle failed payment sheet initialization here
                setIsLoading(false);
                return;
            }

            // Present the payment sheet to the user
            const { error } = await presentPaymentSheet();

            if (error) {
                if (error.code === 'Canceled') {
                    console.log('User canceled the payment');
                } else {
                    Alert.alert('Error', error.message);
                }
                setSelectedPackage(null);
                return;
            }

            // Payment successful -- process with backend
            const result = await paymentService.handlePaymentSuccess();

            if (result.success) {
                try {
                    if (user) {
                        // Create a new user object with updated credits
                        const updatedUser = {
                            ...user,
                            credits: result.credits
                        };

                        // Update the user in context
                        await updateUser(updatedUser);

                        // Show success message
                        Alert.alert('Success', `Successfully added ${result.credits_added} credits to your account!`);
                    } else {
                        // If user is null, fetch the latest user data
                        const userData = await paymentService.fetchUserData();
                        if (userData) {
                            await updateUser(userData);
                            Alert.alert('Success', `Successfully added ${result.credits_added} credits to your account!`);
                        }
                    }
                } catch (error) {
                    console.log('Error updating user credits:', error);
                    Alert.alert('Error', 'Payment succeeded but failed to update credits. Please contact support.');
                }
            } else {
                Alert.alert('Error', result.error || 'Payment succeeded but failed to process. Please contact support.');
            }

        } catch (error) {
            // Error handling here
            console.log('Error during purchase process:', error);
            Alert.alert('Error', 'An unexpected error occurred during the purchase process. Please try again.');
        } finally {
            // Cleanup or loading reset here
            setIsLoading(false);
            setSelectedPackage(null);
        }
    }, [ isLoading, initializePaymentSheet, presentPaymentSheet, user, updateUser]);

    return {
        user,
        isLoading,
        selectedPackage,
        handlePurchase
    };

};

