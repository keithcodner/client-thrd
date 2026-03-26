import React, { useState, useEffect, useCallback } from "react";
import { useRouter, useFocusEffect } from "expo-router";
import { useSession } from "@/context/AuthContext";
import { useProfileOverlay } from "@/context/ProfileOverlayContext";
import Home  from "./home";
import axiosInstance from "@/config/axiosConfig";
import { CreateCircleModal } from "@/components/app/CreateCircleModal";
import { createCircle, getUserCircleData } from "@/services/chatService";
import { getUnreadCount, notificationWebSocket } from "@/services/notificationService";
import Toast from "react-native-toast-message";

const HomeScreen = () => {
  const router = useRouter();
  const { user: currentUser } = useSession();
  const { openProfileOverlay } = useProfileOverlay();
  
  const [spaces, setSpaces] = useState<any[]>([]);
  const [groups, setGroups] = useState<any[]>([]);
  const [todos, setTodos] = useState<any[]>([]);
  const [notificationsCount, setNotificationsCount] = useState(0);
  const [isLoading, setIsLoading] = useState(true);
  const [showCreateCircleModal, setShowCreateCircleModal] = useState(false);
  const [isCreatingCircle, setIsCreatingCircle] = useState(false);

  // Refresh circles silently (no loading spinner — called on every focus)
  const refreshCircles = useCallback(async () => {
    try {
      const circlesRes = await getUserCircleData(false).catch(() => ({ circles: [] }));
      const userCircles = circlesRes.circles || [];
      const thrdChat = {
        id: '1',
        conversation_id: '1',
        name: 'THRD',
        description: 'System updates and announcements',
        customization: { headerBanner: null },
        isPinned: true,
      };
      const formattedCircles = [
        thrdChat,
        ...userCircles.slice(0, 9).map((circle: any) => ({
          id: circle.id?.toString() || '',
          conversation_id: circle.conversation_id?.toString() || circle.id?.toString() || '',
          name: circle.name || 'Unnamed Circle',
          description: circle.description || '',
          customization: { headerBanner: circle.customization?.headerBanner || null },
          isPinned: false,
        })),
      ];
      setGroups(formattedCircles);
    } catch {
      // fail silently — existing circles remain shown
    }
  }, []);

  // Fetch notification count
  const fetchNotificationCount = async () => {
    try {
      const count = await getUnreadCount();
      setNotificationsCount(count);
    } catch (error) {
      console.error('Error fetching notification count:', error);
      setNotificationsCount(0);
    }
  };

  // Fetch home feed data
  useEffect(() => {
    const fetchHomeData = async () => {
      try {
        setIsLoading(true);
        
        // Fetch spaces, circles, and todos in parallel
        const [spacesRes, circlesRes, todosRes] = await Promise.all([
          axiosInstance.get('/spaces').catch(() => ({ data: [] })),
          getUserCircleData(false).catch(() => ({ circles: [] })),
          axiosInstance.get('/todos?per_page=4').catch(() => ({ data: [] })),
        ]);

        setSpaces(spacesRes.data.data || spacesRes.data || []);

        // Format circles from the already-fetched circlesRes
        const userCircles = circlesRes.circles || [];
        const thrdChat = {
          id: '1',
          conversation_id: '1',
          name: 'THRD',
          description: 'System updates and announcements',
          customization: { headerBanner: null },
          isPinned: true,
        };
        setGroups([
          thrdChat,
          ...userCircles.slice(0, 9).map((circle: any) => ({
            id: circle.id?.toString() || '',
            conversation_id: circle.conversation_id?.toString() || circle.id?.toString() || '',
            name: circle.name || 'Unnamed Circle',
            description: circle.description || '',
            customization: { headerBanner: circle.customization?.headerBanner || null },
            isPinned: false,
          })),
        ]);

        setTodos(todosRes.data.data || todosRes.data || []);
        
        // Fetch notification count
        await fetchNotificationCount();
      } catch (error) {
        console.error('Error fetching home data:', error);
      } finally {
        setIsLoading(false);
      }
    };

    if (currentUser) {
      fetchHomeData();
    }
  }, [currentUser]);

  // Refresh circles + notification count every time this tab is focused
  useFocusEffect(
    React.useCallback(() => {
      if (currentUser) {
        refreshCircles();
        fetchNotificationCount();
      }
    }, [currentUser, refreshCircles])
  );

  // Listen for real-time notifications and update count
  useEffect(() => {
    if (!currentUser) return;

    // Initialize WebSocket
    notificationWebSocket.initialize();

    // Subscribe to new notifications
    const unsubscribe = notificationWebSocket.onNotification((notification) => {
      console.log('📬 New notification received on home screen');
      // Increment count
      setNotificationsCount((prev) => prev + 1);
    });

    // Cleanup
    return () => {
      unsubscribe();
    };
  }, [currentUser]);

  const handleNavigate = (screen: string) => {
    router.push(`/(app)/(tabs)/(chat)`);
  };

  const handleOpenProfile = () => {
    openProfileOverlay();
  };

  const handleSelectGroup = (groupId: string) => {
    // Find the group to get its conversation_id
    const group = groups.find((g: any) => g.id === groupId);
    const conversationId = group?.conversation_id || groupId;
    router.push(`/(app)/(tabs)/(chat)/${conversationId}`);
  };

  const handleSelectSpace = (spaceId: string) => {
    router.push(`/(app)/(tabs)/(explore)/${spaceId}`);
  };

  const handleOpenNotifications = () => {
    router.push('/(app)/(notifications)');
  };

  const handleAddEvent = () => {
    // TODO: Open calendar modal to add event
    console.log('Add event');
  };

  const handleHostEvent = () => {
    // TODO: Open host event wizard
    console.log('Host event');
  };

  const handleCreateGroup = () => {
    setShowCreateCircleModal(true);
  };

  const handleCreatePost = () => {
    // TODO: Open create post modal
    console.log('Create post');
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

  return (
    <>
      <Home
        currentUser={currentUser}
        spaces={spaces.length > 0 ? spaces : undefined}
        groups={groups.length > 0 ? groups : undefined}
        todos={todos.length > 0 ? todos : undefined}
        notificationsCount={notificationsCount}
        onNavigate={handleNavigate}
        onOpenProfile={handleOpenProfile}
        onSelectGroup={handleSelectGroup}
        onSelectSpace={handleSelectSpace}
        onOpenNotifications={handleOpenNotifications}
        onAddEvent={handleAddEvent}
        onHostEvent={handleHostEvent}
        onCreateGroup={handleCreateGroup}
        onCreatePost={handleCreatePost}
      />
      
      <CreateCircleModal
        visible={showCreateCircleModal}
        onClose={() => setShowCreateCircleModal(false)}
        onSubmit={handleCircleSubmit}
        isLoading={isCreatingCircle}
      />
    </>
  );
};

export default HomeScreen;
