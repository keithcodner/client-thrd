import React, { useState, useCallback, useEffect } from 'react';
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
import { useTheme } from '@/context/ThemeContext';
import CalendarHeader from './CalendarHeader';
import ViewTabs, { CalendarView } from './ViewTabs';
import CalendarGrid from './CalendarGrid';
import { CalendarEvent } from './DayCell';
import { fetchMonthEvents } from '@/services/calendarService';

const MONTH_NAMES = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

interface MonthViewProps {
  onDayPress?: (year: number, month: number, day: number) => void;
  onAddEvent?: () => void;
}

const MonthView = ({ onDayPress, onAddEvent }: MonthViewProps) => {
  const colours = useThemeColours();
  const { currentTheme } = useTheme();
  const router = useRouter();

  const today = new Date();
  const [activeTab, setActiveTab] = useState<CalendarView>('month');
  const [selectedDay, setSelectedDay] = useState<number | null>(today.getDate());
  const [currentYear]  = useState(today.getFullYear());
  const [currentMonth] = useState(today.getMonth());
  const [events, setEvents] = useState<CalendarEvent[]>([]);

  // Load events for the current month and convert to CalendarEvent dots
  useEffect(() => {
    fetchMonthEvents(currentYear, currentMonth + 1)
      .then((rows) => {
        const converted: CalendarEvent[] = rows.map((e) => {
          const d = new Date(e.start_at);
          const pad = (n: number) => String(n).padStart(2, '0');
          return {
            id:    String(e.id),
            color: e.color ?? '#ADC178',
            date:  `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`,
          };
        });
        setEvents(converted);
      })
      .catch(() => {}); // silent fail
  }, [currentYear, currentMonth]);

  const handleTabChange = useCallback((tab: CalendarView) => {
    if (tab === 'month') return;
    if (tab === 'week')  { router.replace('/(app)/(tabs)/(calendar)/week'); return; }
    if (tab === 'day')   { router.replace('/(app)/(tabs)/(calendar)/day'); return; }
    if (tab === 'list')  { router.replace('/(app)/(tabs)/(calendar)/list'); return; }
  }, [router]);

  const handleDayPress = useCallback(
    (year: number, month: number, day: number) => {
      setSelectedDay(day);
      const pad = (n: number) => String(n).padStart(2, '0');
      const dateStr = `${year}-${pad(month + 1)}-${pad(day)}`;
      router.push({ pathname: '/(app)/(tabs)/(calendar)/day', params: { date: dateStr } });
      onDayPress?.(year, month, day);
    },
    [onDayPress, router]
  );

  const handleClose = useCallback(() => {
    router.back();
  }, [router]);

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colours.background }]}>
      <StatusBar barStyle={currentTheme === 'dark' ? 'light-content' : 'dark-content'} backgroundColor={colours.background} />

      <CalendarHeader
        monthName={MONTH_NAMES[currentMonth]}
        year={currentYear}
        onClose={handleClose}
        colours={colours}
      />

      <ViewTabs
        activeTab={activeTab}
        onTabChange={handleTabChange}
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
