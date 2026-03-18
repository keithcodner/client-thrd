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

  if (message.isSystemMessage) {
    return (
      <View className="px-4 py-3">
        <View className="bg-gray-800 dark:bg-gray-700 rounded-lg p-4">
          <Text className="text-xs font-semibold text-gray-400 dark:text-gray-500 mb-2">
            {message.sender}
          </Text>
          <Text className="text-sm text-gray-300 dark:text-gray-200 leading-5 italic">
            {message.content}
          </Text>
          <Text className="text-xs text-gray-500 dark:text-gray-600 mt-2 text-right">
            {message.timestamp}
          </Text>
        </View>
      </View>
    );
  }

  return (
    <View className="px-4 py-2">
      <View className="bg-gray-800 dark:bg-gray-700 rounded-lg p-3">
        <Text className="text-sm text-gray-300 dark:text-gray-200 leading-5">
          {message.content}
        </Text>
        <Text className="text-xs text-gray-500 dark:text-gray-600 mt-2 text-right">
          {message.timestamp}
        </Text>
      </View>
    </View>
  );
};
