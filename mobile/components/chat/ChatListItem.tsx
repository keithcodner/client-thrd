import React, { useState, useEffect } from 'react';
import { View, Text, Pressable, Image } from 'react-native';
import { useRouter } from 'expo-router';
import { useThemeColours } from '@/hooks/useThemeColours';
import { getInitials, getAvatarColor } from '@/utils/avatarUtils';

export interface ChatItemData {
  id: string; // conversation_id
  name: string;
  lastMessage: string;
  timestamp: string;
  unread?: boolean;
  avatar?: string;
  isPrivate?: boolean;
  circleId?: number; // circle_id for reference
}

interface ChatListItemProps {
  chat: ChatItemData;
  /** 
   * Called when user long-presses the chat item
   * Use this to show the ChatManagementOverlay
   */
  onLongPress?: (chat: ChatItemData) => void;
}

export const ChatListItem = ({ chat, onLongPress }: ChatListItemProps) => {
  const colors = useThemeColours();
  const router = useRouter();
  const [hasUnreadMessages, setHasUnreadMessages] = useState(chat.unread || false);

  // Update local state when prop changes (from polling)
  useEffect(() => {
    setHasUnreadMessages(chat.unread || false);
  }, [chat.unread]);

  const handlePress = () => {
    // Clear unread status when user opens the chat
    setHasUnreadMessages(false);
    router.push(`/${chat.id}`);
  };

  const handleLongPress = () => {
    if (onLongPress) {
      onLongPress(chat);
    }
  };

  return (
    <Pressable
      onPress={handlePress}
      onLongPress={handleLongPress}
      className={`flex-row items-center px-5 py-4 h-24 
        ${chat.id === '1' ? 'border-l-8 border-yellow-700' : ''}
        ${chat.id === '1' ? 'border-b' : 'border-b border-gray-800'}
        `}
      style={({ pressed }) => ({
        opacity: pressed ? 0.7 : 1,
        backgroundColor: pressed ? colors.surface : colors.background,
        borderBottomColor: colors.border,
        borderBottomWidth: 1,
      })}
    >
      {/* Avatar */}
      {chat.avatar ? (
        <Image 
          source={{ uri: chat.avatar }}
          className="w-12 h-12 rounded-full mr-3"
          style={{ backgroundColor: colors.surface }}
        />
      ) : (
        <View 
          className="w-12 h-12 rounded-full items-center justify-center mr-3"
          style={{ backgroundColor: chat.id === '1' ? colors.accent : getAvatarColor(chat.name) }}
        >
          <Text className="text-white font-semibold text-base">
            {getInitials(chat.name)}
          </Text>
        </View>
      )}

      {/* Content */}
      <View className="flex-1">
        <View className="flex-row justify-between items-center mb-1">
          <View className="flex-row items-center flex-1">
            <Text className="text-base font-semibold" style={{ color: colors.text }}>
              {chat.name}
            </Text>
            {chat.isPrivate !== undefined && (
              <View 
                className="ml-2 px-2 py-0.5 rounded"
                style={{ backgroundColor: chat.isPrivate ? '#7C2D12' : '#059669' }}
              >
                <Text className="text-white text-xs font-semibold">
                  {chat.isPrivate ? 'Private' : 'Public'}
                </Text>
              </View>
            )}
          </View>
          {chat.timestamp && (
            <Text className="text-xs" style={{ color: colors.secondaryText }}>
              {chat.timestamp}
            </Text>
          )}
        </View>
        <View className="flex-row items-center">
          <Text 
            className="text-sm flex-1 italic"
            style={{ color: colors.secondaryText }}
            numberOfLines={1}
          >
            {chat.lastMessage}
          </Text>
          {hasUnreadMessages && (
            <View 
              className="w-3 h-3 rounded-full ml-2"
              style={{ backgroundColor: '#10B981' }}
            />
          )}
        </View>
      </View>
    </Pressable>
  );
};
