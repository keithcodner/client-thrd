import React, { createContext, useContext, useState, useEffect } from "react";
import { useColorScheme } from "react-native";
import { useStorageState } from "@/hooks/useStorageState";

type ThemeType = "light" | "dark" | "system";

interface ThemeContextType {
  theme: ThemeType;
  currentTheme: "light" | "dark";
  setTheme: (theme: ThemeType) => void;
}

const ThemeContext = createContext<ThemeContextType>({
  theme: "system",
  currentTheme: "light",
  setTheme: () => {},
});

export const useTheme = () => useContext(ThemeContext);

export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const systemColorScheme = useColorScheme() as "light" | "dark" | null;
  const [[, storedTheme], setStorageTheme] = useStorageState("theme");

  const [theme, setThemeState] = useState<ThemeType>("system");
  const [currentTheme, setCurrentTheme] = useState<"light" | "dark">(
    systemColorScheme === "dark" ? "dark" : "light"
  );

  // Load stored theme
  useEffect(() => {
    if (storedTheme) {
      setThemeState(storedTheme as ThemeType);
    }
  }, [storedTheme]);

  // Resolve theme
  useEffect(() => {
    if (theme === "system") {
      setCurrentTheme(systemColorScheme === "dark" ? "dark" : "light");
    } else {
      setCurrentTheme(theme);
    }
  }, [theme, systemColorScheme]);

  const setTheme = (newTheme: ThemeType) => {
    setThemeState(newTheme);
    setStorageTheme(newTheme);
  };

  return (
    <ThemeContext.Provider value={{ theme, currentTheme, setTheme }}>
      {children}
    </ThemeContext.Provider>
  );
};
