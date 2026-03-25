import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  Pressable,
  Modal,
  Animated,
  StyleSheet,
  Dimensions,
} from 'react-native';
import * as Haptics from 'expo-haptics';
import { Trash2, Pin, MoreVertical, Info, LogOut, MessageSquare, Users } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { useRouter } from 'expo-router';
import { ChatItemData } from './ChatListItem';
import { getCircleMembers } from '@/services/chatService';
import { useSession } from '@/context/AuthContext';
import {
  OverlayHeader,
  DropdownMenu,
  MembersList,
  CircleMember,
  DropdownMenuItem,
  QuickActionButtonData,
} from './chat-management-components';

const { height: SCREEN_HEIGHT } = Dimensions.get('window');
const OVERLAY_HEIGHT = 240; // Height of the action bar

export interface ChatManagementOverlayProps {
  visible: boolean;
  chat: ChatItemData | null;
  onClose: () => void;
  onDelete?: (chatId: string) => void;
  onPin?: (chatId: string) => void;
  onLeave?: (chatId: string) => void;
  onClearChats?: (chatId: string) => void;
  isOwner?: boolean;
}

/**
 * ChatManagementOverlay
 * 
 * A slide-down overlay that appears when long-pressing a chat item.
 * Provides quick actions (delete, pin) and a dropdown menu for additional options.
 * 
 * Features:
 * - Slides from top with animation
 * - Haptic feedback on open (medium impact vibration)
 * - Quick action buttons in header (configurable via quickActionButtons array)
 * - Dropdown menu for additional options (configurable via dropdownMenuItems array)
 * - Navigation to chat detail view
 * - Owner-specific actions (delete circle)
 * 
 * To add new quick action buttons:
 * Add a new object to the quickActionButtons array with:
 * - id: unique identifier
 * - icon: Lucide icon component
 * - color: icon color
 * - onPress: handler function
 * - specialStyle: (optional) true for custom background behavior
 * 
 * To add new dropdown menu items:
 * Add a new object to the dropdownMenuItems array with:
 * - id: unique identifier
 * - label: display text
 * - icon: Lucide icon component
 * - color: icon/text color
 * - onPress: handler function
 * - showOnlyForOwner: (optional) true to show only for circle owners
 * 
 * See: mobile/docs/CHAT_MANAGEMENT_OVERLAY.md
 */
export const ChatManagementOverlay = ({
  visible,
  chat,
  onClose,
  onDelete,
  onPin,
  onLeave,
  onClearChats,
  isOwner = false,
}: ChatManagementOverlayProps) => {
  const colours = useThemeColours();
  const router = useRouter();
  const { onlineUsers } = useSession();
  const [showDropdown, setShowDropdown] = useState(false);
  const [slideAnim] = useState(new Animated.Value(-OVERLAY_HEIGHT));
  const [showMembers, setShowMembers] = useState(false);
  const [members, setMembers] = useState<CircleMember[]>([]);
  const [isLoadingMembers, setIsLoadingMembers] = useState(false);

  // Animate overlay in/out
  useEffect(() => {
    if (visible) {
      // Trigger haptic feedback when overlay opens
      Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
      
      Animated.spring(slideAnim, {
        toValue: 0,
        useNativeDriver: true,
        tension: 65,
        friction: 11,
      }).start();
    } else {
      Animated.timing(slideAnim, {
        toValue: -OVERLAY_HEIGHT,
        duration: 200,
        useNativeDriver: true,
      }).start(() => {
        setShowDropdown(false);
        setShowMembers(false);
      });
    }
  }, [visible]);

  // Fetch circle members and subscribe to presence when members section is opened
  useEffect(() => {
    if (!chat || chat.id === '1' || !showMembers) return;

    console.log('🔍 ========== CHATMANAGEMENTOVERLAY: Members Effect ==========');
    console.log('🔍 Chat ID (conversation):', chat.id, 'Type:', typeof chat.id);
    console.log('🔍 Circle ID:', chat.circleId, 'Type:', typeof chat.circleId);

    // Ensure we have a valid circleId
    if (!chat.circleId) {
      console.error('❌ No circleId found for chat:', chat.id);
      console.error('❌ Chat object:', JSON.stringify(chat, null, 2));
      return;
    }

    const fetchMembers = async () => {
      setIsLoadingMembers(true);
      try {
        const circleId = chat.circleId!; // Already validated above
        console.log('📡 Fetching members for circle:', circleId, 'Type:', typeof circleId);
        const circleMembers = await getCircleMembers(circleId);
        console.log('✅ Received members:', circleMembers, 'Count:', circleMembers.length);
        setMembers(circleMembers);
        
        // Presence is handled by the chat screen when the conversation is opened
      } catch (error) {
        console.error('Error fetching circle members:', error);
      } finally {
        setIsLoadingMembers(false);
      }
    };

    fetchMembers();

    // Presence cleanup is handled by the chat screen
  }, [chat, showMembers]);

  const handleViewMembers = () => {
    console.log('🔄 handleViewMembers called, current showMembers:', showMembers);
    setShowMembers(!showMembers);
    setShowDropdown(false);
    console.log('🔄 Setting showMembers to:', !showMembers);
  };

  const handleClose = () => {
    setShowDropdown(false);
    setShowMembers(false);
    onClose();
  };

  const handleViewInfo = () => {
    if (chat) {
      handleClose();
      // Navigate to circle info (can be implemented as modal or screen)
      router.push(`/(app)/(tabs)/(chat)/${chat.id}`);
    }
  };

  const handleLeaveCircle = () => {
    if (chat && onLeave) {
      handleClose();
      onLeave(chat.id);
    }
  };

  const handleDeleteCircle = () => {
    if (chat && onDelete && isOwner) {
      handleClose();
      onDelete(chat.id);
    }
  };

  const handleClearChats = () => {
    if (chat && onClearChats) {
      handleClose();
      onClearChats(chat.id);
    }
  };

  const handlePinChat = () => {
    if (chat && onPin) {
      onPin(chat.id);
      handleClose();
    }
  };

  const handleDeleteChat = () => {
    if (chat && onDelete) {
      handleClose();
      onDelete(chat.id);
    }
  };

  const toggleDropdown = () => {
    setShowDropdown(!showDropdown);
    setShowMembers(false); // Close members when toggling dropdown
  };

  // Quick action buttons configuration
  const quickActionButtons: QuickActionButtonData[] = [
    {
      id: 'delete',
      icon: Trash2,
      color: colours.error,
      onPress: handleDeleteChat,
    },
    {
      id: 'pin',
      icon: Pin,
      color: colours.warning,
      onPress: handlePinChat,
    },
    {
      id: 'more',
      icon: MoreVertical,
      color: showDropdown ? '#fff' : colours.text,
      onPress: toggleDropdown,
      specialStyle: true, // Changes background based on dropdown state
    },
  ];

  // Dropdown menu items configuration
  const dropdownMenuItems: DropdownMenuItem[] = [
    {
      id: 'members',
      label: 'Members',
      icon: Users,
      color: colours.primary,
      onPress: handleViewMembers,
    },
    {
      id: 'view-info',
      label: 'View Info',
      icon: Info,
      color: colours.info,
      onPress: handleViewInfo,
    },
    {
      id: 'leave-circle',
      label: 'Leave Circle',
      icon: LogOut,
      color: colours.warning,
      onPress: handleLeaveCircle,
    },
    {
      id: 'delete-circle',
      label: 'Delete Circle',
      icon: Trash2,
      color: colours.error,
      onPress: handleDeleteCircle,
      showOnlyForOwner: true,
    },
    {
      id: 'clear-chats',
      label: 'Clear Chats',
      icon: MessageSquare,
      color: colours.secondaryText,
      onPress: handleClearChats,
    },
  ];

  // Filter menu items based on ownership
  const visibleMenuItems = dropdownMenuItems.filter(
    item => !item.showOnlyForOwner || (item.showOnlyForOwner && isOwner)
  );

  if (!chat) return null;

  return (
    <Modal
      visible={visible}
      transparent
      animationType="fade"
      statusBarTranslucent
      onRequestClose={handleClose}
    >
      {/* Backdrop */}
      <Pressable
        style={styles.backdrop}
        onPress={handleClose}
        className="flex-1"
      >
        <View style={{ backgroundColor: 'rgba(0, 0, 0, 0.5)' }} className="flex-1" />
      </Pressable>

      {/* Overlay Content */}
      <Animated.View
        style={[
          styles.overlayContainer,
          {
            backgroundColor: colours.background,
            borderBottomColor: colours.border,
            transform: [{ translateY: slideAnim }],
          },
        ]}
      >
        {/* Header with Quick Actions */}
        <OverlayHeader
          chatName={chat.name}
          onClose={handleClose}
          quickActionButtons={quickActionButtons}
          isDropdownActive={showDropdown}
        />

        {/* Dropdown Menu */}
        <DropdownMenu
          visible={showDropdown}
          items={visibleMenuItems}
        />

        {/* Members List */}
        <MembersList
          visible={showMembers}
          members={members}
          onlineUsers={onlineUsers}
          isLoading={isLoadingMembers}
        />

        {/* Helper Text */}
        {!showDropdown && !showMembers && (
          <View className="px-5 py-4">
            <Text className="text-sm text-center" style={{ color: colours.secondaryText }}>
              Tap the three dots for more options
            </Text>
          </View>
        )}
      </Animated.View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  backdrop: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    zIndex: 1,
  },
  overlayContainer: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    zIndex: 2,
    elevation: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    borderBottomWidth: 1,
  },
});
