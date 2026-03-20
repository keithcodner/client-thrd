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