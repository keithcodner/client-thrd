import { useTheme } from "@/context/ThemeContext";
import { colours } from "@/constants/colours";

export const useThemeColours = () => {
    const { currentTheme } = useTheme();
    return colours[currentTheme];
};