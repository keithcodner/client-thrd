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
import { ChevronLeft, Info, Plus, Mic, X, Send, BarChart3, Calendar, Image } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ChatMessage, MessageData } from "@/components/chat/ChatMessage";
import { CircleInfoModal } from "@/components/chat/CircleInfoModal";

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
  const [showCircleInfo, setShowCircleInfo] = useState(false);
  const [showInputActions, setShowInputActions] = useState(false);
  
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
    <>
      <KeyboardAvoidingView 
        className="flex-1"
        style={{ backgroundColor: '#1a1a1a' }}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
      {/* Header */}
      <View className="pt-12 pb-4 px-5 flex-row items-center justify-between" style={{ backgroundColor: '#1a1a1a' }}>
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
            style={{ backgroundColor: '#6B7A4F' }}
          >
            <Text className="text-white font-semibold text-sm">
              {chatName[0].toUpperCase()}
            </Text>
          </View>
          
          <Text className="text-lg font-semibold text-white flex-1">
            {chatName}
          </Text>
        </View>
        
        <Pressable onPress={() => setShowCircleInfo(true)}>
          <Info size={24} color="#fff" />
        </Pressable>
      </View>

      {/* Messages */}
      <ScrollView 
        ref={scrollViewRef}
        className="flex-1"
        style={{ backgroundColor: '#1a1a1a' }}
        contentContainerStyle={{ paddingBottom: 10, paddingTop: 10 }}
      >
        {messages.map(message => (
          <ChatMessage key={message.id} message={message} />
        ))}
      </ScrollView>

      {/* Input Bar */}
      <View className="px-4 pb-6 pt-3" style={{ backgroundColor: '#1a1a1a' }}>
        {/* Expanded Actions */}
        {showInputActions && (
          <View className="mb-3 px-4 py-4 rounded-2xl" style={{ backgroundColor: '#2a2a2a' }}>
            <Pressable 
              className="absolute top-3 right-3 z-10"
              onPress={() => setShowInputActions(false)}
            >
              <X size={20} color="#999" />
            </Pressable>
            <View className="flex-row justify-around mt-4">
              <Pressable className="items-center" onPress={() => console.log('Poll')}>
                <View className="w-14 h-14 rounded-full items-center justify-center mb-2" style={{ backgroundColor: '#3a3a3a' }}>
                  <BarChart3 size={24} color="#B8936F" />
                </View>
                <Text className="text-xs font-semibold" style={{ color: '#999' }}>POLL</Text>
              </Pressable>
              <Pressable className="items-center" onPress={() => console.log('Plan')}>
                <View className="w-14 h-14 rounded-full items-center justify-center mb-2" style={{ backgroundColor: '#3a3a3a' }}>
                  <Calendar size={24} color="#6B7A4F" />
                </View>
                <Text className="text-xs font-semibold" style={{ color: '#999' }}>PLAN</Text>
              </Pressable>
              <Pressable className="items-center" onPress={() => console.log('Photo')}>
                <View className="w-14 h-14 rounded-full items-center justify-center mb-2" style={{ backgroundColor: '#3a3a3a' }}>
                  <Image size={24} color="#4A9EFF" />
                </View>
                <Text className="text-xs font-semibold" style={{ color: '#999' }}>PHOTO</Text>
              </Pressable>
            </View>
          </View>
        )}
        
        <View className="flex-row items-center">
          <Pressable 
            className="mr-3"
            onPress={() => setShowInputActions(!showInputActions)}
          >
            {showInputActions ? (
              <X size={24} color="#999" />
            ) : (
              <Plus size={24} color="#999" />
            )}
          </Pressable>
          
          <View className="flex-1 rounded-full px-4 py-3 flex-row items-center" style={{ backgroundColor: '#2a2a2a' }}>
            <TextInput
              className="flex-1 text-white text-base"
              placeholder="Type something..."
              placeholderTextColor="#666"
              value={messageText}
              onChangeText={setMessageText}
              onSubmitEditing={handleSendMessage}
              returnKeyType="send"
              multiline
            />
            <Pressable 
              className="ml-2"
              onPress={() => console.log('Voice message')}
            >
              <Mic size={20} color="#999" />
            </Pressable>
          </View>
          
          {messageText.trim() ? (
            <Pressable 
              className="ml-3 w-10 h-10 rounded-full items-center justify-center"
              style={{ backgroundColor: '#6B7A4F' }}
              onPress={handleSendMessage}
            >
              <Send size={18} color="#fff" />
            </Pressable>
          ) : null}
        </View>
      </View>
    </KeyboardAvoidingView>

    <CircleInfoModal
      visible={showCircleInfo}
      onClose={() => setShowCircleInfo(false)}
      circleName={chatName}
      circleId={chatId}
    />
  </>
  );
};

export default ChatDetail;
