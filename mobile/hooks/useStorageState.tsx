import { useState, useEffect, useCallback, use } from "react";
import { storage } from "@/utils/storage";

type StorageState = [[boolean, string | null], (value: string | null) => void];

export function useStorageState(key:string): StorageState {
    const [isLoading, setIsLoading] = useState(true);
    const [value, setValue] = useState<string | null>(null);

    useEffect(() => {
        storage.getItem(key).then(value => {
            setValue(value);
            setIsLoading(false);
        });
    }, [key]);

    const updateValue = useCallback((newValue: string | null) => {
        setValue(newValue);
        if (newValue === null) {
            storage.removeItem(key);
        } else {
            storage.setItem(key, newValue);
        }
    }, [key]);

    return [[isLoading, value], updateValue];
}