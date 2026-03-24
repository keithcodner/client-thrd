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

/**
 * Create a new circle
 * 
 * Creates a new circle with the specified configuration. The authenticated user
 * becomes the owner and is automatically added as the first member.
 * 
 * @param circleData - Object containing circle name, description, privacy settings, etc.
 * @returns Response object containing the created circle data
 * @throws Error if circle creation fails or validation errors occur
 * 
 * @example
 * ```typescript
 * const newCircle = await createCircle({
 *   name: "Book Club",
 *   description: "Monthly book discussions",
 *   isPrivate: true,
 *   style_code: "sage"
 * });
 * console.log(newCircle.circle.id); // Circle ID
 * ```
 */
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
 * 
 * Fetches all circles where the authenticated user is an active member.
 * Implements cache-first strategy: returns cached data immediately if available,
 * then refreshes in the background for next time.
 * 
 * @param useCache - Whether to use cached data (default: true)
 * @returns Object containing circles array with circle details
 * @throws Error if the API request fails
 * 
 * @example
 * ```typescript
 * // Use cache (recommended for better performance)
 * const { circles } = await getUserCircleData();
 * 
 * // Force fresh data from API
 * const { circles } = await getUserCircleData(false);
 * ```
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

/**
 * Send a message to a conversation
 * 
 * Sends a text message to a circle chat or 1-to-1 conversation.
 * Invalidates the message cache for the conversation to ensure fresh data.
 * 
 * @param messageData - Object containing conversation_id, content, and optional type/end_user_id
 * @returns Response object containing the created message data
 * @throws Error if message sending fails or validation errors occur
 * 
 * @example
 * ```typescript
 * // Send a regular chat message
 * const result = await sendMessage({
 *   conversation_id: 123,
 *   content: "Hello everyone!",
 *   type: "chat"
 * });
 * 
 * // Send a system announcement
 * const announcement = await sendMessage({
 *   conversation_id: 123,
 *   content: "Meeting starts in 5 minutes",
 *   type: "announcement"
 * });
 * ```
 */
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
 * Searches for users by name or email to invite to a circle.
 * Returns users that are not already members or have pending invites.
 * 
 * @param query - Search query string (name or email)
 * @returns Array of users matching the search query
 * @throws Error if the search request fails
 * 
 * @example
 * ```typescript
 * const users = await searchUsersForInvite("john");
 * console.log(users); // [{ id: 1, name: "John Doe", email: "john@example.com" }]
 * ```
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
 * Creates a pending circle invitation for the specified user.
 * The invited user will receive a notification and can accept or deny the invite.
 * 
 * @param circleId - Circle ID to invite the user to
 * @param invitedUserId - User ID of the person being invited
 * @returns Response object with success status and message
 * @throws Error if user is already a member, invite already exists, or request fails
 * 
 * @example
 * ```typescript
 * try {
 *   const result = await sendCircleInvite(5, 123);
 *   console.log(result.message); // "Circle invite sent successfully."
 * } catch (error) {
 *   console.error("Failed to send invite:", error);
 * }
 * ```
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
 * Accepts a pending circle invitation. The user will be added as a member
 * of the circle and gain access to the circle's chat and features.
 * 
 * @param requestId - Circle request ID (from CircleRequest table)
 * @returns Response object with success status and message
 * @throws Error if the request fails, request ID is invalid, or invite already processed
 * 
 * @example
 * ```typescript
 * try {
 *   const result = await acceptCircleInvite(123);
 *   console.log(result.message); // "Circle invite accepted successfully."
 * } catch (error) {
 *   console.error("Failed to accept invite:", error);
 * }
 * ```
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
 * Rejects a pending circle invitation. The invitation will be marked as declined
 * and the inviting user will be notified of the rejection.
 * 
 * @param requestId - Circle request ID (from CircleRequest table)
 * @returns Response object with success status and message
 * @throws Error if the request fails or request ID is invalid
 * 
 * @example
 * ```typescript
 * try {
 *   const result = await denyCircleInvite(123);
 *   console.log(result.message); // "Circle invite declined successfully."
 * } catch (error) {
 *   console.error("Failed to deny invite:", error);
 * }
 * ```
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
 * 
 * Retrieves a list of user IDs who have pending invitations to the specified circle.
 * Only members of the circle can view pending invites.
 * 
 * @param circleId - Circle ID to check for pending invites
 * @returns Response object containing pending_user_ids array
 * @throws Error if user is not a member of the circle or request fails
 * 
 * @example
 * ```typescript
 * const result = await getPendingCircleInvites(5);
 * console.log(result.pending_user_ids); // [123, 456, 789]
 * ```
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
 * 
 * Broadcasts the user's typing status to other members of the conversation via WebSocket.
 * Automatically managed by the chat UI - sends true when typing starts and false when stopped.
 * Errors are logged but not thrown since typing status is non-critical.
 * 
 * @param conversationId - The conversation ID to broadcast typing status to
 * @param isTyping - Whether the user is currently typing (true) or stopped (false)
 * @returns Promise that resolves when status is updated (errors are caught internally)
 * 
 * @example
 * ```typescript
 * // Start typing
 * await updateTypingStatus(123, true);
 * 
 * // Stop typing after 3 seconds of inactivity
 * setTimeout(() => updateTypingStatus(123, false), 3000);
 * ```
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
 * Get unread message counts for all user's conversations
 * 
 * Fetches the total number of unread messages and per-conversation unread counts.
 * Used for displaying badge counts on the chat tab and individual chat items.
 * 
 * @returns Object containing total unread count and unread counts by conversation
 * @throws Error if the request fails
 * 
 * @example
 * ```typescript
 * const { total_unread, unread_by_conversation } = await getUnreadMessageCounts();
 * console.log(`You have ${total_unread} unread messages`);
 * console.log(unread_by_conversation); // { 123: 5, 456: 2 }
 * ```
 */
export const getUnreadMessageCounts = async () => {
  try {
    const response = await axiosInstance.get("/unread-message-counts");
    return response.data;
  } catch (error) {
    console.error("Error fetching unread message counts:", error);
    throw error;
  }
};

/**
 * Mark messages as read for a conversation
 * 
 * Marks all unread messages in a conversation as read when the user views the chat.
 * This updates the unread badge counts.
 * 
 * @param conversationId - The conversation ID to mark messages as read
 * @returns Response object with success status and updated count
 * @throws Error if the request fails
 * 
 * @example
 * ```typescript
 * await markMessagesAsRead(123);
 * ```
 */
export const markMessagesAsRead = async (conversationId: number) => {
  try {
    const response = await axiosInstance.post("/mark-messages-read", {
      conversation_id: conversationId,
    });
    return response.data;
  } catch (error) {
    console.error("Error marking messages as read:", error);
    // Don't throw - this is not critical
  }
};

/**
 * Get all members of a circle
 * 
 * Retrieves all active members of the specified circle with their user information.
 * Only members of the circle can view the member list.
 * 
 * @param circleId - The circle ID to fetch members for
 * @returns Array of circle members with id, name, email, type (owner/member), and joined_at
 * @throws Error if user is not a member of the circle or request fails
 * 
 * @example
 * ```typescript
 * const members = await getCircleMembers(5);
 * members.forEach(member => {
 *   console.log(`${member.name} (${member.type})`);
 *   // Output: "John Doe (owner)", "Jane Smith (member)"
 * });
 * ```
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