import { View, Text, Alert, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useSession } from '@/context/AuthContext';
import Button from '@/components/core/Button';
import { useThemeColours } from '@/hooks/useThemeColours';

export default function Profile() {
  const { user, signOut } = useSession();
  const colors = useThemeColours();

  const handleLogout = () => {
    if (Platform.OS === 'web') {
      // For web browsers
      if (window.confirm('Are you sure you want to logout?')) {
        signOut?.();
      }
    } else {
      // Logic for other platforms goes here
      // For mobile devices
      Alert.alert(
        'Logout',
        'Are you sure you want to logout?',
        [
          {
            text: 'Cancel',
            style: 'cancel',
          },
          {
            text: 'Logout',
            style: 'destructive',
            onPress: () => signOut?.(),
          }
        ],
      );
    }
  };

  return (
    // JSX elements would go here
    <SafeAreaView className="flex-1 bg-white dark:bg-gray-900">
      <View className="flex-1 p-4">
        <View className="bg-gray-100 dark:bg-gray-800 rounded-3xl p-6 shadow-lg border border-gray-200 dark:border-gray-700 mb-4">
          <Text className="text-2xl font-bold mb-4 text-gray-800 dark:text-white">
            Profile
          </Text>
          <View className="space-y-2">
            {/* header name */}
            <View
              className="bg-gray-200/80 dark:bg-gray-700/50 p-4 rounded-2xl"
              style={{ backgroundColor: colors.surface }}
            >
              {/* Content goes here */}
              <Text className="text-sm mb-1" style={{color: colors.secondaryText}}>Name</Text>
              <Text className="text-lg" style={{color: colors.text}}>{user?.name}</Text>      
            </View>

            {/* header email */}
            <View className="p-4 rounded-2xl" style={{backgroundColor: colors.surface}}>
              <Text className="text-sm mb-1" style={{color: colors.secondaryText}}>Email</Text>
              <Text className="text-lg" style={{color: colors.text}}>{user?.email}</Text>
            </View>
          </View>
        </View>
        {/* logout button */}
        <Button
          onPress={handleLogout}
          variant="danger"
          className="rounded-2xl shadow-lg"
        >
            <Text className="text-lg font-bold">Logout</Text>
        </Button>
      </View>
    </SafeAreaView>
  );
}