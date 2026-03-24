import React, { useState, useRef, useEffect } from "react";
import { 
  View, 
  Text, 
  ScrollView, 
  TextInput, 
  Pressable,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
  Keyboard
} from "react-native";
import { useLocalSearchParams, useRouter } from "expo-router";
import { useNavigation } from "@react-navigation/native";
import { ChevronLeft, Info, Plus, Mic, X, Send, BarChart3, Calendar, Image } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ChatMessage, MessageData } from "@/components/chat/ChatMessage";
import { CircleInfoModal } from "@/components/chat/CircleInfoModal";
import { useSession } from "@/context/AuthContext";
import { useUnreadMessagesContext } from "@/context/UnreadMessagesContext";
import { sendMessage, getUserCircleData, getConversationMessages, updateTypingStatus, markMessagesAsRead } from "@/services/chatService";
import websocketService from "@/services/websocketService";
import { getInitials, getAvatarColor } from "@/utils/avatarUtils";
import Toast from "react-native-toast-message";

// Dummy messages data with dates
const INITIAL_MESSAGES: { [key: string]: MessageData[] } = {
  '1': [
    {
      id: '1',
      sender: 'THRD GUIDE',
      content: 'Welcome to THRD 💚 Start small: Step 1 — create your circle (one is enough, we\'re not collecting Pokémon).',
      timestamp: '12:57 AM',
      createdAt: new Date().toISOString(),
      isSystemMessage: true,
    },
    {
      id: '2',
      sender: 'THRD GUIDE',
      content: 'Step 2 — explore when you feel like doing something fun, whether it\'s a restaurant, workshop, or exhibition. Step 3 — use the AI Scheduler to find a time that actually works (or add a manual calendar block if you\'re in your \'do-not-disturb\' era).',
      timestamp: '12:57 AM',
      createdAt: new Date().toISOString(),
      isSystemMessage: true,
    },
    {
      id: '3',
      sender: 'THRD GUIDE',
      content: 'If things start feeling like... a lot, Mind Space has tools to help you reset, and the Help Centre has answers when you\'re like \'wait, how do I—?\' You\'re always in control, and there\'s zero pressure to be loud here.',
      timestamp: '12:57 AM',
      createdAt: new Date().toISOString(),
      isSystemMessage: true,
    },
  ],
  '2': [
    {
      id: '1',
      sender: 'System',
      content: 'Start a conversation with this circle.',
      timestamp: 'Now',
      createdAt: new Date().toISOString(),
      isSystemMessage: true,
    },
  ],
};

// Date separator component
const DateSeparator = ({ date, colours }: { date: string; colours: any }) => {
  return (
    <View className="px-4 py-3 items-center">
      <View 
        className="px-4 py-2 rounded-full" 
        style={{ backgroundColor: colours.card }}
      >
        <Text 
          className="text-xs font-semibold" 
          style={{ color: colours.secondaryText }}
        >
          {date}
        </Text>
      </View>
    </View>
  );
};

// Helper to format date for separator
const formatDateSeparator = (date: Date): string => {
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);
  
  const messageDate = new Date(date);
  
  // Reset hours for date comparison
  today.setHours(0, 0, 0, 0);
  yesterday.setHours(0, 0, 0, 0);
  messageDate.setHours(0, 0, 0, 0);
  
  if (messageDate.getTime() === today.getTime()) {
    return 'Today';
  } else if (messageDate.getTime() === yesterday.getTime()) {
    return 'Yesterday';
  } else {
    // Format as "Mon, Jan 15"
    const options: Intl.DateTimeFormatOptions = { 
      weekday: 'short', 
      month: 'short', 
      day: 'numeric' 
    };
    return messageDate.toLocaleDateString('en-US', options);
  }
};

// Helper to check if two dates are on different days
const isDifferentDay = (date1: string | Date, date2: string | Date): boolean => {
  const d1 = new Date(date1);
  const d2 = new Date(date2);
  
  return (
    d1.getFullYear() !== d2.getFullYear() ||
    d1.getMonth() !== d2.getMonth() ||
    d1.getDate() !== d2.getDate()
  );
};

const MESSAGE_LIMIT = 30;

const ChatDetail = () => {
  const colours = useThemeColours();
  const { id } = useLocalSearchParams();
  const router = useRouter();
  const navigation = useNavigation();
  const scrollViewRef = useRef<ScrollView>(null);
  const [messageText, setMessageText] = useState('');
  const { user, session } = useSession();
  const { refresh: refreshUnreadCounts } = useUnreadMessagesContext();

  const chatId = Array.isArray(id) ? id[0] : id || '1';
  const [messages, setMessages] = useState<MessageData[]>([]);
  const [showCircleInfo, setShowCircleInfo] = useState(false);
  const [showInputActions, setShowInputActions] = useState(false);
  const [isSending, setIsSending] = useState(false);
  const [isLoadingMessages, setIsLoadingMessages] = useState(true);
  const [chatName, setChatName] = useState<string>('Loading...');
  const [isLoadingChatInfo, setIsLoadingChatInfo] = useState(true);
  const [isKeyboardVisible, setIsKeyboardVisible] = useState(false);
  const [isOwner, setIsOwner] = useState(false);
  const [circleId, setCircleId] = useState<string | undefined>(undefined);
  
  // Pagination state
  const [hasMore, setHasMore] = useState(false);
  const [isLoadingMore, setIsLoadingMore] = useState(false);
  const [offset, setOffset] = useState(0);
  const scrollContentHeight = useRef(0);
  const scrollViewHeight = useRef(0);

  // Typing indicator state
  const [typingUsers, setTypingUsers] = useState<Set<string>>(new Set());
  const typingTimeoutRef = useRef<number | undefined>(undefined);

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

  // Handle keyboard visibility and hide tabs when keyboard is shown
  useEffect(() => {
    const keyboardWillShowListener = Keyboard.addListener(
      Platform.OS === 'ios' ? 'keyboardWillShow' : 'keyboardDidShow',
      () => {
        setIsKeyboardVisible(true);
        // Hide tab bar when keyboard is shown
        const parent = navigation.getParent();
        parent?.setOptions({
          tabBarStyle: { display: 'none' }
        });
      }
    );

    const keyboardWillHideListener = Keyboard.addListener(
      Platform.OS === 'ios' ? 'keyboardWillHide' : 'keyboardDidHide',
      () => {
        setIsKeyboardVisible(false);
        // Show tab bar when keyboard is hidden
        const parent = navigation.getParent();
        parent?.setOptions({
          tabBarStyle: {
            backgroundColor: colours.background,
            borderTopColor: colours.border,
          }
        });
      }
    );

    return () => {
      keyboardWillShowListener.remove();
      keyboardWillHideListener.remove();
      // Restore tab bar when leaving this screen
      const parent = navigation.getParent();
      parent?.setOptions({
        tabBarStyle: {
          backgroundColor: colours.background,
          borderTopColor: colours.border,
        }
      });
    };
  }, [navigation, colours]);

  const handleSendMessage = async () => {
    if (messageText.trim() && user) {
      setIsSending(true);
      
      // Clear typing timeout and send typing=false
      if (typingTimeoutRef.current) {
        clearTimeout(typingTimeoutRef.current);
      }
      if (chatId !== '1') {
        updateTypingStatus(parseInt(chatId), false);
      }
      
      try {
        // Create optimistic message for immediate UI update
        const optimisticMessage: MessageData = {
          id: `temp-${Date.now()}`,
          sender: user.name,
          senderId: user.id,
          content: messageText.trim(),
          timestamp: getCurrentTime(),
          createdAt: new Date().toISOString(),
          isSystemMessage: false,
          isCurrentUser: true,
        };
        
        // Add message to UI immediately
        setMessages(prev => [...prev, optimisticMessage]);
        const messageContent = messageText.trim();
        setMessageText('');
        
        // Scroll to bottom after sending
        setTimeout(() => {
          scrollViewRef.current?.scrollToEnd({ animated: true });
        }, 100);

        // Send message to API
        const response = await sendMessage({
          conversation_id: parseInt(chatId),
          content: messageContent,
          type: 'chat',
        });

        // Update the temporary message with the actual message from server
        if (response.chat) {
          setMessages(prev => 
            prev.map(msg => 
              msg.id === optimisticMessage.id 
                ? {
                    ...msg,
                    id: response.chat.id.toString(),
                  }
                : msg
            )
          );
        }
      } catch (error) {
        console.error('Error sending message:', error);
        // Optionally remove the optimistic message on error
        setMessages(prev => prev.filter(msg => !msg.id.startsWith('temp-')));
        // Show error to user
        alert('Failed to send message. Please try again.');
      } finally {
        setIsSending(false);
      }
    }
  };

  /**
   * Handle text input changes and manage typing status
   * Sends typing=true when user starts typing
   * Auto-sends typing=false after 3 seconds of inactivity
   */
  const handleTextChange = (text: string) => {
    setMessageText(text);

    // Skip typing status for THRD system chat
    if (chatId === '1') {
      return;
    }

    // Clear existing timeout
    if (typingTimeoutRef.current) {
      clearTimeout(typingTimeoutRef.current);
    }

    // Send typing=true if there's text
    if (text.trim()) {
      updateTypingStatus(parseInt(chatId), true);

      // Auto-send typing=false after 3 seconds of inactivity
      typingTimeoutRef.current = setTimeout(() => {
        updateTypingStatus(parseInt(chatId), false);
      }, 3000);
    } else {
      // Empty text = not typing
      updateTypingStatus(parseInt(chatId), false);
    }
  };

  /**
   * Handle key press events for Enter/Shift+Enter behavior
   * - Enter: Send message
   * - Shift+Enter: New line
   * Note: This primarily works on web/desktop platforms
   */
  const handleKeyPress = (e: any) => {
    // Check if Enter key is pressed without Shift
    if (e.nativeEvent.key === 'Enter' && !e.nativeEvent.shiftKey) {
      e.preventDefault();
      handleSendMessage();
    }
    // If Shift+Enter, React Native will handle it naturally (new line)
  };

  /**
   * Render typing indicator below messages
   */
  const renderTypingIndicator = () => {
    if (typingUsers.size === 0) return null;

    const typingText = typingUsers.size === 1
      ? `${Array.from(typingUsers)[0]} is typing...`
      : `${typingUsers.size} people are typing...`;

    return (
      <View className="px-4 py-2">
        <Text 
          className="text-xs italic" 
          style={{ color: colours.secondaryText }}
        >
          {typingText}
        </Text>
      </View>
    );
  };

  // Load circle/chat info on mount
  useEffect(() => {
    const loadChatInfo = async () => {
      setIsLoadingChatInfo(true);
      try {
        if (chatId === '1') {
          // Special case for THRD system chat
          setChatName('THRD');
          setIsOwner(false); // THRD is not user-owned
          setCircleId(undefined);
        } else {
          // Fetch circle data to get the circle name
          // chatId is now the conversation_id
          const response = await getUserCircleData();
          const circle = response.circles.find((c: any) => c.conversation_id?.toString() === chatId);
          
          if (circle) {
            setChatName(circle.name);
            setCircleId(circle.id.toString());
            // Check if current user is the owner
            setIsOwner(user?.id === circle.user_owner_id);
          } else {
            setChatName('Chat');
            setCircleId(undefined);
            setIsOwner(false);
            console.warn(`Circle with conversation_id ${chatId} not found`);
          }
        }
      } catch (error) {
        console.error('Error loading chat info:', error);
        setChatName('Chat');
        setCircleId(undefined);
        setIsOwner(false);
      } finally {
        setIsLoadingChatInfo(false);
      }
    };

    loadChatInfo();
  }, [chatId, user]);

  // Load messages on mount (with caching)
  useEffect(() => {
    const loadMessages = async () => {
      setIsLoadingMessages(true);
      setOffset(0); // Reset offset
      try {
        // For THRD system chat (ID: 1), use local initial messages
        if (chatId === '1') {
          const initialMessages = INITIAL_MESSAGES[chatId] || [];
          const limitedMessages = initialMessages.slice(-MESSAGE_LIMIT);
          setMessages(limitedMessages);
          setHasMore(false);
        } else {
          // For all other chats, fetch from API with caching
          const response = await getConversationMessages(
            parseInt(chatId), 
            MESSAGE_LIMIT, 
            0, // offset = 0 for initial load (will be cached)
            true // useCache = true
          );
          
          if (response.messages && Array.isArray(response.messages)) {
            setMessages(response.messages);
            setHasMore(response.hasMore || false);
            setOffset(response.messages.length); // Set offset for next load
            
            // Mark messages as read after loading and refresh unread counts
            markMessagesAsRead(parseInt(chatId))
              .then(() => {
                // Immediately refresh unread counts to update badge
                refreshUnreadCounts();
              })
              .catch(err => console.log('⚠️ Failed to mark messages as read:', err));
          } else {
            // If no messages returned, set empty array
            setMessages([]);
            setHasMore(false);
          }
        }
      } catch (error) {
        console.error('Error loading messages:', error);
        // On error, set empty messages array
        setMessages([]);
        setHasMore(false);
      } finally {
        setIsLoadingMessages(false);
      }
    };

    loadMessages();
  }, [chatId]);

  /**
   * Load more messages (infinite scroll)
   * Called when user scrolls to the top
   * These messages are NOT cached
   */
  const loadMoreMessages = async () => {
    if (isLoadingMore || !hasMore || chatId === '1') {
      return;
    }

    setIsLoadingMore(true);
    try {
      const response = await getConversationMessages(
        parseInt(chatId),
        MESSAGE_LIMIT,
        offset, // Use current offset for pagination
        false // useCache = false (don't cache paginated results)
      );

      if (response.messages && Array.isArray(response.messages) && response.messages.length > 0) {
        // Prepend older messages to the beginning
        setMessages(prev => [...response.messages, ...prev]);
        setHasMore(response.hasMore || false);
        setOffset(prev => prev + response.messages.length);
      } else {
        setHasMore(false);
      }
    } catch (error) {
      console.error('Error loading more messages:', error);
      setHasMore(false);
    } finally {
      setIsLoadingMore(false);
    }
  };

  /**
   * Handle scroll event to detect when user scrolls near the top
   * Load more messages when user is within 200px of the top
   */
  const handleScroll = (event: any) => {
    const { contentOffset, contentSize, layoutMeasurement } = event.nativeEvent;
    
    scrollContentHeight.current = contentSize.height;
    scrollViewHeight.current = layoutMeasurement.height;

    // Check if user scrolled near the top (within 200px)
    const scrolledToTop = contentOffset.y < 200;

    if (scrolledToTop && hasMore && !isLoadingMore) {
      loadMoreMessages();
    }
  };

  // Subscribe to WebSocket for real-time messages
  useEffect(() => {
    // Skip WebSocket for THRD system chat (ID: 1) - it's a local-only chat
    if (chatId === '1') {
      console.log('Skipping WebSocket for THRD system chat');
      return;
    }

    if (!user || !session) {
      console.log('User or session not available, skipping WebSocket connection');
      return;
    }

    console.log('🔌 Subscribing to conversation messages...');
    
    // WebSocket is already connected via AuthContext, just subscribe to this conversation

    // Subscribe to conversation
    const handleNewMessage = (data: any) => {
      console.log('📨 Received new message:', data);
      
      // Don't add message if it's from current user (already added optimistically)
      if (data.sender.id === user.id) {
        console.log('Ignoring own message from WebSocket');
        return;
      }

      const newMessage: MessageData = {
        id: data.id.toString(),
        sender: data.sender.name,
        senderId: data.sender.id,
        content: data.content,
        timestamp: data.timestamp,
        createdAt: data.created_at,
        isSystemMessage: data.type === 'system' || data.type === 'announcement',
        isCurrentUser: false,
      };

      setMessages(prev => [...prev, newMessage]);

      // Scroll to bottom
      setTimeout(() => {
        scrollViewRef.current?.scrollToEnd({ animated: true });
      }, 100);
    };

    websocketService.subscribeToConversation(chatId, handleNewMessage);

    // Subscribe to typing status
    const handleTypingChange = (data: { user_id: number; user_name: string; is_typing: boolean }) => {
      console.log('⌨️ Typing status changed:', data);
      
      // Ignore own typing
      if (data.user_id === user.id) {
        return;
      }

      setTypingUsers(prev => {
        const newSet = new Set(prev);
        if (data.is_typing) {
          newSet.add(data.user_name);
        } else {
          newSet.delete(data.user_name);
        }
        return newSet;
      });
    };

    websocketService.subscribeToTyping(chatId, handleTypingChange);

    // Cleanup on unmount
    return () => {
      console.log('🔕 Unsubscribing from conversation');
      websocketService.unsubscribeFromConversation(chatId);
      websocketService.unsubscribeFromTyping(chatId);
    };
  }, [chatId, user, session]);

  // Scroll to bottom when messages change
  useEffect(() => {
    if (!isLoadingMessages && messages.length > 0) {
      setTimeout(() => {
        scrollViewRef.current?.scrollToEnd({ animated: false });
      }, 100);
    }
  }, [messages, isLoadingMessages]);

  // Handler for leaving a circle
  const handleLeaveCircle = () => {
    Toast.show({
      type: 'info',
      text1: 'Leave Circle',
      text2: 'This feature will be available soon.',
    });
    setShowCircleInfo(false);
    // TODO: Implement leave circle API call
    // After successful leave, navigate back to chat list
    // router.navigate('/(app)/(tabs)/(chat)');
  };

  // Handler for deleting a circle (owner only)
  const handleDeleteCircle = () => {
    Toast.show({
      type: 'info',
      text1: 'Delete Circle',
      text2: 'This feature will be available soon.',
    });
    setShowCircleInfo(false);
    // TODO: Implement delete circle API call
    // After successful delete, navigate back to chat list
    // router.navigate('/(app)/(tabs)/(chat)');
  };

  return (
    <>
      {/* Header - Outside KeyboardAvoidingView */}
      <View className="mt-8 pt-12 pb-4 px-5 flex-row items-center justify-between" style={{ backgroundColor: colours.background }}>
        <View className="flex-row items-center flex-1">
          <Pressable 
            onPress={() => {
              // Use dismiss() instead of back() to preserve tab state
              // See: mobile/docs/NAVIGATION_ARCHITECTURE.md
              if (router.canDismiss()) {
                router.dismiss();
              } else {
                router.navigate('/(app)/(tabs)/(chat)');
              }
            }}
            className="mr-4"
          >
            <ChevronLeft size={28} color={colours.text} />
          </Pressable>
          
          {/* Avatar */}
          <View 
            className="w-10 h-10 rounded-full items-center justify-center mr-3"
            style={{ backgroundColor: getAvatarColor(chatName) }}
          >
            <Text className="text-white font-semibold text-sm">
              {getInitials(chatName)}
            </Text>
          </View>
          
          <Text className="text-lg font-semibold flex-1" style={{ color: colours.text }}>
            {chatName}
          </Text>
        </View>
        
        <Pressable onPress={() => setShowCircleInfo(true)}>
          <Info size={24} color={colours.text} />
        </Pressable>
      </View>

      {/* Separator line between header and messages */}
      <View style={{ height: 1, backgroundColor: colours.border }} />

      <KeyboardAvoidingView 
        className="flex-1"
        style={{ backgroundColor: colours.chatBackground }}
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        keyboardVerticalOffset={Platform.OS === 'ios' ? 90 : 0}
      >
      {/* Messages */}
      <ScrollView 
        ref={scrollViewRef}
        className="flex-1"
        style={{ backgroundColor: colours.chatBackground }}
        contentContainerStyle={{ paddingBottom: 10, paddingTop: 10 }}
        onScroll={handleScroll}
        scrollEventThrottle={400}
      >
        {/* Loading indicator for pagination at top */}
        {isLoadingMore && (
          <View className="py-3 items-center">
            <ActivityIndicator size="small" color={colours.primary} />
            <Text 
              className="mt-2 text-xs" 
              style={{ color: colours.secondaryText }}
            >
              Loading more messages...
            </Text>
          </View>
        )}

        {isLoadingMessages ? (
          <View className="flex-1 justify-center items-center py-20">
            <ActivityIndicator size="large" color={colours.primary} />
            <Text 
              className="mt-4 text-sm" 
              style={{ color: colours.secondaryText }}
            >
              Loading messages...
            </Text>
          </View>
        ) : messages.length === 0 ? (
          <View className="flex-1 justify-center items-center py-20">
            <Text 
              className="text-sm" 
              style={{ color: colours.secondaryText }}
            >
              No messages yet. Start the conversation!
            </Text>
          </View>
        ) : (
          messages.map((message, index) => {
            const prevMessage = messages[index - 1];
            const showDateSeparator = 
              index === 0 || 
              (message.createdAt && prevMessage?.createdAt && 
               isDifferentDay(message.createdAt, prevMessage.createdAt));
            
            return (
              <React.Fragment key={message.id}>
                {showDateSeparator && message.createdAt && (
                  <DateSeparator 
                    date={formatDateSeparator(new Date(message.createdAt))} 
                    colours={colours} 
                  />
                )}
                <ChatMessage message={message} />
              </React.Fragment>
            );
          })
        )}
      </ScrollView>

      {/* Typing Indicator */}
      {renderTypingIndicator()}

      {/* Separator line between messages and input */}
      <View style={{ height: 1, backgroundColor: colours.border }} />

      {/* Input Bar */}
      <View className="px-4 pb-6 pt-3" style={{ backgroundColor: colours.background }}>
        {/* Expanded Actions */}
        {showInputActions && (
          <View className="mb-3 px-4 py-4 rounded-2xl" style={{ backgroundColor: colours.card }}>
            <Pressable 
              className="absolute top-3 right-3 z-10"
              onPress={() => setShowInputActions(false)}
            >
              <X size={20} color={colours.secondaryText} />
            </Pressable>
            <View className="flex-row justify-around mt-4">
              <Pressable className="items-center" onPress={() => console.log('Poll')}>
                <View className="w-14 h-14 rounded-full items-center justify-center mb-2" style={{ backgroundColor: colours.surface }}>
                  <BarChart3 size={24} color="#B8936F" />
                </View>
                <Text className="text-xs font-semibold" style={{ color: colours.secondaryText }}>POLL</Text>
              </Pressable>
              <Pressable className="items-center" onPress={() => console.log('Plan')}>
                <View className="w-14 h-14 rounded-full items-center justify-center mb-2" style={{ backgroundColor: colours.surface }}>
                  <Calendar size={24} color={colours.primary} />
                </View>
                <Text className="text-xs font-semibold" style={{ color: colours.secondaryText }}>PLAN</Text>
              </Pressable>
              <Pressable className="items-center" onPress={() => console.log('Photo')}>
                <View className="w-14 h-14 rounded-full items-center justify-center mb-2" style={{ backgroundColor: colours.surface }}>
                  <Image size={24} color={colours.info} />
                </View>
                <Text className="text-xs font-semibold" style={{ color: colours.secondaryText }}>PHOTO</Text>
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
              <X size={24} color={colours.secondaryText} />
            ) : (
              <Plus size={24} color={colours.secondaryText} />
            )}
          </Pressable>
          
          <View className="flex-1 rounded-full px-4 py-3 flex-row items-center" style={{ backgroundColor: colours.card }}>
            <TextInput
              className="flex-1 text-base"
              placeholder="Type something..."
              placeholderTextColor={colours.secondaryText}
              value={messageText}
              onChangeText={handleTextChange}
              onKeyPress={handleKeyPress}
              blurOnSubmit={false}
              multiline
              style={{ color: colours.text }}
            />
            <Pressable 
              className="ml-2"
              onPress={() => console.log('Voice message')}
            >
              <Mic size={20} color={colours.secondaryText} />
            </Pressable>
          </View>
          
          {messageText.trim() ? (
            <Pressable 
              className="ml-3 w-10 h-10 rounded-full items-center justify-center"
              style={{ 
                backgroundColor: isSending ? colours.secondaryText : colours.primary,
                opacity: isSending ? 0.5 : 1,
              }}
              onPress={handleSendMessage}
              disabled={isSending}
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
      circleId={circleId || chatId}
      conversationId={chatId}
      isOwner={isOwner}
      onLeave={handleLeaveCircle}
      onDelete={handleDeleteCircle}
    />
  </>
  );
};

export default ChatDetail;
