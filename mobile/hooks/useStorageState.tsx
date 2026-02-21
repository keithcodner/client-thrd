import { useState, useEffect, useCallback, use } from "react";
import * as SourceStore from "expo-secure-store";
import { Platform } from "react-native";

const storage = {
    get: async (key: string): Promise<string | null> => {
        try {
            if (Platform.OS === "web") {
              return localStorage.getItem(key);  
            }
            return await SourceStore.getItemAsync(key);
        } catch (error) {
            console.log("Storage is unavailable", error);
            return null;
        }
    },
    set: async (key: string, value: string | null): Promise<void> => {
        try {
            if (Platform.OS === "web") {
              value === null ? localStorage.removeItem(key) : localStorage.setItem(key, value);
            }else{
                value === null ? await SourceStore.deleteItemAsync(key) : await SourceStore.setItemAsync(key, value);
            }
           
        } catch (error) {
            console.log("Storage is unavailable", error);
        }   
    },
};

type StorageState = [[boolean, string | null], (value: string | null) => void];

export function useStorageState(key:string): StorageState {
    const [isLoading, setIsLoading] = useState(true);
    const [value, setValue] = useState<string | null>(null);

    useEffect(() => {
        storage.get(key).then(value => {
            setValue(value);
            setIsLoading(false);
        });
    }, [key]);

    const updateValue = useCallback((newValue: string | null) => {
        setValue(newValue);
        storage.set(key, newValue);
    }, [key]);

    return [[isLoading, value], updateValue];
}