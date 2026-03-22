/**
 * Notification Service
 * 
 * Handles all notification-related API calls and real-time WebSocket notifications.
 * 
 * **Features:**
 * - Fetch notifications with filtering by type
 * - Mark notifications as read
 * - Get unread notification counts
 * - Real-time WebSocket notifications
 * - Phone vibration on new notification
 * 
 * **Notification Types:**
 * - circle_request: Invitation to join a circle
 * - message: New message notification
 * - calendar: Calendar event notification
 * - system: System announcements
 */

import axiosInstance from '@/config/axiosConfig';
import * as Haptics from 'expo-haptics';
import { Platform } from 'react-native';
import Pusher from 'pusher-js/react-native';
import * as SecureStore from 'expo-secure-store';

// Notification types enum
export enum NotificationType {
  CIRCLE_REQUEST = 'circle_request',
  MESSAGE = 'message',
  CALENDAR = 'calendar',
  SYSTEM = 'system',
}

// Notification status enum
export enum NotificationStatus {
  UNREAD = 'unread',
  READ = 'read',
}

export interface Notification {
  id: number;
  user_id: number;
  from_id: number | null;
  type: NotificationType;
  title: string;
  comment: string;
  status: NotificationStatus;
  color_status: string;
  created_at: string;
  updated_at: string;
  from_user?: {
    id: number;
    name: string;
    firstname: string;
    username: string;
  };
  circle_request?: {
    id: number;
    circle_id: number;
    circle_name: string;
    requester_id: number;
    requester_name: string;
    status: string;
    created_at: string;
  };
}

/**
 * Get notifications for the current user
 * Supports filtering by type, status, and pagination
 */
export const getNotifications = async (params?: {
  type?: NotificationType;
  status?: NotificationStatus;
  limit?: number;
  offset?: number;
}): Promise<{ notifications: Notification[]; hasMore: boolean }> => {
  try {
    const response = await axiosInstance.post('/notifications', params || {});
    return {
      notifications: response.data.notifications,
      hasMore: response.data.hasMore,
    };
  } catch (error) {
    console.error('Error fetching notifications:', error);
    throw error;
  }
};

/**
 * Get a single notification by ID with full details
 */
export const getNotificationById = async (
  notificationId: number
): Promise<Notification> => {
  try {
    const response = await axiosInstance.post('/notification', {
      notification_id: notificationId,
    });
    return response.data.notification;
  } catch (error) {
    console.error('Error fetching notification:', error);
    throw error;
  }
};

/**
 * Mark a notification as read
 */
export const markNotificationAsRead = async (
  notificationId: number
): Promise<void> => {
  try {
    await axiosInstance.post('/notification/mark-read', {
      notification_id: notificationId,
    });
  } catch (error) {
    console.error('Error marking notification as read:', error);
    throw error;
  }
};

/**
 * Get unread notification count
 */
export const getUnreadCount = async (
  type?: NotificationType
): Promise<number> => {
  try {
    const params = type ? { type } : {};
    const response = await axiosInstance.get('/notifications/unread-count', {
      params,
    });
    return response.data.unread_count;
  } catch (error) {
    console.error('Error fetching unread count:', error);
    throw error;
  }
};

/**
 * Vibrate phone for notification
 * Uses different patterns based on notification type
 */
export const vibrateForNotification = async (type?: NotificationType) => {
  try {
    if (Platform.OS === 'ios') {
      // iOS
      await Haptics.notificationAsync(
        Haptics.NotificationFeedbackType.Success
      );
    } else {
      // Android - custom vibration patterns
      if (type === NotificationType.CIRCLE_REQUEST) {
        // Double vibration for invites
        await Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
        setTimeout(async () => {
          await Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
        }, 100);
      } else {
        // Single vibration for other notifications
        await Haptics.notificationAsync(
          Haptics.NotificationFeedbackType.Success
        );
      }
    }
  } catch (error) {
    console.error('Error vibrating phone:', error);
  }
};

/**
 * WebSocket Notification Manager
 * Handles real-time notifications using Pusher
 */
class NotificationWebSocketManager {
  private pusher: Pusher | null = null;
  private channel: any = null;
  private listeners: ((notification: Notification) => void)[] = [];

  /**
   * Initialize WebSocket connection
   */
  async initialize() {
    try {
      // Get auth token
      const token = await SecureStore.getItemAsync('authToken');
      if (!token) {
        console.warn('No auth token found, cannot initialize notifications');
        return;
      }

      // Get user ID
      const userJson = await SecureStore.getItemAsync('user');
      if (!userJson) {
        console.warn('No user found, cannot initialize notifications');
        return;
      }
      const user = JSON.parse(userJson);

      // Initialize Pusher (using Soketi config from backend)
      this.pusher = new Pusher('app-key', {
        wsHost: 'localhost',
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        cluster: 'mt1',
      });

      // Subscribe to user's private notification channel
      this.channel = this.pusher.subscribe(`private-notifications.${user.id}`);

      // Listen for new notifications
      this.channel.bind('notification.new', (data: Notification) => {
        console.log('📨 New notification received:', data);
        
        // Vibrate phone
        vibrateForNotification(data.type);

        // Notify all listeners
        this.listeners.forEach((listener) => listener(data));
      });

      console.log('✅ Notification WebSocket initialized');
    } catch (error) {
      console.error('Error initializing notification WebSocket:', error);
    }
  }

  /**
   * Subscribe to new notifications
   */
  onNotification(callback: (notification: Notification) => void) {
    this.listeners.push(callback);
    
    // Return unsubscribe function
    return () => {
      this.listeners = this.listeners.filter((l) => l !== callback);
    };
  }

  /**
   * Disconnect WebSocket
   */
  disconnect() {
    if (this.channel) {
      this.channel.unbind_all();
      this.channel.unsubscribe();
    }
    if (this.pusher) {
      this.pusher.disconnect();
    }
    this.listeners = [];
    console.log('🔌 Notification WebSocket disconnected');
  }
}

// Singleton instance
export const notificationWebSocket = new NotificationWebSocketManager();
