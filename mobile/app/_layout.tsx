import { Stack, Redirect, Slot, usePathname } from "expo-router";
import { StatusBar } from "expo-status-bar";
import "../global.css";
import { ThemeProvider, useTheme } from "@/context/ThemeContext";
import { SessionProvider, useSession } from "@/context/AuthContext";
import Toast from "react-native-toast-message";

function Header() {
  const { currentTheme } = useTheme();
  const {session, isLoading} = useSession();

  // Define the authentication routes
  const pathname = usePathname();
  // Check if the current route is an authentication route
  const isAuthRoute = pathname === "/" || pathname === "/sign-in" || pathname === "/signup" || pathname === "/index";    

  if (session && !isLoading && isAuthRoute) {
    return(
      <>
        <StatusBar style={currentTheme === "dark" ? "light" : "dark"} backgroundColor={currentTheme === 'dark' ? '#15110fff' : '#FFFFFF'} />
        {/* Tab initial login redirect */}
        <Redirect href="/(app)/(tabs)/(home)" />
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
      <Toast />
    </ThemeProvider>
  </SessionProvider>
}
