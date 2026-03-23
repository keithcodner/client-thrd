import {useContext, createContext, useState, useEffect, type PropsWithChildren} from "react";
import { useStorageState } from "@/hooks/useStorageState";
import { router } from "expo-router";
import axios from "axios";
import axiosInstance from "@/config/axiosConfig";
import { clearAllChatCaches } from "@/services/cacheService";
import websocketService from "@/services/websocketService";

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    credits: string | null;
    // Add other user fields as needed
}

interface AuthContextType {
    signIn: (token: string, user: User) => void;
    signOut?: () => void;
    session?: string | null;
    user?: User | null;
    isLoading: boolean;
    updateUser: (userData: any) => Promise<void>;
    isUserOnline: (userId: number) => boolean;
    subscribeToConversationPresence: (conversationId: string) => void;
    unsubscribeFromConversationPresence: (conversationId: string) => void;
    onlineUsers: Set<number>;
}

const AuthContext = createContext<AuthContextType>({
    signIn: () => null,
    signOut: () => null,
    session: null,
    user: null,
    isLoading: false,
    updateUser: async () => {},
    isUserOnline: () => false,
    subscribeToConversationPresence: () => {},
    unsubscribeFromConversationPresence: () => {},
    onlineUsers: new Set(),
});

export function useSession() {
    const value = useContext(AuthContext);
    if (process.env.NODE_ENV !== "production" && !value ) {
        throw new Error("useSession must be used within an <SessionProvider>.");
    }
    return value;
}

export function SessionProvider({ children }: PropsWithChildren) {
    const [[isLoading, session], setSession] = useStorageState("session");
    const [[, user], setUser] = useStorageState("user");
    const [onlineUsers, setOnlineUsers] = useState<Set<number>>(new Set());
    const [activePresenceChannels, setActivePresenceChannels] = useState<Set<string>>(new Set());

    const updateUser = async (userData: any) => {
       await setUser(userData);
    };


    const loadUserInfo = async (userData: any) => {
       try {
           
            const response = await axiosInstance.get("/user");
            
            setUser(JSON.stringify(response.data));
       } catch (error) {
            if (axios.isAxiosError(error) && error.response?.status === 401) {
                setSession(null);
                setUser(null);
                router.replace("/sign-in");
            } else {
                console.error("Error fetching user info:", error);
            }
       }
    };


    const handleSignIn = async (token: string, userData: User) => {
        try {
            // SECURITY: Clear all caches before signing in to prevent showing previous user's data
            await clearAllChatCaches();
            console.log('🛡️ Cache cleared before sign in');
            
            await setSession(token);
            await setUser(JSON.stringify(userData));
            
            // Initialize WebSocket connection immediately after sign in
            console.log('🔌 Initializing WebSocket connection after sign in');
            websocketService.connect(token, userData.id);
        } catch (error) {
            console.error("Error during sign in:", error);
            throw error;
        }
    };
    
    const handleSignOut = async () => {
       try {
                if (session) {
                // Disconnect WebSocket before signing out
                console.log('🔌 Disconnecting WebSocket before sign out');
                websocketService.disconnect();
                
                // SECURITY: Clear all caches on logout to prevent data leakage
                await clearAllChatCaches();
                console.log('🛡️ Cache cleared on logout');
                
                await axiosInstance.post("/logout", null);

                setSession(null);
                setUser(null);
                router.replace("/sign-in");
            }
       } catch (error) {
            console.error("Error during sign out:", error);
            
            // Even if logout API fails, clear local data
            websocketService.disconnect();
            await clearAllChatCaches();
            setSession(null);
            setUser(null);
            router.replace("/sign-in");
       }
    };

    //Parse user data if it exists
    const parsedUser = user ? (() => {
        try {
            return JSON.parse(user);
        } catch (error) {
            console.error("Error parsing user data:", error);
            return null;
        }
    })() : null;

    useEffect(() => {
        if (session && !user) {
            loadUserInfo(user);
        }
    }, [session]);
    
    // Initialize WebSocket connection when user is authenticated (on app start or after sign in)
    useEffect(() => {
        if (session && parsedUser && !isLoading) {
            const wsState = websocketService.getConnectionState();
            if (wsState !== 'connected' && wsState !== 'connecting') {
                console.log('🔌 Initializing WebSocket connection for authenticated user');
                websocketService.connect(session, parsedUser.id);
            }
        }
    }, [session, parsedUser, isLoading]);

    //Function to update user data in storage and context
    const handleUpdatedUser = async (userData: any) => {
       try {
            const userString = JSON.stringify(userData);
            await setUser(userString);
       } catch (error) {
            console.error("Error updating user data:", error);
       }
    };

    // Check if a user is online
    const isUserOnline = (userId: number): boolean => {
        return onlineUsers.has(userId);
    };

    // Subscribe to a conversation's presence channel
    const subscribeToConversationPresence = (conversationId: string) => {
        if (activePresenceChannels.has(conversationId)) {
            console.log('📍 Already subscribed to presence for conversation:', conversationId);
            return;
        }

        console.log('📍 AuthContext subscribing to presence for conversation:', conversationId);
        
        websocketService.subscribeToPresence(
            conversationId,
            // User joined
            (member: any) => {
                console.log('✅ User came online (AuthContext):', member.id);
                setOnlineUsers(prev => {
                    const newSet = new Set(prev);
                    newSet.add(member.id);
                    return newSet;
                });
            },
            // User left
            (member: any) => {
                console.log('❌ User went offline (AuthContext):', member.id);
                setOnlineUsers(prev => {
                    const newSet = new Set(prev);
                    newSet.delete(member.id);
                    return newSet;
                });
            },
            // Initial member list
            (memberList: any[]) => {
                console.log('👥 Initial members (AuthContext):', memberList);
                setOnlineUsers(prev => {
                    const newSet = new Set(prev);
                    memberList.forEach((m: any) => {
                        if (m.id) newSet.add(m.id);
                    });
                    return newSet;
                });
            },
            // Error handler
            (error: any) => {
                console.error('❌ Presence subscription error (AuthContext):', error);
            }
        );

        setActivePresenceChannels(prev => {
            const newSet = new Set(prev);
            newSet.add(conversationId);
            return newSet;
        });
    };

    // Unsubscribe from a conversation's presence channel
    const unsubscribeFromConversationPresence = (conversationId: string) => {
        console.log('📍 AuthContext unsubscribing from presence for conversation:', conversationId);
        websocketService.unsubscribeFromPresence(conversationId);
        
        setActivePresenceChannels(prev => {
            const newSet = new Set(prev);
            newSet.delete(conversationId);
            return newSet;
        });
    };

    return (
        <AuthContext.Provider value={{
            signIn: handleSignIn,
            signOut: handleSignOut,
            session,
            user: parsedUser,
            isLoading,
            updateUser: handleUpdatedUser,
            isUserOnline,
            subscribeToConversationPresence,
            unsubscribeFromConversationPresence,
            onlineUsers,
        }}>
            {children}  

        </AuthContext.Provider>
    );
}