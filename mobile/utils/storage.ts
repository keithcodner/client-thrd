import * as SecureStore from "expo-secure-store";
import { Platform } from "react-native";

/**
 * Platform-aware storage utility
 * Uses expo-secure-store on mobile and localStorage on web
 */
export const storage = {
    /**
     * Get an item from storage
     * @param key - The key to retrieve
     * @returns The value or null if not found
     */
    getItem: async (key: string): Promise<string | null> => {
        try {
            if (Platform.OS === "web") {
                return localStorage.getItem(key);
            }
            return await SecureStore.getItemAsync(key);
        } catch (error) {
            console.error(`Error getting item from storage (${key}):`, error);
            return null;
        }
    },

    /**
     * Set an item in storage
     * @param key - The key to set
     * @param value - The value to store
     */
    setItem: async (key: string, value: string): Promise<void> => {
        try {
            if (Platform.OS === "web") {
                localStorage.setItem(key, value);
            } else {
                await SecureStore.setItemAsync(key, value);
            }
        } catch (error) {
            console.error(`Error setting item in storage (${key}):`, error);
        }
    },

    /**
     * Remove an item from storage
     * @param key - The key to remove
     */
    removeItem: async (key: string): Promise<void> => {
        try {
            if (Platform.OS === "web") {
                localStorage.removeItem(key);
            } else {
                await SecureStore.deleteItemAsync(key);
            }
        } catch (error) {
            console.error(`Error removing item from storage (${key}):`, error);
        }
    },
};
