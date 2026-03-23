import { Slot, useRouter, useSegments } from "expo-router";
import { StatusBar } from "expo-status-bar";
import { useEffect } from "react";
import "../global.css";
import { ThemeProvider, useTheme } from "@/context/ThemeContext";
import { SessionProvider, useSession } from "@/context/AuthContext";
import Toast from "react-native-toast-message";

// Suppress noisy React Native Web SSR warnings
if (typeof window !== 'undefined') {
  const originalWarn = console.warn;
  const originalError = console.error;
  
  console.warn = (...args: any[]) => {
    const msg = args[0]?.toString() || '';
    if (
      msg.includes('props.pointerEvents is deprecated') ||
      msg.includes('shadow*" style props are deprecated')
    ) return;
    originalWarn(...args);
  };
  
  console.error = (...args: any[]) => {
    const msg = args[0]?.toString() || '';
    if (msg.includes('non-boolean attribute') && msg.includes('collapsable')) return;
    originalError(...args);
  };
}

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
