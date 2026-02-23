import {useContext, createContext, useState, useEffect, type PropsWithChildren} from "react";
import { useStorageState } from "@/hooks/useStorageState";
import { router } from "expo-router";
import axios from "axios";
import axiosInstance from "@/config/axiosConfig";

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
}

const AuthContext = createContext<AuthContextType>({
    signIn: () => null,
    signOut: () => null,
    session: null,
    user: null,
    isLoading: false,
    updateUser: async () => {},
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

    const updateUser = async (userData: any) => {
       await setUser(userData);
    };


    const loadUserInfo = async (userData: any) => {
       try {
           
            const response = await axiosInstance.post("/auth/user", {
                headers: {
                    Authorization: `Bearer ${session}`,
                }
            });
            
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


    const handleSignIn = (token: string, userData: User) => {
        try {
            await setSession(token);
            await setUser(JSON.stringify(userData));
            //router.replace("/home");    
        } catch (error) {
            console.error("Error during sign in:", error);
            throw error;
        }
    };
    
    const handleSignOut = () => {
       try {
            if (session) {
                await axiosInstance.post("/auth/logout", null, {
                    headers: {
                        Authorization: `Bearer ${session}`,
                    }
                });

                setSession(null);
                setUser(null);
                router.replace("/sign-in");
            }
       } catch (error) {
            console.error("Error during sign out:", error);
       }
    };

    useEffect(() => {
        if (session && !user) {
            loadUserInfo(user);
        }
    }, [session]);

    //Parse user data if it exists
    const parsedUser = user ? (() => {
        try {
            return JSON.parse(user);
        } catch (error) {
            console.error("Error parsing user data:", error);
            return null;
        }
    })() : null;

    //Function to update user data in storage and context
    const handleUpdatedUser = async (userData: any) => {
       try {
            const userString = JSON.stringify(userData);
            await setUser(userString);
       } catch (error) {
            console.error("Error updating user data:", error);
       }
    };

    return (
        <AuthContext.Provider value={{
            signIn: handleSignIn,
            signOut: handleSignOut,
            session,
            user: parsedUser,
            isLoading,
            updateUser: handleUpdatedUser,
        }}>
            {children}  

        </AuthContext.Provider>
    );
}