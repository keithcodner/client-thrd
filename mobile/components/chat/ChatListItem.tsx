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
    router.push(`/(app)/(tabs)/(chat)/${chat.id}`);
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
      className="flex-row items-center px-5 py-4 border-b h-24 border-l-8"
      style={({ pressed }) => ({
        opacity: pressed ? 0.7 : 1,
        backgroundColor: pressed ? colors.surface : colors.background,
        borderBottomColor: colors.border,
        borderBottomWidth: 2,
        borderLeftColor: '#F59E0B',
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
          <Text className="text-base font-semibold" style={{ color: colors.text }}>
            {chat.name}
          </Text>
          {chat.timestamp && (
            <Text className="text-xs" style={{ color: colors.secondaryText }}>
              {chat.timestamp}
            </Text>
          )}
        </View>
        <View className="flex-row items-center">
          <Text 
            className="text-sm flex-1"
            style={{ color: colors.secondaryText }}
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
