import React from 'react';
import { View, Text, Pressable } from 'react-native';
import { useRouter } from 'expo-router';
import { useThemeColours } from '@/hooks/useThemeColours';

export interface ChatItemData {
  id: string;
  name: string;
  lastMessage: string;
  timestamp: string;
  unread?: boolean;
  avatar?: string;
}

interface ChatListItemProps {
  chat: ChatItemData;
}

export const ChatListItem = ({ chat }: ChatListItemProps) => {
  const colors = useThemeColours();
  const router = useRouter();

  const handlePress = () => {
    router.push(`/(app)/(chat)/(chat-home)/${chat.id}`);
  };

  // Get initials from name
  const getInitials = (name: string) => {
    return name
      .split(' ')
      .map(word => word[0])
      .join('')
      .toUpperCase()
      .slice(0, 2);
  };

  return (
    <Pressable
      onPress={handlePress}
      className="flex-row items-center px-4 py-3 border-b border-gray-200 dark:border-gray-800"
      style={({ pressed }) => ({
        opacity: pressed ? 0.7 : 1,
      })}
    >
      {/* Avatar */}
      <View 
        className="w-12 h-12 rounded-full items-center justify-center mr-3"
        style={{ backgroundColor: colors.primary }}
      >
        <Text className="text-white font-semibold text-base">
          {getInitials(chat.name)}
        </Text>
      </View>

      {/* Content */}
      <View className="flex-1">
        <View className="flex-row justify-between items-center mb-1">
          <Text className="text-base font-semibold text-gray-900 dark:text-white">
            {chat.name}
          </Text>
          <Text className="text-xs text-gray-500 dark:text-gray-400">
            {chat.timestamp}
          </Text>
        </View>
        <View className="flex-row items-center">
          <Text 
            className="text-sm text-gray-600 dark:text-gray-400 flex-1"
            numberOfLines={1}
          >
            {chat.lastMessage}
          </Text>
          {chat.unread && (
            <View 
              className="w-2 h-2 rounded-full ml-2"
              style={{ backgroundColor: colors.primary }}
            />
          )}
        </View>
      </View>
    </Pressable>
  );
};
