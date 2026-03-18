import { Slot, useRouter, useSegments } from "expo-router";
import { StatusBar } from "expo-status-bar";
import { useEffect } from "react";
import "../global.css";
import { ThemeProvider, useTheme } from "@/context/ThemeContext";
import { SessionProvider, useSession } from "@/context/AuthContext";
import Toast from "react-native-toast-message";

/**
 * NavigationGuard - Root-level auth guard
 * 
 * CRITICAL: This must be at root level ONLY. Never add auth checks to nested layouts.
 * 
 * Uses imperative navigation (router.replace) instead of declarative (<Redirect>)
 * to prevent navigation state resets when layouts re-render.
 * 
 * See: mobile/docs/NAVIGATION_ARCHITECTURE.md
 */
function NavigationGuard() {
  const { currentTheme } = useTheme();
  const { session, isLoading } = useSession();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    if (isLoading) return;

    const inApp = segments[0] === "(app)";
    const inAuth = segments[0] === "(auth)" || segments[0] === undefined;

    // Redirect logged out users to sign-in when trying to access app
    if (!session && inApp) {
      router.replace("/sign-in");
    }
    // Redirect logged in users to app when on auth pages
    else if (session && !inApp) {
      router.replace("/(app)/(tabs)/(home)");
    }
  }, [session, segments, isLoading]);

  return <StatusBar style={currentTheme === "dark" ? "light" : "dark"} backgroundColor={currentTheme === 'dark' ? '#15110fff' : '#FFFFFF'} />;
}

export default function RootLayout() {
  return (
    <SessionProvider>
      <ThemeProvider>
        <NavigationGuard />
        <Slot />
        <Toast />
      </ThemeProvider>
    </SessionProvider>
  );
}
