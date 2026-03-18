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
      className="flex-row items-center px-5 py-4 border-b border-gray-700 h-24 border-l-8 border-l-yellow-500"
      style={({ pressed }) => ({
        opacity: pressed ? 0.7 : 1,
        backgroundColor: pressed ? '#222' : '#1a1a1a',
        borderBottomColor: '#333',
        borderBottomWidth: 2,

      })}
    >
      {/* Avatar */}
      <View 
        className="w-12 h-12 rounded-full items-center justify-center mr-3"
        style={{ backgroundColor: '#6B7A4F' }}
      >
        <Text className="text-white font-semibold text-base">
          {getInitials(chat.name)}
        </Text>
      </View>

      {/* Content */}
      <View className="flex-1">
        <View className="flex-row justify-between items-center mb-1">
          <Text className="text-base font-semibold text-white">
            {chat.name}
          </Text>
          {chat.timestamp && (
            <Text className="text-xs" style={{ color: '#666' }}>
              {chat.timestamp}
            </Text>
          )}
        </View>
        <View className="flex-row items-center">
          <Text 
            className="text-sm flex-1"
            style={{ color: '#999' }}
            numberOfLines={1}
          >
            {chat.lastMessage}
          </Text>
          {chat.unread && (
            <View 
              className="w-2 h-2 rounded-full ml-2"
              style={{ backgroundColor: '#ADC178' }}
            />
          )}
        </View>
      </View>
    </Pressable>
  );
};
