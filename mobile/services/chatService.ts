import axiosInstance from "@/config/axiosConfig";
import {
  cacheCircles,
  getCachedCircles,
  cacheMessages,
  getCachedMessages,
  invalidateMessagesCache,
} from "./cacheService";

export interface SendMessageData {
  conversation_id: number;
  content: string;
  type?: "chat" | "announcement" | "system";
  end_user_id?: number;
}

export const createCircle = async (circleData: any) => {
  try {
    const response = await axiosInstance.post("/create-circle", circleData);
    // Invalidate circles cache when new circle is created
    return response.data;
  } catch (error) {
    console.error("Error creating circle:", error);
    throw error;
  }
};

/**
 * Get user circles with caching
 * Cache-first strategy: Return cached data immediately if available,
 * then refresh in background
 */
export const getUserCircleData = async (useCache: boolean = true) => {
  try {
    // Try to get from cache first
    if (useCache) {
      const cachedCircles = await getCachedCircles();
      if (cachedCircles) {
        // Return cached data immediately
        console.log("📦 Using cached circles");
        
        // Refresh in background (fire and forget)
        axiosInstance.post("/user-circles")
          .then(response => {
            cacheCircles(response.data.circles);
          })
          .catch(err => console.error("Background refresh failed:", err));
        
        return { circles: cachedCircles };
      }
    }

    // No cache or cache disabled, fetch from API
    const response = await axiosInstance.post("/user-circles");
    
    // Cache the fresh data
    if (response.data.circles) {
      await cacheCircles(response.data.circles);
    }
    
    return response.data;
  } catch (error) {
    console.error("Error fetching user circles:", error);
    throw error;
  }
};

export const sendMessage = async (messageData: SendMessageData) => {
  try {
    const response = await axiosInstance.post("/post-chat", messageData);
    
    // Invalidate cache for this conversation since we added a new message
    await invalidateMessagesCache(messageData.conversation_id);
    
    return response.data;
  } catch (error) {
    console.error("Error sending message:", error);
    throw error;
  }
};

/**
 * Get conversation messages with caching
 * ONLY caches the initial load (first 30 messages)
 * Pagination loads (offset > 0) are NOT cached
 * 
 * @param conversationId - The conversation ID to fetch messages for
 * @param limit - Number of messages to fetch (default: 30)
 * @param offset - Offset for pagination (default: 0). NOT cached if offset > 0
 * @param useCache - Whether to use cached data (default: true)
 */
export const getConversationMessages = async (
  conversationId: number,
  limit: number = 30,
  offset: number = 0,
  useCache: boolean = true
) => {
  try {
    // Only use cache for initial load (offset = 0)
    if (useCache && offset === 0) {
      const cachedMessages = await getCachedMessages(conversationId);
      if (cachedMessages) {
        console.log("📦 Using cached messages", { conversationId });
        
        // Refresh in background (fire and forget)
        axiosInstance.post("/chat", {
          conversation_id: conversationId,
          limit,
          offset,
        })
          .then(response => {
            if (response.data.messages) {
              cacheMessages(conversationId, response.data.messages);
            }
          })
          .catch(err => console.error("Background messages refresh failed:", err));
        
        return {
          messages: cachedMessages,
          hasMore: cachedMessages.length >= limit,
        };
      }
    }

    // No cache, cache disabled, or pagination load - fetch from API
    const response = await axiosInstance.post("/chat", {
      conversation_id: conversationId,
      limit,
      offset,
    });
    
    // Only cache initial load (offset = 0)
    if (response.data.messages && offset === 0) {
      await cacheMessages(conversationId, response.data.messages);
    }
    
    return {
      messages: response.data.messages || [],
      hasMore: response.data.messages?.length >= limit,
    };
  } catch (error) {
    console.error("Error fetching conversation messages:", error);
    throw error;
  }
};

/**
 * Search users for circle invites
 * 
 * @param query - Search query string
 * @returns Array of users matching the search query
 */
export const searchUsersForInvite = async (query: string) => {
  try {
    const response = await axiosInstance.post("/search-users", {
      query: query.trim(),
    });
    
    return response.data.users || [];
  } catch (error) {
    console.error("Error searching users for invite:", error);
    throw error;
  }
};

/**
 * Send a circle invite to a user
 * 
 * @param circleId - Circle ID
 * @param invitedUserId - User ID of the person being invited
 * @returns Response from the API
 */
export const sendCircleInvite = async (circleId: number, invitedUserId: number) => {
  try {
    const response = await axiosInstance.post("/send-circle-invite", {
      circle_id: circleId,
      invited_user_id: invitedUserId,
    });
    
    return response.data;
  } catch (error) {
    console.error("Error sending circle invite:", error);
    throw error;
  }
};

/**
 * Accept a circle invite
 * 
 * @param requestId - Circle request ID
 * @returns Response from the API
 */
export const acceptCircleInvite = async (requestId: number) => {
  try {
    const response = await axiosInstance.post("/accept-circle-invite", {
      request_id: requestId,
    });
    
    return response.data;
  } catch (error) {
    console.error("Error accepting circle invite:", error);
    throw error;
  }
};

/**
 * Deny a circle invite
 * 
 * @param requestId - Circle request ID
 * @returns Response from the API
 */
export const denyCircleInvite = async (requestId: number) => {
  try {
    const response = await axiosInstance.post("/deny-circle-invite", {
      request_id: requestId,
    });
    
    return response.data;
  } catch (error) {
    console.error("Error denying circle invite:", error);
    throw error;
  }
};

/**
 * Get pending circle invites for a circle
 * Returns array of user IDs that have pending invites
 * 
 * @param circleId - Circle ID
 * @returns Response with pending_user_ids array
 */
export const getPendingCircleInvites = async (circleId: number) => {
  try {
    const response = await axiosInstance.post("/get-pending-circle-invites", {
      circle_id: circleId,
    });
    
    return response.data;
  } catch (error) {
    console.error("Error fetching pending circle invites:", error);
    throw error;
  }
};

/**
 * Update typing status for a conversation
 * Broadcasts to other users that the current user is typing or stopped typing
 * 
 * @param conversationId - The conversation ID
 * @param isTyping - Whether the user is currently typing
 */
export const updateTypingStatus = async (conversationId: number, isTyping: boolean) => {
  try {
    await axiosInstance.post("/typing-status", {
      conversation_id: conversationId,
      is_typing: isTyping,
    });
  } catch (error) {
    console.error("Error updating typing status:", error);
    // Don't throw error - typing status is not critical
  }
};

/**
 * Get all members of a circle
 * 
 * @param circleId - The circle ID
 * @returns Array of circle members with user information
 */
export const getCircleMembers = async (circleId: number) => {
  try {
    const response = await axiosInstance.post("/get-circle-members", {
      circle_id: circleId,
    });
    
    return response.data.members || [];
  } catch (error) {
    console.error("Error fetching circle members:", error);
    throw error;
  }
};