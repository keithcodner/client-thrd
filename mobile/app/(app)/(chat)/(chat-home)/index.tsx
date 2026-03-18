import React, { useState } from "react";
import { View, Text, ScrollView, TextInput, Pressable } from "react-native";
import { Search, SlidersHorizontal } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ChatListItem, ChatItemData } from "@/components/chat/ChatListItem";
import { FAB } from "@/components/FAB";
import { CreateCircleModal } from "@/components/app/CreateCircleModal";

// Dummy chat data
const DUMMY_CHATS: ChatItemData[] = [
  {
    id: '1',
    name: 'THRD',
    lastMessage: 'If things start feeling like... a lot, Mind Spa...',
    timestamp: '12:57 AM',
    unread: true,
  },
  {
    id: '2',
    name: 'test',
    lastMessage: 'Start a conversation',
    timestamp: '',
    unread: false,
  },
];

const ChatHome = () => {
  const colours = useThemeColours();
  const [showCreateCircleModal, setShowCreateCircleModal] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  const handleCreateCircle = () => {
    setShowCreateCircleModal(true);
  };

  const handleCircleSubmit = async (circleData: any) => {
    try {
      // TODO: API call to create circle
      console.log('Creating circle:', circleData);
      setShowCreateCircleModal(false);
    } catch (error) {
      console.error('Error creating circle:', error);
    }
  };

  const filteredChats = searchQuery
    ? DUMMY_CHATS.filter(chat =>
        chat.name.toLowerCase().includes(searchQuery.toLowerCase())
      )
    : DUMMY_CHATS;

  return (
    <>
      <View className="flex-1 bg-white dark:bg-gray-900">
        {/* Header */}
        <View className="px-4 pt-12 pb-4">
          <Text className="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Chats
          </Text>

          {/* Search Bar */}
          <View className="flex-row items-center">
            <View className="flex-1 flex-row items-center bg-gray-100 dark:bg-gray-800 rounded-full px-4 py-2 mr-2">
              <Search size={20} color={colours.secondaryText} />
              <TextInput
                className="flex-1 ml-2 text-gray-900 dark:text-white"
                placeholder="Search"
                placeholderTextColor={colours.secondaryText}
                value={searchQuery}
                onChangeText={setSearchQuery}
              />
            </View>
            <Pressable
              className="w-10 h-10 items-center justify-center bg-gray-100 dark:bg-gray-800 rounded-full"
              onPress={() => console.log('Open filter')}
            >
              <SlidersHorizontal size={20} color={colours.secondaryText} />
            </Pressable>
          </View>
        </View>

        {/* Messages Section */}
        <View className="px-4 py-2">
          <Text className="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
            MESSAGES
          </Text>
        </View>

        {/* Chat List */}
        <ScrollView className="flex-1">
          {filteredChats.map(chat => (
            <ChatListItem key={chat.id} chat={chat} />
          ))}
        </ScrollView>

        {/* FAB */}
        <FAB
          colors={colours}
          actions={[
            {
              id: 'create-circle',
              label: 'Create Circle',
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
      />
    </>
  );
};

export default ChatHome;
