import { useState, useEffect, useCallback, useRef } from 'react';
import { getUnreadMessageCounts } from '@/services/chatService';
import { AppState, AppStateStatus } from 'react-native';

interface UnreadMessageData {
  total_unread: number;
  unread_by_conversation: Record<string, number>;
}

/**
 * Hook to poll for unread message counts
 * 
 * Polls the backend API at regular intervals to get unread message counts.
 * Automatically pauses when app is in background and resumes in foreground.
 * 
 * @param pollInterval - Polling interval in milliseconds (default: 30000 = 30 seconds)
 * @param enabled - Whether polling is enabled (default: true)
 * @returns Object containing unread message data and refresh function
 */
export const useUnreadMessages = (pollInterval: number = 30000, enabled: boolean = true) => {
  const [unreadData, setUnreadData] = useState<UnreadMessageData>({
    total_unread: 0,
    unread_by_conversation: {},
  });
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<Error | null>(null);
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null);
  const appStateRef = useRef<AppStateStatus>(AppState.currentState);

  const fetchUnreadCounts = useCallback(async () => {
    try {
      const data = await getUnreadMessageCounts();
      setUnreadData({
        total_unread: data.total_unread || 0,
        unread_by_conversation: data.unread_by_conversation || {},
      });
      setError(null);
    } catch (err) {
      console.error('Error fetching unread message counts:', err);
      setError(err as Error);
    } finally {
      setIsLoading(false);
    }
  }, []);

  // Handle app state changes (pause polling when app is in background)
  useEffect(() => {
    const handleAppStateChange = (nextAppState: AppStateStatus) => {
      const wasInBackground = appStateRef.current.match(/inactive|background/);
      const isNowActive = nextAppState === 'active';

      // If app just became active, fetch unread counts immediately
      if (wasInBackground && isNowActive) {
        fetchUnreadCounts();
      }

      appStateRef.current = nextAppState;
    };

    const subscription = AppState.addEventListener('change', handleAppStateChange);

    return () => {
      subscription?.remove();
    };
  }, [fetchUnreadCounts]);

  // Set up polling
  useEffect(() => {
    if (!enabled) {
      // Clear interval if polling is disabled
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
        intervalRef.current = null;
      }
      return;
    }

    // Fetch immediately on mount
    fetchUnreadCounts();

    // Set up polling interval
    intervalRef.current = setInterval(() => {
      // Only poll if app is active
      if (AppState.currentState === 'active') {
        fetchUnreadCounts();
      }
    }, pollInterval);

    // Cleanup interval on unmount
    return () => {
      if (intervalRef.current) {
        clearInterval(intervalRef.current);
        intervalRef.current = null;
      }
    };
  }, [enabled, pollInterval, fetchUnreadCounts]);

  return {
    totalUnread: unreadData.total_unread,
    unreadByConversation: unreadData.unread_by_conversation,
    isLoading,
    error,
    refresh: fetchUnreadCounts,
  };
};
