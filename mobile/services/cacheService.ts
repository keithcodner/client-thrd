/**
 * Cache Service
 * 
 * Manages local caching of circles, conversations, and messages using AsyncStorage.
 * 
 * **SECURITY:** All cache keys are scoped to the current user ID to prevent data leakage between users.
 * 
 * **Caching Strategy:**
 * - Circles: Cached indefinitely, refreshed on app launch
 * - Conversations: Cached indefinitely, refreshed on app launch
 * - Messages: Only the initial load (30 messages) is cached per conversation
 * - Paginated messages (loaded on scroll): NOT cached to save storage
 * 
 * **Cache Keys:**
 * - circles: @chat_cache:user_{userId}:circles
 * - conversations: @chat_cache:user_{userId}:conversations
 * - messages: @chat_cache:user_{userId}:messages:{conversationId}
 * 
 * **Storage Limits:**
 * - iOS: No practical limit (100+ MB easily)
 * - Android: Default 6MB, can be increased in native config
 */

import AsyncStorage from '@react-native-async-storage/async-storage';
import { storage } from '@/utils/storage';

/**
 * Get the current user ID from secure storage
 * CRITICAL: This ensures cache is isolated per user
 */
const getCurrentUserId = async (): Promise<string | null> => {
  try {
    const userJson = await storage.getItem('user');
    if (!userJson) return null;
    
    const user = JSON.parse(userJson);
    return user?.id?.toString() || null;
  } catch (error) {
    console.error('Error getting current user ID:', error);
    return null;
  }
};

/**
 * Generate cache keys scoped to the current user
 */
const getCacheKeys = async () => {
  const userId = await getCurrentUserId();
  if (!userId) {
    throw new Error('Cannot access cache: No user logged in');
  }

  return {
    CIRCLES: `@chat_cache:user_${userId}:circles`,
    CONVERSATIONS: `@chat_cache:user_${userId}:conversations`,
    MESSAGES: `@chat_cache:user_${userId}:messages:`, // Will append conversation ID
  };
};

const CACHE_EXPIRY = {
  CIRCLES: 24 * 60 * 60 * 1000, // 24 hours
  CONVERSATIONS: 24 * 60 * 60 * 1000, // 24 hours
  MESSAGES: 1 * 60 * 60 * 1000, // 1 hour
};

interface CacheData<T> {
  data: T;
  timestamp: number;
}

/**
 * Cache circles data
 */
export const cacheCircles = async (circles: any[]): Promise<void> => {
  try {
    const CACHE_KEYS = await getCacheKeys();
    const cacheData: CacheData<any[]> = {
      data: circles,
      timestamp: Date.now(),
    };
    await AsyncStorage.setItem(CACHE_KEYS.CIRCLES, JSON.stringify(cacheData));
    const userId = await getCurrentUserId();
    console.log('✅ Circles cached successfully', { userId, count: circles.length });
  } catch (error) {
    console.error('Error caching circles:', error);
  }
};

/**
 * Get cached circles
 * Returns null if cache is expired or doesn't exist
 */
export const getCachedCircles = async (): Promise<any[] | null> => {
  try {
    const CACHE_KEYS = await getCacheKeys();
    const cached = await AsyncStorage.getItem(CACHE_KEYS.CIRCLES);
    if (!cached) return null;

    const cacheData: CacheData<any[]> = JSON.parse(cached);
    const age = Date.now() - cacheData.timestamp;

    if (age > CACHE_EXPIRY.CIRCLES) {
      const userId = await getCurrentUserId();
      console.log('⏰ Circles cache expired', { userId });
      return null;
    }

    const userId = await getCurrentUserId();
    console.log('✅ Retrieved cached circles', { 
      userId,
      count: cacheData.data.length,
      age: Math.round(age / 1000) + 's' 
    });
    return cacheData.data;
  } catch (error) {
    console.error('Error getting cached circles:', error);
    return null;
  }
};

/**
 * Cache conversations data
 */
export const cacheConversations = async (conversations: any[]): Promise<void> => {
  try {
    const CACHE_KEYS = await getCacheKeys();
    const cacheData: CacheData<any[]> = {
      data: conversations,
      timestamp: Date.now(),
    };
    await AsyncStorage.setItem(CACHE_KEYS.CONVERSATIONS, JSON.stringify(cacheData));
    const userId = await getCurrentUserId();
    console.log('✅ Conversations cached successfully', { userId, count: conversations.length });
  } catch (error) {
    console.error('Error caching conversations:', error);
  }
};

/**
 * Get cached conversations
 * Returns null if cache is expired or doesn't exist
 */
export const getCachedConversations = async (): Promise<any[] | null> => {
  try {
    const CACHE_KEYS = await getCacheKeys();
    const cached = await AsyncStorage.getItem(CACHE_KEYS.CONVERSATIONS);
    if (!cached) return null;

    const cacheData: CacheData<any[]> = JSON.parse(cached);
    const age = Date.now() - cacheData.timestamp;

    if (age > CACHE_EXPIRY.CONVERSATIONS) {
      const userId = await getCurrentUserId();
      console.log('⏰ Conversations cache expired', { userId });
      return null;
    }

    const userId = await getCurrentUserId();
    console.log('✅ Retrieved cached conversations', { 
      userId,
      count: cacheData.data.length,
      age: Math.round(age / 1000) + 's' 
    });
    return cacheData.data;
  } catch (error) {
    console.error('Error getting cached conversations:', error);
    return null;
  }
};

/**
 * Cache messages for a specific conversation
 * ONLY caches the initial load (30 messages)
 * Paginated messages (loaded on scroll) are NOT cached
 */
export const cacheMessages = async (conversationId: number, messages: any[]): Promise<void> => {
  try {
    const CACHE_KEYS = await getCacheKeys();
    const cacheData: CacheData<any[]> = {
      data: messages,
      timestamp: Date.now(),
    };
    const key = `${CACHE_KEYS.MESSAGES}${conversationId}`;
    await AsyncStorage.setItem(key, JSON.stringify(cacheData));
    const userId = await getCurrentUserId();
    console.log('✅ Messages cached successfully', { 
      userId,
      conversationId, 
      count: messages.length 
    });
  } catch (error) {
    console.error('Error caching messages:', error);
  }
};

/**
 * Get cached messages for a specific conversation
 * Returns null if cache is expired or doesn't exist
 */
export const getCachedMessages = async (conversationId: number): Promise<any[] | null> => {
  try {
    const CACHE_KEYS = await getCacheKeys();
    const key = `${CACHE_KEYS.MESSAGES}${conversationId}`;
    const cached = await AsyncStorage.getItem(key);
    if (!cached) return null;

    const cacheData: CacheData<any[]> = JSON.parse(cached);
    const age = Date.now() - cacheData.timestamp;

    if (age > CACHE_EXPIRY.MESSAGES) {
      const userId = await getCurrentUserId();
      console.log('⏰ Messages cache expired', { userId, conversationId });
      return null;
    }

    const userId = await getCurrentUserId();
    console.log('✅ Retrieved cached messages', { 
      userId,
      conversationId,
      count: cacheData.data.length,
      age: Math.round(age / 1000) + 's' 
    });
    return cacheData.data;
  } catch (error) {
    console.error('Error getting cached messages:', error);
    return null;
  }
};

/**
 * Invalidate (clear) messages cache for a specific conversation
 * Used when new messages are sent
 */
export const invalidateMessagesCache = async (conversationId: number): Promise<void> => {
  try {
    const CACHE_KEYS = await getCacheKeys();
    const key = `${CACHE_KEYS.MESSAGES}${conversationId}`;
    await AsyncStorage.removeItem(key);
    const userId = await getCurrentUserId();
    console.log('🗑️ Messages cache invalidated', { userId, conversationId });
  } catch (error) {
    console.error('Error invalidating messages cache:', error);
  }
};

/**
 * Clear all chat caches for the current user
 * CRITICAL: Called on logout and login to prevent data leakage
 */
export const clearAllChatCaches = async (): Promise<void> => {
  try {
    const keys = await AsyncStorage.getAllKeys();
    const chatKeys = keys.filter((key: string) => key.startsWith('@chat_cache:'));
    await AsyncStorage.multiRemove(chatKeys);
    console.log('🗑️ All chat caches cleared (ALL USERS)', { count: chatKeys.length });
  } catch (error) {
    console.error('Error clearing all chat caches:', error);
  }
};

/**
 * Clear chat caches for a specific user
 * Used when logging out to ensure no data persists
 */
export const clearUserChatCaches = async (userId?: string): Promise<void> => {
  try {
    const targetUserId = userId || await getCurrentUserId();
    if (!targetUserId) {
      console.warn('Cannot clear user cache: No user ID provided');
      return;
    }

    const keys = await AsyncStorage.getAllKeys();
    const userCacheKeys = keys.filter((key: string) => 
      key.startsWith(`@chat_cache:user_${targetUserId}:`)
    );
    
    await AsyncStorage.multiRemove(userCacheKeys);
    console.log('🗑️ User chat caches cleared', { userId: targetUserId, count: userCacheKeys.length });
  } catch (error) {
    console.error('Error clearing user chat caches:', error);
  }
};

/**
 * Get cache statistics (for debugging)
 */
export const getCacheStats = async (): Promise<{
  userId: string | null;
  circles: boolean;
  conversations: boolean;
  messagesCount: number;
}> => {
  try {
    const userId = await getCurrentUserId();
    if (!userId) {
      return {
        userId: null,
        circles: false,
        conversations: false,
        messagesCount: 0,
      };
    }

    const CACHE_KEYS = await getCacheKeys();
    const keys = await AsyncStorage.getAllKeys();
    const circlesExists = keys.includes(CACHE_KEYS.CIRCLES);
    const conversationsExists = keys.includes(CACHE_KEYS.CONVERSATIONS);
    const messagesCount = keys.filter((key: string) => key.startsWith(CACHE_KEYS.MESSAGES)).length;

    return {
      userId,
      circles: circlesExists,
      conversations: conversationsExists,
      messagesCount,
    };
  } catch (error) {
    console.error('Error getting cache stats:', error);
    return {
      userId: null,
      circles: false,
      conversations: false,
      messagesCount: 0,
    };
  }
};
