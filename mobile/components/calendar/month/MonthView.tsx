import React, { useState, useCallback } from 'react';
import {
  View,
  Pressable,
  StyleSheet,
  SafeAreaView,
  StatusBar,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Plus } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import CalendarHeader from './CalendarHeader';
import ViewTabs, { CalendarView } from './ViewTabs';
import CalendarGrid from './CalendarGrid';
import { CalendarEvent } from './DayCell';

const MONTH_NAMES = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

interface MonthViewProps {
  events?: CalendarEvent[];
  onDayPress?: (year: number, month: number, day: number) => void;
  onAddEvent?: () => void;
}

const MonthView = ({ events = [], onDayPress, onAddEvent }: MonthViewProps) => {
  const colours = useThemeColours();
  const router = useRouter();

  const today = new Date();
  const [activeTab, setActiveTab] = useState<CalendarView>('month');
  const [selectedDay, setSelectedDay] = useState<number | null>(today.getDate());
  const [currentYear] = useState(today.getFullYear());
  const [currentMonth] = useState(today.getMonth());

  const handleDayPress = useCallback(
    (year: number, month: number, day: number) => {
      setSelectedDay(day);
      onDayPress?.(year, month, day);
    },
    [onDayPress]
  );

  const handleClose = useCallback(() => {
    router.back();
  }, [router]);

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colours.background }]}>
      <StatusBar barStyle="light-content" backgroundColor={colours.background} />

      <CalendarHeader
        monthName={MONTH_NAMES[currentMonth]}
        year={currentYear}
        onClose={handleClose}
        colours={colours}
      />

      <ViewTabs
        activeTab={activeTab}
        onTabChange={setActiveTab}
        colours={colours}
      />

      {activeTab === 'month' && (
        <CalendarGrid
          year={currentYear}
          month={currentMonth}
          events={events}
          selectedDay={selectedDay}
          onDayPress={handleDayPress}
          colours={colours}
        />
      )}

      {/* FAB */}
      <View style={styles.fabContainer}>
        <Pressable
          onPress={onAddEvent}
          style={[styles.fab, { backgroundColor: colours.card }]}
        >
          <Plus size={24} color={colours.text} />
        </Pressable>
      </View>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
  },
  fabContainer: {
    position: 'absolute',
    bottom: 32,
    right: 24,
  },
  fab: {
    width: 52,
    height: 52,
    borderRadius: 26,
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
  },
});

export default MonthView;
