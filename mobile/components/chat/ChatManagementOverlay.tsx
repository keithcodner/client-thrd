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
import { X, Trash2, Pin, MoreVertical, Info, LogOut, MessageSquare } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { useRouter } from 'expo-router';
import { ChatItemData } from './ChatListItem';
import { LucideIcon } from 'lucide-react-native';

const { height: SCREEN_HEIGHT } = Dimensions.get('window');
const OVERLAY_HEIGHT = 240; // Height of the action bar

interface DropdownMenuItem {
  id: string;
  label: string;
  icon: LucideIcon;
  color?: string;
  onPress: () => void;
  showOnlyForOwner?: boolean;
}

interface QuickActionButton {
  id: string;
  icon: LucideIcon;
  color: string;
  onPress: () => void;
  specialStyle?: boolean; // For the "more" button that changes background
}

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
  const [showDropdown, setShowDropdown] = useState(false);
  const [slideAnim] = useState(new Animated.Value(-OVERLAY_HEIGHT));

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
      });
    }
  }, [visible]);

  const handleClose = () => {
    setShowDropdown(false);
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
  };

  // Quick action buttons configuration
  const quickActionButtons: QuickActionButton[] = [
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

  /**
   * QuickActionButton Component
   * Renders individual quick action buttons with consistent styling
   * Handles special styling for buttons like "more" that change based on state
   */
  const QuickActionButton = ({ button }: { button: QuickActionButton }) => {
    const Icon = button.icon;
    const backgroundColor = button.specialStyle && showDropdown 
      ? colours.primary 
      : colours.surface;

    return (
      <Pressable
        onPress={button.onPress}
        className="w-10 h-10 items-center justify-center rounded-full"
        style={{ backgroundColor }}
      >
        <Icon size={18} color={button.color} />
      </Pressable>
    );
  };

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
        <View 
          style={{ 
            backgroundColor: colours.background,
            borderBottomColor: colours.border,
            borderBottomWidth: 1,
          }} 
          className="pt-12 pb-4 px-5"
        >
          <View className="flex-row items-center justify-between mb-3">
            {/* Back Button */}
            <Pressable
              onPress={handleClose}
              className="w-10 h-10 items-center justify-center rounded-full"
              style={{ backgroundColor: colours.surface }}
            >
              <X size={20} color={colours.text} />
            </Pressable>

            <Text className="text-lg font-semibold flex-1 text-center" style={{ color: colours.text }}>
              {chat.name}
            </Text>

            {/* Quick Action Buttons */}
            <View className="flex-row gap-2">
              {quickActionButtons.map(button => (
                <QuickActionButton key={button.id} button={button} />
              ))}
            </View>
          </View>
        </View>

        {/* Dropdown Menu */}
        {showDropdown && (
          <View
            style={{ backgroundColor: colours.card }}
            className="mx-5 mt-2 rounded-lg overflow-hidden"
          >
            {visibleMenuItems.map((item, index) => {
              const Icon = item.icon;
              const isLastItem = index === visibleMenuItems.length - 1;
              
              return (
                <Pressable
                  key={item.id}
                  onPress={item.onPress}
                  className={`flex-row items-center px-4 py-3 ${!isLastItem ? 'border-b' : ''}`}
                  style={({ pressed }) => ({
                    backgroundColor: pressed ? colours.surface : colours.card,
                    borderBottomColor: colours.border,
                  })}
                >
                  <Icon size={20} color={item.color || colours.text} />
                  <Text 
                    className="ml-3 text-base" 
                    style={{ 
                      color: item.id === 'delete-circle' ? colours.error : colours.text 
                    }}
                  >
                    {item.label}
                  </Text>
                </Pressable>
              );
            })}
          </View>
        )}

        {/* Helper Text */}
        {!showDropdown && (
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
