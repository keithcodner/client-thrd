import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  Pressable,
  ActivityIndicator,
  RefreshControl,
} from 'react-native';
import { useRouter } from 'expo-router';
import { X } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { NotificationItem } from '@/components/notifications/NotificationItem';
import {
  getNotifications,
  Notification,
  NotificationType,
  notificationWebSocket,
} from '@/services/notificationService';

type FilterType = 'ALL' | 'INVITES' | 'MESSAGES' | 'CIRCLES' | 'CALENDAR';

export default function NotificationsScreen() {
  const router = useRouter();
  const colors = useThemeColours();
  
  const [activeFilter, setActiveFilter] = useState<FilterType>('ALL');
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isRefreshing, setIsRefreshing] = useState(false);
  const [hasMore, setHasMore] = useState(false);
  const [offset, setOffset] = useState(0);

  const LIMIT = 30;

  // Map filter to API type
  const getNotificationType = (filter: FilterType): NotificationType | undefined => {
    switch (filter) {
      case 'INVITES':
        return NotificationType.CIRCLE_REQUEST;
      case 'MESSAGES':
        return NotificationType.MESSAGE;
      case 'CALENDAR':
        return NotificationType.CALENDAR;
      default:
        return undefined;
    }
  };

  // Fetch notifications
  const fetchNotifications = useCallback(async (refresh = false) => {
    try {
      const newOffset = refresh ? 0 : offset;
      if (refresh) setIsRefreshing(true);
      else if (newOffset === 0) setIsLoading(true);

      const type = getNotificationType(activeFilter);
      const result = await getNotifications({
        type,
        limit: LIMIT,
        offset: newOffset,
      });

      if (refresh) {
        setNotifications(result.notifications);
        setOffset(LIMIT);
      } else {
        setNotifications((prev) => 
          newOffset === 0 ? result.notifications : [...prev, ...result.notifications]
        );
        setOffset(newOffset + LIMIT);
      }

      setHasMore(result.hasMore);
    } catch (error) {
      console.error('Error fetching notifications:', error);
    } finally {
      setIsLoading(false);
      setIsRefreshing(false);
    }
  }, [activeFilter, offset]);

  // Initial load and filter change
  useEffect(() => {
    setOffset(0);
    setNotifications([]);
    fetchNotifications(true);
  }, [activeFilter]);

  // Subscribe to real-time notifications
  useEffect(() => {
    // Initialize WebSocket connection
    notificationWebSocket.initialize();

    // Subscribe to new notifications
    const unsubscribe = notificationWebSocket.onNotification((newNotification) => {
      console.log('📬 New notification arrived:', newNotification);
      
      // Add to list if it matches current filter
      const filterType = getNotificationType(activeFilter);
      if (!filterType || newNotification.type === filterType) {
        setNotifications((prev) => [newNotification, ...prev]);
      }
    });

    // Cleanup
    return () => {
      unsubscribe();
      notificationWebSocket.disconnect();
    };
  }, [activeFilter]);

  const handleFilterPress = (filter: FilterType) => {
    setActiveFilter(filter);
  };

  const handleNotificationPress = (notificationId: number) => {
    router.push(`/(app)/(notifications)/${notificationId}`);
  };

  const handleClose = () => {
    router.back();
  };

  const handleLoadMore = () => {
    if (!isLoading && hasMore) {
      fetchNotifications(false);
    }
  };

  const handleRefresh = () => {
    fetchNotifications(true);
  };

  const renderNotificationItem = ({ item }: { item: Notification }) => (
    <NotificationItem
      notification={item}
      onPress={() => handleNotificationPress(item.id)}
    />
  );

  const renderEmptyState = () => {
    if (isLoading) return null;
    
    return (
      <View style={styles.emptyState}>
        <Text style={[styles.emptyStateText, { color: colors.secondaryText }]}>
          No notifications yet
        </Text>
      </View>
    );
  };

  const renderFooter = () => {
    if (!isLoading || offset === 0) return null;
    
    return (
      <View style={styles.footer}>
        <ActivityIndicator size="small" color={colors.info} />
      </View>
    );
  };

  const filters: FilterType[] = ['ALL', 'INVITES', 'MESSAGES', 'CIRCLES', 'CALENDAR'];

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      {/* Header */}
      <View style={[styles.header, { borderBottomColor: colors.border }]}>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Activity</Text>
        <Pressable onPress={handleClose} style={styles.closeButton}>
          <X size={24} color={colors.text} />
        </Pressable>
      </View>

      {/* Filter Tabs */}
      <View style={[styles.filterContainer, { borderBottomColor: colors.border }]}>
        {filters.map((filter) => (
          <Pressable
            key={filter}
            onPress={() => handleFilterPress(filter)}
            style={[
              styles.filterButton,
              activeFilter === filter && [
                styles.filterButtonActive,
                { backgroundColor: colors.info },
              ],
            ]}
          >
            <Text
              style={[
                styles.filterButtonText,
                { color: activeFilter === filter ? '#fff' : colors.secondaryText },
                activeFilter === filter && styles.filterButtonTextActive,
              ]}
            >
              {filter}
            </Text>
          </Pressable>
        ))}
      </View>

      {/* Notifications List */}
      {isLoading && offset === 0 ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={colors.info} />
        </View>
      ) : (
        <FlatList
          data={notifications}
          renderItem={renderNotificationItem}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          ListEmptyComponent={renderEmptyState}
          ListFooterComponent={renderFooter}
          onEndReached={handleLoadMore}
          onEndReachedThreshold={0.5}
          refreshControl={
            <RefreshControl
              refreshing={isRefreshing}
              onRefresh={handleRefresh}
              tintColor={colors.info}
            />
          }
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 50,
    paddingBottom: 16,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: '300',
    fontFamily: 'Georgia',
  },
  closeButton: {
    padding: 4,
  },
  filterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingVertical: 12,
    gap: 8,
    borderBottomWidth: 1,
  },
  filterButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 20,
  },
  filterButtonActive: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
    elevation: 2,
  },
  filterButtonText: {
    fontSize: 13,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
  filterButtonTextActive: {
    color: '#fff',
  },
  listContent: {
    flexGrow: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyState: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 60,
  },
  emptyStateText: {
    fontSize: 16,
  },
  footer: {
    paddingVertical: 20,
    alignItems: 'center',
  },
});
