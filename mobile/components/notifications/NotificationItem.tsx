import React from 'react';
import { View, Text, StyleSheet, Pressable } from 'react-native';
import { MessageCircle } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { getInitials, getAvatarColor } from '@/utils/avatarUtils';
import { Notification } from '@/services/notificationService';
import { formatDistanceToNow } from 'date-fns';

interface NotificationItemProps {
  notification: Notification;
  onPress: () => void;
}

export const NotificationItem = ({
  notification,
  onPress,
}: NotificationItemProps) => {
  const colors = useThemeColours();

  // Get display name from notification
  const getDisplayName = () => {
    if (notification.from_user) {
      return (
        notification.from_user.firstname ||
        notification.from_user.name ||
        notification.from_user.username ||
        'Unknown'
      );
    }
    return 'Unknown';
  };

  // Format timestamp
  const getTimeAgo = () => {
    try {
      return formatDistanceToNow(new Date(notification.created_at), {
        addSuffix: false,
      });
    } catch {
      return '';
    }
  };

  const displayName = getDisplayName();
  const isUnread = notification.status === 'unread';

  return (
    <Pressable
      onPress={onPress}
      style={[
        styles.container,
        { borderBottomColor: colors.border },
        isUnread && { backgroundColor: colors.card },
      ]}
    >
      <View style={styles.content}>
        {/* Avatar */}
        <View style={[styles.avatar, { backgroundColor: getAvatarColor(displayName) }]}>
          <Text style={styles.avatarText}>{getInitials(displayName)}</Text>
          {notification.status === 'unread' && <View style={styles.unreadDot} />}
        </View>

        {/* Notification Content */}
        <View style={styles.textContainer}>
          <Text style={[styles.title, { color: colors.text }]} numberOfLines={1}>
            {displayName}
          </Text>
          <Text
            style={[styles.message, { color: colors.secondaryText }]}
            numberOfLines={2}
          >
            {notification.comment || notification.title}
          </Text>
          <Text style={[styles.subtitle, { color: colors.secondaryText }]}>
            {notification.type === 'circle_request' ? 'THRD' : 'SYSTEM'}
          </Text>
        </View>

        {/* Timestamp */}
        <View style={styles.rightSection}>
          <Text style={[styles.timestamp, { color: colors.secondaryText }]}>
            {getTimeAgo()}
          </Text>
        </View>
      </View>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  container: {
    paddingVertical: 16,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
  },
  content: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  avatar: {
    width: 48,
    height: 48,
    borderRadius: 24,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
    position: 'relative',
  },
  avatarText: {
    color: '#fff',
    fontSize: 18,
    fontWeight: '600',
    fontStyle: 'italic',
  },
  unreadDot: {
    position: 'absolute',
    top: 0,
    right: 0,
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: '#007AFF',
    borderWidth: 2,
    borderColor: '#fff',
  },
  textContainer: {
    flex: 1,
    marginRight: 8,
  },
  title: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 4,
  },
  message: {
    fontSize: 14,
    lineHeight: 20,
    marginBottom: 4,
  },
  subtitle: {
    fontSize: 12,
    letterSpacing: 0.5,
  },
  rightSection: {
    alignItems: 'flex-end',
  },
  timestamp: {
    fontSize: 12,
  },
});
