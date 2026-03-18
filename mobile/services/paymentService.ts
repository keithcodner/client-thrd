import axiosInstance from "../config/axiosConfig";

interface PaymentSheetParams {
  paymentIntent: string;
  ephemeralKey: string;
  customer: string;
}

export interface PaymentResult {
  success: boolean;
  credits?: number;
  credits_added?: number;
  error?: string;
}

class PaymentService {
    private paymentIntentId: string | null = '';

    /**
     * Fetch payment sheet parameters from the backend
     */
    async fetchPaymentSheetParams(credits: number, price: number): Promise<PaymentSheetParams> {
        try {
            const response = await axiosInstance.post('/payment/create-payment-intent', {
                credits,
                price,
            }, {
                headers: {
                    'Stripe-Version': '2022-11-15', // Ensure this matches the Stripe API version used in your backend
                }
            });

            // Store payment intent ID
            if (response.data.paymentIntentId) {
                this.paymentIntentId = response.data.paymentIntentId;
            } else if (response.data.paymentIntent) {
                // Extract ID from client secret if needed
                const clientSecret = response.data.paymentIntent;
                const parts = clientSecret.split('_secret_');
                if(parts.length > 0) {
                    this.paymentIntentId = parts[0];
                }
            }

            return {
                paymentIntent: response.data.paymentIntent,
                ephemeralKey: response.data.ephemeralKey,
                customer: response.data.customer,
            };
        } catch (error) {
            // Error handling would go here
            console.log('Error fetching payment sheet params:', error);
            throw new Error('Failed to fetch payment sheet parameters');
            
        }
    }

    // Handle post-payment success logic by notifying the backend
    getPaymentIntentId(): string | null {
        return this.paymentIntentId;
    }

    // Call this after confirming payment on the client to update backend and credit user
    async handlePaymentSuccess(): Promise<PaymentResult> {
        try {
            const response = await axiosInstance.post('/payment/handle-payment-success', {
                payment_intent: this.paymentIntentId,
            });

            if (response.data.success) {
                return {
                    success: true,
                    credits: response.data.credits,
                    credits_added: response.data.credits_added,
                };
            } else {
                return {
                    success: false,
                    error: 'Failed to process payment'
                };
            }

        } catch (error) {
            console.log('Error handling payment success:', error);
            return {
                success: false,
                error: 'Failed to process payment success'
            };
        }
    }

    async fetchUserData(){
        try {
            const userResponse = await axiosInstance.get('/user');
            return userResponse.data;
            
        } catch (error) {
            console.log('Error fetching user data:', error);
            throw new Error('Failed to fetch user data');
        }
    }
}


export default new PaymentService();