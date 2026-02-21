import React, { createContext, useContext, useState, useEffect, use } from "react";
import { Appearance, useColorScheme } from "react-native";
import { useStorageState } from "@/hooks/useStorageState";

type ThemeType = "light" | "dark" | "system";

interface ThemeContextType {
    theme: ThemeType;
    currentTheme: "light" | "dark";
    setTheme: (theme: ThemeType) => void;
}

const ThemeContext = createContext<ThemeContextType>({
    theme: "system",
    currentTheme: "dark",
    setTheme: () => null,
});

export const useTheme = () => useContext(ThemeContext);


export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const systemColourScheme = useColorScheme() as 'light' | 'dark';
    const [[, storedTheme], setStorageTheme] = useStorageState("theme");
    const [theme, setThemeState] = useState<ThemeType>("system");
    const [currentTheme, setCurrentTheme] = useState<"light" | "dark">('dark');

    useEffect(() => {
        if (storedTheme) {
            setThemeState(storedTheme as ThemeType);
        }
    }, [storedTheme]);

    // Update current theme based on selected theme and system preference
    useEffect(() => {
        if (theme === 'system') {
            setCurrentTheme(systemColourScheme || 'dark');
        } else {
            setCurrentTheme(theme as "light" | "dark");
        }
    }, [theme, systemColourScheme]);

    // Update storage when theme changes
    useEffect(() => {
        const subscriptionn = Appearance.addChangeListener(({ colorScheme }) => {
            if (theme === 'system') {
                setCurrentTheme((colorScheme as "light" | "dark") || 'dark');
            }
        });
        return () => subscriptionn.remove();
    }, [theme]);

    const setTheme = (newTheme: ThemeType) => {
        setThemeState(newTheme);
        setStorageTheme(newTheme);
    }

    return(
        <ThemeContext.Provider value={{ theme, currentTheme, setTheme }}>
            {children}
        </ThemeContext.Provider>
    )
};