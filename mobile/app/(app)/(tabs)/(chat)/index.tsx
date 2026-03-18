import React, { useState } from "react";
import { View, Text, ScrollView, TextInput, Pressable } from "react-native";
import { Search, SlidersHorizontal } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";
import { ChatListItem, ChatItemData } from "@/components/chat/ChatListItem";
import { FAB } from "@/components/FAB";
import { CreateCircleModal } from "@/components/app/CreateCircleModal";
import { createCircle } from "@/services/chatService";
import Toast from "react-native-toast-message";

// Dummy chat data
const DUMMY_CHATS: ChatItemData[] = [
  {
    id: '1',
    name: 'THRD',
    lastMessage: 'test',
    timestamp: '1:49 AM',
    unread: false,
  },
  {
    id: '2',
    name: 'test',
    lastMessage: 'hey',
    timestamp: '1:49 AM',
    unread: false,
  },
];

const ChatHome = () => {
  const colours = useThemeColours();
  const [showCreateCircleModal, setShowCreateCircleModal] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [isCreatingCircle, setIsCreatingCircle] = useState(false);

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

  const filteredChats = searchQuery
    ? DUMMY_CHATS.filter(chat =>
        chat.name.toLowerCase().includes(searchQuery.toLowerCase())
      )
    : DUMMY_CHATS;

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
        <ScrollView className="flex-1" style={{ backgroundColor: colours.background }}>
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
        isLoading={isCreatingCircle}
      />
    </>
  );
};

export default ChatHome;
