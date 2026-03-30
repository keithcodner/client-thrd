import { Platform } from 'react-native';

export const API_BASE_URL = "http://192.168.2.11:8000/api";

export const STRIPE_PUBLISHABLE_KEY = "pk_test_51T62MTQ4bltyPTuHrpxCkJ105JCl1bKbAKddaZcneHfmA0ddSa5b7l8Tizl3J1DMQcGbnsCWblaM6fWz98tVmDjR00HSlEOJSV";

// Platform-specific WebSocket configuration
// Web: Uses localhost because browser can't access network IPs
// Mobile: Uses network IP to connect from physical devices
const getWebSocketHost = () => {
  if (Platform.OS === 'web') {
    return process.env.EXPO_PUBLIC_PUSHER_HOST_WEB || 'localhost';
  }
  return process.env.EXPO_PUBLIC_PUSHER_HOST || '192.168.2.11';
};

const getApiUrl = () => {
  if (Platform.OS === 'web') {
    return process.env.EXPO_PUBLIC_API_URL_WEB || 'http://localhost:8000';
  }
  return process.env.EXPO_PUBLIC_API_URL || 'http://192.168.2.11:8000';
};

export const PUSHER_CONFIG = {
  key: process.env.EXPO_PUBLIC_PUSHER_KEY || 'thrd-app-key',
  cluster: process.env.EXPO_PUBLIC_PUSHER_CLUSTER || 'mt1',
  apiUrl: getApiUrl(),
  wsHost: getWebSocketHost(),
  wsPort: parseInt(process.env.EXPO_PUBLIC_PUSHER_PORT || '6001'),
  wssPort: parseInt(process.env.EXPO_PUBLIC_PUSHER_PORT || '6001'),
  forceTLS: false,
  enabledTransports: ['ws', 'wss'],
};