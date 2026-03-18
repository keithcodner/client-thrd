import React, { useState, useRef, useEffect } from "react";
import { 
  View, 
  Text, 
  ScrollView, 
  TextInput, 
  Pressable,
  KeyboardAvoidingView,
  Platform 
} from "react-native";
import { useLocalSearchParams, useRouter } from "expo-router";
import { ChevronLeft, Info, Plus, Mic } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ChatMessage, MessageData } from "@/components/chat/ChatMessage";

// Dummy messages data
const INITIAL_MESSAGES: { [key: string]: MessageData[] } = {
  '1': [
    {
      id: '1',
      sender: 'THRD GUIDE',
      content: 'Welcome to THRD 💚 Start small: Step 1 — create your circle (one is enough, we\'re not collecting Pokémon).',
      timestamp: '12:57 AM',
      isSystemMessage: true,
    },
    {
      id: '2',
      sender: 'THRD GUIDE',
      content: 'Step 2 — explore when you feel like doing something fun, whether it\'s a restaurant, workshop, or exhibition. Step 3 — use the AI Scheduler to find a time that actually works (or add a manual calendar block if you\'re in your \'do-not-disturb\' era).',
      timestamp: '12:57 AM',
      isSystemMessage: true,
    },
    {
      id: '3',
      sender: 'THRD GUIDE',
      content: 'If things start feeling like... a lot, Mind Space has tools to help you reset, and the Help Centre has answers when you\'re like \'wait, how do I—?\' You\'re always in control, and there\'s zero pressure to be loud here.',
      timestamp: '12:57 AM',
      isSystemMessage: true,
    },
  ],
  '2': [
    {
      id: '1',
      sender: 'System',
      content: 'Start a conversation with this circle.',
      timestamp: 'Now',
      isSystemMessage: true,
    },
  ],
};

const ChatDetail = () => {
  const colours = useThemeColours();
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const scrollViewRef = useRef<ScrollView>(null);
  const [messageText, setMessageText] = useState('');

  const chatId = Array.isArray(id) ? id[0] : id || '1';
  const [messages, setMessages] = useState<MessageData[]>(INITIAL_MESSAGES[chatId] || []);
  
  // Get chat name from ID
  const chatName = chatId === '1' ? 'THRD' : 'test';

  // Format current time
  const getCurrentTime = () => {
    const now = new Date();
    let hours = now.getHours();
    const minutes = now.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    const minutesStr = minutes < 10 ? '0' + minutes : minutes;
    return `${hours}:${minutesStr} ${ampm}`;
  };

  const handleSendMessage = () => {
    if (messageText.trim()) {
      const newMessage: MessageData = {
        id: Date.now().toString(),
        sender: 'You',
        content: messageText.trim(),
        timestamp: getCurrentTime(),
        isSystemMessage: false,
      };
      
      setMessages(prev => [...prev, newMessage]);
      setMessageText('');
      
      // Scroll to bottom after sending
      setTimeout(() => {
        scrollViewRef.current?.scrollToEnd({ animated: true });
      }, 100);
    }
  };

  // Scroll to bottom when messages change
  useEffect(() => {
    setTimeout(() => {
      scrollViewRef.current?.scrollToEnd({ animated: false });
    }, 100);
  }, []);

  return (
    <KeyboardAvoidingView 
      className="flex-1 bg-gray-900"
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      {/* Header */}
      <View className="bg-gray-900 pt-12 pb-4 px-4 flex-row items-center justify-between border-b border-gray-800">
        <View className="flex-row items-center flex-1">
          <Pressable 
            onPress={() => router.back()}
            className="mr-4"
          >
            <ChevronLeft size={24} color="#fff" />
          </Pressable>
          
          {/* Avatar */}
          <View 
            className="w-10 h-10 rounded-full items-center justify-center mr-3"
            style={{ backgroundColor: colours.primary }}
          >
            <Text className="text-white font-semibold text-sm">
              {chatName[0].toUpperCase()}
            </Text>
          </View>
          
          <Text className="text-lg font-semibold text-white flex-1">
            {chatName}
          </Text>
        </View>
        
        <Pressable onPress={() => console.log('Chat info')}>
          <Info size={24} color="#fff" />
        </Pressable>
      </View>

      {/* Messages */}
      <ScrollView 
        ref={scrollViewRef}
        className="flex-1 bg-gray-900"
        contentContainerStyle={{ paddingBottom: 10 }}
      >
        {messages.map(message => (
          <ChatMessage key={message.id} message={message} />
        ))}
      </ScrollView>

      {/* Input Bar */}
      <View className="bg-gray-900 px-4 pb-6 pt-3 border-t border-gray-800">
        <View className="flex-row items-center">
          <Pressable 
            className="mr-3"
            onPress={() => console.log('Add attachment')}
          >
            <Plus size={24} color="#9CA3AF" />
          </Pressable>
          
          <View className="flex-1 bg-gray-800 rounded-full px-4 py-3 flex-row items-center">
            <TextInput
              className="flex-1 text-white text-base"
              placeholder="Type something..."
              placeholderTextColor="#6B7280"
              value={messageText}
              onChangeText={setMessageText}
              onSubmitEditing={handleSendMessage}
              returnKeyType="send"
              multiline
            />
          </View>
          
          <Pressable 
            className="ml-3"
            onPress={messageText.trim() ? handleSendMessage : () => console.log('Voice message')}
          >
            <Mic size={24} color="#9CA3AF" />
          </Pressable>
        </View>
      </View>
    </KeyboardAvoidingView>
  );
};

export default ChatDetail;
