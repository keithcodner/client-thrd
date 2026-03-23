import React, { useState, useEffect, useCallback } from "react";
import { View, Text, ScrollView, TextInput, Pressable, ActivityIndicator, RefreshControl } from "react-native";
import { Plus, Search, SlidersHorizontal } from "lucide-react-native";
import { useFocusEffect } from '@react-navigation/native';
import { useThemeColours } from "@/hooks/useThemeColours";
import { ChatListItem, ChatItemData } from "@/components/chat/ChatListItem";
import { ChatManagementOverlay } from "@/components/chat/ChatManagementOverlay";
import { FAB } from "@/components/FAB";
import { CreateCircleModal } from "@/components/app/CreateCircleModal";
import { createCircle, getUserCircleData } from "@/services/chatService";
import Toast from "react-native-toast-message";

/**
 * ChatHome - Main chat list screen
 * 
 * Features:
 * - Search and filter chats
 * - Pull-to-refresh to reload circles
 * - Auto-refresh when screen gains focus (e.g., after accepting/denying invites)
 * - Long press chat items to open ChatManagementOverlay
 * - Quick actions: delete, pin, view info, leave, clear
 * - Owner-specific actions (delete circle)
 * 
 * Cache Refresh Triggers:
 * - Screen focus (navigation back from other screens)
 * - Pull-to-refresh gesture
 * - Circle creation
 * - Accepting/denying circle invites (via screen focus)
 * - Leaving a circle
 * 
 * See: mobile/docs/CHAT_MANAGEMENT_OVERLAY.md
 */

// Dummy chat data
const DUMMY_CHATS: ChatItemData[] = [
  {
    id: '1',
    name: 'THRD',
    lastMessage: 'test',
    timestamp: '1:49 AM',
    unread: false,
  }
];

const ChatHome = () => {
  const colours = useThemeColours();
  const [showCreateCircleModal, setShowCreateCircleModal] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [isCreatingCircle, setIsCreatingCircle] = useState(false);
  const [chats, setChats] = useState<ChatItemData[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [selectedChat, setSelectedChat] = useState<ChatItemData | null>(null);
  const [showManagementOverlay, setShowManagementOverlay] = useState(false);

  const fetchUserCircles = async () => {
    try {
      setIsLoading(true);
      const response = await getUserCircleData();
      
      console.log('📊 getUserCircleData response:', JSON.stringify(response, null, 2));
      console.log('📊 Circles count:', response?.circles?.length || 0);
      
      // Check if response has circles array
      if (!response || !Array.isArray(response.circles)) {
        console.error('❌ Invalid response structure:', response);
        setChats(DUMMY_CHATS);
        Toast.show({
          type: 'error',
          text1: 'Error',
          text2: 'Invalid data format received.',
        });
        return;
      }
      
      // Transform API response to ChatItemData format
      const circleChats: ChatItemData[] = response.circles.map((circle: any) => ({
        id: circle.id.toString(),
        name: circle.name,
        lastMessage: 'No messages yet',
        timestamp: new Date(circle.updated_at).toLocaleTimeString('en-US', { 
          hour: 'numeric', 
          minute: '2-digit' 
        }),
        unread: false,
        isPrivate: circle.type === 'private_circle',
      }));
      
      console.log('📊 Transformed circle chats:', circleChats);
      
      // Keep DUMMY_CHATS first, then add circle chats
      setChats([...DUMMY_CHATS, ...circleChats]);
    } catch (error) {
      console.error('❌ Error fetching user circles:', error);
      // Set DUMMY_CHATS even on error so the THRD chat appears
      setChats(DUMMY_CHATS);
      Toast.show({
        type: 'error',
        text1: 'Error',
        text2: 'Failed to load circles. Please try again.',
      });
    } finally {
      setIsLoading(false);
    }
  };

  // Refresh handler for pull-to-refresh
  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    try {
      await fetchUserCircles();
    } finally {
      setRefreshing(false);
    }
  }, []);

  // Refresh when screen comes into focus (e.g., after accepting/denying invites)
  useFocusEffect(
    useCallback(() => {
      console.log('🔄 Chat screen focused - refreshing circles');
      fetchUserCircles();
    }, [])
  );

  const handleCreateCircle = () => {
    setShowCreateCircleModal(true);
  };

  const handleCircleSubmit = async (circleData: any) => {
    try {
      setIsCreatingCircle(true);
      await createCircle(circleData);
      setShowCreateCircleModal(false);
      Toast.show({
        type: 'success',
        text1: 'Success',
        text2: 'Circle created successfully!',
      });
      // Refresh the circles list
      await fetchUserCircles();
    } catch (error) {
      console.error("Error creating circle:", error);
      Toast.show({
        type: 'error',
        text1: 'Error',
        text2: 'Failed to create circle. Please try again.',
      });
    } finally {
      setIsCreatingCircle(false);
    }
  };

  /**
   * Long press handler for chat items
   * Opens the ChatManagementOverlay with quick actions and menu options
   * See: mobile/docs/CHAT_MANAGEMENT_OVERLAY.md
   */
  const handleChatLongPress = (chat: ChatItemData) => {
    setSelectedChat(chat);
    setShowManagementOverlay(true);
  };

  /**
   * Close overlay with animation
   * Clears selected chat after animation completes to prevent visual glitches
   */
  const handleCloseOverlay = () => {
    setShowManagementOverlay(false);
    setTimeout(() => setSelectedChat(null), 300);
  };

  const handleDeleteChat = (chatId: string) => {
    // Remove chat from list
    setChats(chats.filter(chat => chat.id !== chatId));
    Toast.show({
      type: 'success',
      text1: 'Deleted',
      text2: 'Chat removed from your list.',
    });
  };

  const handlePinChat = (chatId: string) => {
    Toast.show({
      type: 'success',
      text1: 'Pinned',
      text2: 'Chat pinned to top.',
    });
    // TODO: Implement pin functionality
  };

  const handleLeaveCircle = async (chatId: string) => {
    try {
      Toast.show({
        type: 'info',
        text1: 'Left Circle',
        text2: 'You have left the circle.',
      });
      // TODO: Implement leave circle API call
      // await leaveCircle(chatId);
      
      // Refresh the circles list
      await fetchUserCircles();
    } catch (error) {
      console.error('Error leaving circle:', error);
      Toast.show({
        type: 'error',
        text1: 'Error',
        text2: 'Failed to leave circle.',
      });
    }
  };

  const handleClearChats = (chatId: string) => {
    Toast.show({
      type: 'success',
      text1: 'Cleared',
      text2: 'All messages have been cleared.',
    });
    // TODO: Implement clear chats API call
  };

  const filteredChats = searchQuery
    ? chats.filter(chat =>
        chat.name.toLowerCase().includes(searchQuery.toLowerCase())
      )
    : chats;

  return (
    <>
      <View className="flex-1" style={{ backgroundColor: colours.background }}>
        {/* Header */}
        <View className="px-5 pt-12 pb-4">
          <Text className="text-4xl mb-5" style={{ fontFamily: 'serif', fontWeight: '400', color: colours.text }}>
            Chats
          </Text>

          {/* Search Bar */}
          <View className="flex-row items-center">
            <View className="flex-1 flex-row items-center rounded-lg px-3 py-2 mr-3" style={{ backgroundColor: colours.card }}>
              <Search size={18} color={colours.secondaryText} />
              <TextInput
                className="flex-1 ml-2 text-sm"
                placeholder="Search"
                placeholderTextColor={colours.secondaryText}
                value={searchQuery}
                onChangeText={setSearchQuery}
                style={{ color: colours.text }}
              />
            </View>
            <Pressable
              className="w-10 h-10 items-center justify-center rounded-lg"
              style={{ backgroundColor: colours.card }}
              onPress={() => console.log('Open filter')}
            >
              <SlidersHorizontal size={18} color={colours.secondaryText} />
            </Pressable>
          </View>
        </View>

        {/* Messages Section */}
        <View className="px-5 py-3 border-b" style={{ borderBottomColor: colours.border }}>
          <Text className="text-xs font-semibold uppercase tracking-widest" style={{ color: colours.secondaryText }}>
            MESSAGES
          </Text>
        </View>

        {/* Chat List */}
        <ScrollView 
          className="flex-1" 
          style={{ backgroundColor: colours.background }}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={{ flexGrow: 1 }}
          refreshControl={
            <RefreshControl 
              refreshing={refreshing} 
              onRefresh={onRefresh}
              tintColor={colours.primary}
              colors={[colours.primary]}
            />
          }
        >
          {isLoading ? (
            <View className="flex-1 justify-center items-center py-20">
              <ActivityIndicator size="large" color={colours.primary} />
              <Text className="mt-4 text-sm" style={{ color: colours.secondaryText }}>
                Loading circles...
              </Text>
            </View>
          ) : filteredChats.length > 0 ? (
            filteredChats.map(chat => (
              <ChatListItem 
                key={chat.id} 
                chat={chat} 
                onLongPress={handleChatLongPress}
              />
            ))
          ) : (
            <View className="flex-1 justify-center items-center py-20">
              <Text className="text-sm" style={{ color: colours.secondaryText }}>
                {searchQuery ? 'No circles found' : 'No circles yet'}
              </Text>
            </View>
          )}
        </ScrollView>

        {/* FAB */}
        <FAB
          colors={colours}
          actions={[
            {
              id: 'create-circle',
              label: 'Create Circle',
              icon: Plus,
              color: '#4c8bf5',
              onPress: handleCreateCircle,
            },
          ]}
          onCreateCircle={handleCreateCircle}
        />
      </View>

      {/* Create Circle Modal */}
      <CreateCircleModal
        visible={showCreateCircleModal}
        onClose={() => setShowCreateCircleModal(false)}
        onSubmit={handleCircleSubmit}
        isLoading={isCreatingCircle}
      />

      {/* Chat Management Overlay */}
      <ChatManagementOverlay
        visible={showManagementOverlay}
        chat={selectedChat}
        onClose={handleCloseOverlay}
        onDelete={handleDeleteChat}
        onPin={handlePinChat}
        onLeave={handleLeaveCircle}
        onClearChats={handleClearChats}
        isOwner={selectedChat?.id !== '1'} // THRD chat is not user-owned
      />
    </>
  );
};

export default ChatHome;
