/**
 * Cache Service
 * 
 * Manages local caching of circles, conversations, and messages using AsyncStorage.
 * 
 * **Caching Strategy:**
 * - Circles: Cached indefinitely, refreshed on app launch
 * - Conversations: Cached indefinitely, refreshed on app launch
 * - Messages: Only the initial load (30 messages) is cached per conversation
 * - Paginated messages (loaded on scroll): NOT cached to save storage
 * 
 * **Cache Keys:**
 * - circles: @chat_cache:circles
 * - conversations: @chat_cache:conversations
 * - messages: @chat_cache:messages:{conversationId}
 * 
 * **Storage Limits:**
 * - iOS: No practical limit (100+ MB easily)
 * - Android: Default 6MB, can be increased in native config
 */

import AsyncStorage from '@react-native-async-storage/async-storage';

const CACHE_KEYS = {
  CIRCLES: '@chat_cache:circles',
  CONVERSATIONS: '@chat_cache:conversations',
  MESSAGES: '@chat_cache:messages:', // Will append conversation ID
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
    const cacheData: CacheData<any[]> = {
      data: circles,
      timestamp: Date.now(),
    };
    await AsyncStorage.setItem(CACHE_KEYS.CIRCLES, JSON.stringify(cacheData));
    console.log('✅ Circles cached successfully', { count: circles.length });
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
    const cached = await AsyncStorage.getItem(CACHE_KEYS.CIRCLES);
    if (!cached) return null;

    const cacheData: CacheData<any[]> = JSON.parse(cached);
    const age = Date.now() - cacheData.timestamp;

    if (age > CACHE_EXPIRY.CIRCLES) {
      console.log('⏰ Circles cache expired');
      return null;
    }

    console.log('✅ Retrieved cached circles', { 
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
    const cacheData: CacheData<any[]> = {
      data: conversations,
      timestamp: Date.now(),
    };
    await AsyncStorage.setItem(CACHE_KEYS.CONVERSATIONS, JSON.stringify(cacheData));
    console.log('✅ Conversations cached successfully', { count: conversations.length });
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
    const cached = await AsyncStorage.getItem(CACHE_KEYS.CONVERSATIONS);
    if (!cached) return null;

    const cacheData: CacheData<any[]> = JSON.parse(cached);
    const age = Date.now() - cacheData.timestamp;

    if (age > CACHE_EXPIRY.CONVERSATIONS) {
      console.log('⏰ Conversations cache expired');
      return null;
    }

    console.log('✅ Retrieved cached conversations', { 
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
    const cacheData: CacheData<any[]> = {
      data: messages,
      timestamp: Date.now(),
    };
    const key = `${CACHE_KEYS.MESSAGES}${conversationId}`;
    await AsyncStorage.setItem(key, JSON.stringify(cacheData));
    console.log('✅ Messages cached successfully', { 
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
    const key = `${CACHE_KEYS.MESSAGES}${conversationId}`;
    const cached = await AsyncStorage.getItem(key);
    if (!cached) return null;

    const cacheData: CacheData<any[]> = JSON.parse(cached);
    const age = Date.now() - cacheData.timestamp;

    if (age > CACHE_EXPIRY.MESSAGES) {
      console.log('⏰ Messages cache expired', { conversationId });
      return null;
    }

    console.log('✅ Retrieved cached messages', { 
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
    const key = `${CACHE_KEYS.MESSAGES}${conversationId}`;
    await AsyncStorage.removeItem(key);
    console.log('🗑️ Messages cache invalidated', { conversationId });
  } catch (error) {
    console.error('Error invalidating messages cache:', error);
  }
};

/**
 * Clear all chat caches
 * Useful for logout or manual refresh
 */
export const clearAllChatCaches = async (): Promise<void> => {
  try {
    const keys = await AsyncStorage.getAllKeys();
    const chatKeys = keys.filter((key: string) => key.startsWith('@chat_cache:'));
    await AsyncStorage.multiRemove(chatKeys);
    console.log('🗑️ All chat caches cleared', { count: chatKeys.length });
  } catch (error) {
    console.error('Error clearing all chat caches:', error);
  }
};

/**
 * Get cache statistics (for debugging)
 */
export const getCacheStats = async (): Promise<{
  circles: boolean;
  conversations: boolean;
  messagesCount: number;
}> => {
  try {
    const keys = await AsyncStorage.getAllKeys();
    const circlesExists = keys.includes(CACHE_KEYS.CIRCLES);
    const conversationsExists = keys.includes(CACHE_KEYS.CONVERSATIONS);
    const messagesCount = keys.filter((key: string) => key.startsWith(CACHE_KEYS.MESSAGES)).length;

    return {
      circles: circlesExists,
      conversations: conversationsExists,
      messagesCount,
    };
  } catch (error) {
    console.error('Error getting cache stats:', error);
    return {
      circles: false,
      conversations: false,
      messagesCount: 0,
    };
  }
};
