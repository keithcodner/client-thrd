import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Pressable,
  ActivityIndicator,
  Alert,
  ScrollView,
} from 'react-native';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { ChevronLeft, Check, X } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { getInitials, getAvatarColor } from '@/utils/avatarUtils';
import {
  getNotificationById,
  markNotificationAsRead,
  Notification,
} from '@/services/notificationService';
import { acceptCircleInvite, denyCircleInvite } from '@/services/chatService';
import { format } from 'date-fns';

export default function NotificationDetailScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const colors = useThemeColours();

  const [notification, setNotification] = useState<Notification | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isProcessing, setIsProcessing] = useState(false);
  const [actionTaken, setActionTaken] = useState<'accepted' | 'denied' | null>(null);

  const notificationId = parseInt(params.id as string);

  // Fetch notification details
  useEffect(() => {
    const fetchNotification = async () => {
      try {
        setIsLoading(true);
        const data = await getNotificationById(notificationId);
        setNotification(data);

        // Mark as read
        if (data.status === 'unread') {
          await markNotificationAsRead(notificationId);
        }
      } catch (error) {
        console.error('Error fetching notification:', error);
        Alert.alert('Error', 'Failed to load notification');
        router.back();
      } finally {
        setIsLoading(false);
      }
    };

    fetchNotification();
  }, [notificationId]);

  const handleAcceptInvite = async () => {
    if (!notification?.circle_request) return;

    setIsProcessing(true);
    try {
      await acceptCircleInvite(notification.circle_request.id);
      setActionTaken('accepted');
      
      Alert.alert(
        'Success',
        `You've joined ${notification.circle_request.circle_name}!`,
        [
          {
            text: 'OK',
            onPress: () => router.back(),
          },
        ]
      );
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || 'Failed to accept invite';
      Alert.alert('Error', errorMessage);
    } finally {
      setIsProcessing(false);
    }
  };

  const handleDenyInvite = async () => {
    if (!notification?.circle_request) return;

    Alert.alert(
      'Decline Invitation',
      'Are you sure you want to decline this invitation?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Decline',
          style: 'destructive',
          onPress: async () => {
            setIsProcessing(true);
            try {
              await denyCircleInvite(notification.circle_request!.id);
              setActionTaken('denied');
              
              Alert.alert(
                'Declined',
                'You have declined the invitation',
                [
                  {
                    text: 'OK',
                    onPress: () => router.back(),
                  },
                ]
              );
            } catch (error: any) {
              const errorMessage = error.response?.data?.message || 'Failed to decline invite';
              Alert.alert('Error', errorMessage);
            } finally {
              setIsProcessing(false);
            }
          },
        },
      ]
    );
  };

  const getDisplayName = () => {
    if (notification?.from_user) {
      return (
        notification.from_user.firstname ||
        notification.from_user.name ||
        notification.from_user.username ||
        'Unknown'
      );
    }
    return 'Unknown';
  };

  const formatDate = (dateString: string) => {
    try {
      return format(new Date(dateString), "MMMM d, yyyy 'at' h:mm a");
    } catch {
      return dateString;
    }
  };

  if (isLoading) {
    return (
      <View style={[styles.container, { backgroundColor: colors.background }]}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={colors.info} />
        </View>
      </View>
    );
  }

  if (!notification) {
    return (
      <View style={[styles.container, { backgroundColor: colors.background }]}>
        <View style={styles.errorContainer}>
          <Text style={[styles.errorText, { color: colors.secondaryText }]}>
            Notification not found
          </Text>
        </View>
      </View>
    );
  }

  const displayName = getDisplayName();
  const isCircleRequest = notification.type === 'circle_request';
  const canTakeAction =
    isCircleRequest &&
    notification.circle_request &&
    notification.circle_request.status === 'pending' &&
    !actionTaken;

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      {/* Header */}
      <View style={[styles.header, { backgroundColor: colors.background }]}>
        <Pressable onPress={() => router.back()} style={styles.backButton}>
          <ChevronLeft size={24} color={colors.info} />
          <Text style={[styles.backText, { color: colors.info }]}>Back</Text>
        </Pressable>
        <Text style={[styles.headerTitle, { color: colors.text }]}>Notification</Text>
        <View style={{ width: 60 }} />
      </View>

      <ScrollView
        style={styles.content}
        contentContainerStyle={styles.contentContainer}
      >
        {/* Avatar & User Info */}
        <View style={styles.userSection}>
          <View style={[styles.avatar, { backgroundColor: getAvatarColor(displayName) }]}>
            <Text style={styles.avatarText}>{getInitials(displayName)}</Text>
          </View>
          <Text style={[styles.userName, { color: colors.text }]}>{displayName}</Text>
          <Text style={[styles.timestamp, { color: colors.secondaryText }]}>
            {formatDate(notification.created_at)}
          </Text>
        </View>

        {/* Notification Content */}
        <View style={[styles.card, { backgroundColor: colors.card }]}>
          <Text style={[styles.title, { color: colors.text }]}>
            {notification.title}
          </Text>
          <Text style={[styles.message, { color: colors.secondaryText }]}>
            {notification.comment}
          </Text>
        </View>

        {/* Circle Request Details */}
        {isCircleRequest && notification.circle_request && (
          <View style={[styles.card, { backgroundColor: colors.card }]}>
            <Text style={[styles.sectionTitle, { color: colors.text }]}>
              Circle Details
            </Text>
            <View style={styles.detailRow}>
              <Text style={[styles.detailLabel, { color: colors.secondaryText }]}>
                Circle Name:
              </Text>
              <Text style={[styles.detailValue, { color: colors.text }]}>
                {notification.circle_request.circle_name}
              </Text>
            </View>
            <View style={styles.detailRow}>
              <Text style={[styles.detailLabel, { color: colors.secondaryText }]}>
                Invited By:
              </Text>
              <Text style={[styles.detailValue, { color: colors.text }]}>
                {notification.circle_request.requester_name}
              </Text>
            </View>
            <View style={styles.detailRow}>
              <Text style={[styles.detailLabel, { color: colors.secondaryText }]}>
                Status:
              </Text>
              <Text
                style={[
                  styles.detailValue,
                  { color: actionTaken ? colors.info : colors.text },
                  actionTaken && styles.statusBold,
                ]}
              >
                {actionTaken === 'accepted'
                  ? 'Accepted'
                  : actionTaken === 'denied'
                  ? 'Declined'
                  : notification.circle_request.status.charAt(0).toUpperCase() +
                    notification.circle_request.status.slice(1)}
              </Text>
            </View>
          </View>
        )}

        {/* Action Buttons */}
        {canTakeAction && (
          <View style={styles.actionsContainer}>
            <Pressable
              onPress={handleAcceptInvite}
              disabled={isProcessing}
              style={[styles.actionButton, styles.acceptButton, { backgroundColor: colors.info }]}
            >
              {isProcessing ? (
                <ActivityIndicator size="small" color="#fff" />
              ) : (
                <>
                  <Check size={20} color="#fff" />
                  <Text style={styles.actionButtonText}>Accept Invitation</Text>
                </>
              )}
            </Pressable>

            <Pressable
              onPress={handleDenyInvite}
              disabled={isProcessing}
              style={[
                styles.actionButton,
                styles.denyButton,
                { borderColor: colors.border },
              ]}
            >
              <X size={20} color={colors.secondaryText} />
              <Text style={[styles.denyButtonText, { color: colors.secondaryText }]}>
                Decline
              </Text>
            </Pressable>
          </View>
        )}

        {/* Action Taken Message */}
        {actionTaken && (
          <View style={[styles.actionMessage, { backgroundColor: colors.card }]}>
            <Text style={[styles.actionMessageText, { color: colors.info }]}>
              {actionTaken === 'accepted'
                ? '✓ You have accepted this invitation'
                : '✗ You have declined this invitation'}
            </Text>
          </View>
        )}
      </ScrollView>
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
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
    width: 60,
  },
  backText: {
    fontSize: 16,
    marginLeft: 2,
  },
  headerTitle: {
    fontSize: 16,
    fontWeight: '600',
  },
  content: {
    flex: 1,
  },
  contentContainer: {
    padding: 20,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorText: {
    fontSize: 16,
  },
  userSection: {
    alignItems: 'center',
    marginBottom: 24,
  },
  avatar: {
    width: 80,
    height: 80,
    borderRadius: 40,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  avatarText: {
    color: '#fff',
    fontSize: 32,
    fontWeight: '600',
    fontStyle: 'italic',
  },
  userName: {
    fontSize: 20,
    fontWeight: '600',
    marginBottom: 4,
  },
  timestamp: {
    fontSize: 14,
  },
  card: {
    padding: 16,
    borderRadius: 12,
    marginBottom: 16,
  },
  title: {
    fontSize: 18,
    fontWeight: '600',
    marginBottom: 8,
  },
  message: {
    fontSize: 16,
    lineHeight: 24,
  },
  sectionTitle: {
    fontSize: 16,
    fontWeight: '600',
    marginBottom: 12,
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 8,
  },
  detailLabel: {
    fontSize: 14,
  },
  detailValue: {
    fontSize: 14,
    fontWeight: '500',
  },
  statusBold: {
    fontWeight: '700',
  },
  actionsContainer: {
    gap: 12,
    marginTop: 8,
  },
  actionButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 16,
    borderRadius: 12,
    gap: 8,
  },
  acceptButton: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  denyButton: {
    borderWidth: 1,
  },
  actionButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
  denyButtonText: {
    fontSize: 16,
    fontWeight: '600',
  },
  actionMessage: {
    padding: 16,
    borderRadius: 12,
    marginTop: 8,
  },
  actionMessageText: {
    fontSize: 16,
    fontWeight: '600',
    textAlign: 'center',
  },
});
