export const API_BASE_URL = "http://10.0.0.12:8000/api";

export const STRIPE_PUBLISHABLE_KEY = "pk_test_51T62MTQ4bltyPTuHrpxCkJ105JCl1bKbAKddaZcneHfmA0ddSa5b7l8Tizl3J1DMQcGbnsCWblaM6fWz98tVmDjR00HSlEOJSV";

export const PUSHER_CONFIG = {
  key: process.env.EXPO_PUBLIC_PUSHER_KEY || 'thrd-app-key',
  cluster: process.env.EXPO_PUBLIC_PUSHER_CLUSTER || 'mt1',
  apiUrl: process.env.EXPO_PUBLIC_API_URL || 'http://10.0.0.12:8000',
  wsHost: process.env.EXPO_PUBLIC_PUSHER_HOST || '10.0.0.12',
  wsPort: parseInt(process.env.EXPO_PUBLIC_PUSHER_PORT || '6001'),
  wssPort: parseInt(process.env.EXPO_PUBLIC_PUSHER_PORT || '6001'),
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],
};