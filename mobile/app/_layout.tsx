import { Stack, Redirect, Slot } from "expo-router";
import { StatusBar } from "expo-status-bar";
import "../global.css";
import { ThemeProvider, useTheme } from "@/context/ThemeContext";
import { SessionProvider, useSession } from "@/context/AuthContext";

function Header() {
  const { currentTheme } = useTheme();
  const {session, isLoading} = useSession();

  if (session && !isLoading) {
    return(
      <>
        <StatusBar style={currentTheme === "dark" ? "light" : "dark"} backgroundColor={currentTheme === 'dark' ? '#15110fff' : '#FFFFFF'} />
        {/* Leave the way it is */}
        <Redirect href="/(app)/(tabs)" />
      </>
    );
  } 

  return(
    <StatusBar style={currentTheme === "dark" ? "light" : "dark"} backgroundColor={currentTheme === 'dark' ? '#15110fff' : '#FFFFFF'} />
  );
}

export default function RootLayout() {
  return <SessionProvider>
    <ThemeProvider>
      <Header />
      <Slot />
    </ThemeProvider>
  </SessionProvider>
}
