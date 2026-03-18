import React from 'react';
import { View, Text } from 'react-native';
import { useThemeColours } from '@/hooks/useThemeColours';

export interface MessageData {
  id: string;
  sender: string;
  content: string;
  timestamp: string;
  isSystemMessage?: boolean;
}

interface ChatMessageProps {
  message: MessageData;
}

export const ChatMessage = ({ message }: ChatMessageProps) => {
  const colors = useThemeColours();

  // Get initials from sender name
  const getInitials = (name: string) => {
    return name
      .split(' ')
      .map(word => word[0])
      .join('')
      .toUpperCase()
      .slice(0, 1);
  };

  if (message.isSystemMessage) {
    return (
      <View className="px-4 py-2">
        <View className="flex-row items-start">
          {/* Avatar */}
          <View 
            className="w-8 h-8 rounded-full items-center justify-center mr-2 mt-5"
            style={{ backgroundColor: '#6B7A4F' }}
          >
            <Text className="text-white font-semibold text-xs">
              {getInitials(message.sender)}
            </Text>
          </View>
          
          {/* Message Content */}
          <View className="flex-1">
            <Text className="text-xs font-semibold uppercase tracking-wide mb-2" style={{ color: '#999' }}>
              {message.sender}
            </Text>
            <View className="rounded-2xl p-4" style={{ backgroundColor: '#2a2a2a' }}>
              <Text className="text-sm leading-6" style={{ color: '#ddd' }}>
                {message.content}
              </Text>
              <Text className="text-xs mt-2 text-right" style={{ color: '#666' }}>
                {message.timestamp}
              </Text>
            </View>
          </View>
        </View>
      </View>
    );
  }

  // User messages - aligned right with green background
  return (
    <View className="px-4 py-2">
      <View className="flex-row items-end justify-end">
        <View className="items-end" style={{ maxWidth: '80%' }}>
          <View className="rounded-2xl px-4 py-3" style={{ backgroundColor: '#6B7A4F' }}>
            <Text className="text-sm leading-5 text-white">
              {message.content}
            </Text>
          </View>
          <Text className="text-xs mt-1" style={{ color: '#666' }}>
            {message.timestamp}
          </Text>
        </View>
        {/* Avatar */}
        <View 
          className="w-8 h-8 rounded-full items-center justify-center ml-2"
          style={{ backgroundColor: '#6B7A4F' }}
        >
          <Text className="text-white font-semibold text-xs">
            {getInitials(message.sender)}
          </Text>
        </View>
      </View>
    </View>
  );
};
