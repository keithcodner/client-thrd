import React, { useCallback, useEffect, useState } from 'react';
import {
  View,
  ScrollView,
  Pressable,
  StyleSheet,
  SafeAreaView,
  StatusBar,
} from 'react-native';
import { useRouter } from 'expo-router';
import { Plus } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import CalendarHeader from '@/components/calendar/month/CalendarHeader';
import ViewTabs, { CalendarView } from '@/components/calendar/month/ViewTabs';
import SuggestedOverlaps from '@/components/calendar/week/SuggestedOverlaps';
import WeekDay, { WeekEvent } from '@/components/calendar/week/WeekDay';
import { fetchMonthEvents } from '@/services/calendarService';

const MONTH_NAMES = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

// Helpers ──────────────────────────────────────────────────────────────────
function getWeekDates(referenceDate: Date): Date[] {
  const sunday = new Date(referenceDate);
  sunday.setDate(referenceDate.getDate() - referenceDate.getDay());
  return Array.from({ length: 7 }, (_, i) => {
    const d = new Date(sunday);
    d.setDate(sunday.getDate() + i);
    return d;
  });
}

// Main screen ──────────────────────────────────────────────────────────────
export default function Week() {
  const colours = useThemeColours();
  const router = useRouter();

  const today = new Date();
  const [weekDates] = useState<Date[]>(() => getWeekDates(today));
  const [eventMap, setEventMap] = useState<Record<string, WeekEvent[]>>({});

  const currentMonth = weekDates[0].getMonth();
  const currentYear  = weekDates[0].getFullYear();

  // Load real events for the week's month
  useEffect(() => {
    fetchMonthEvents(currentYear, currentMonth + 1)
      .then((rows) => {
        const map: Record<string, WeekEvent[]> = {};
        rows.forEach((e) => {
          const d = new Date(e.start_at);
          const pad = (n: number) => String(n).padStart(2, '0');
          const key = `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
          if (!map[key]) map[key] = [];
          const start = new Date(e.start_at);
          const end   = new Date(e.end_at);
          const fmt = (dt: Date) => {
            let h = dt.getHours();
            const m = dt.getMinutes().toString().padStart(2, '0');
            const p = h >= 12 ? 'PM' : 'AM';
            if (h > 12) h -= 12;
            if (h === 0) h = 12;
            return `${h}:${m} ${p}`;
          };
          map[key].push({ id: String(e.id), title: e.name, time: `${fmt(start)} – ${fmt(end)}`, color: e.color });
        });
        setEventMap(map);
      })
      .catch(() => {});
  }, [currentYear, currentMonth]);

  const handleTabChange = useCallback((tab: CalendarView) => {
    if (tab === 'week') return;
    if (tab === 'month') { router.replace('/(app)/(tabs)/(calendar)/'); return; }
    if (tab === 'day')   { router.replace('/(app)/(tabs)/(calendar)/day'); return; }
    if (tab === 'list')  { router.replace('/(app)/(tabs)/(calendar)/list'); return; }
  }, [router]);

  const handleClose = useCallback(() => {
    router.back();
  }, [router]);

  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const handleDayPress = useCallback((date: Date) => {
    const pad = (n: number) => String(n).padStart(2, '0');
    const dateStr = `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
    router.push({ pathname: '/(app)/(tabs)/(calendar)/day', params: { date: dateStr } });
  }, [router]);

  const getEventsForDate = (date: Date): WeekEvent[] => {
    const key = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    return eventMap[key] ?? [];
  };

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colours.background }]}>
      <StatusBar barStyle="light-content" backgroundColor={colours.background} />

      <CalendarHeader
        monthName={MONTH_NAMES[currentMonth]}
        year={currentYear}
        onClose={handleClose}
        colours={colours}
      />

      <ViewTabs activeTab="week" onTabChange={handleTabChange} colours={colours} />

      <ScrollView style={styles.scroll} contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        <SuggestedOverlaps colours={colours} />

        {weekDates.map((date) => (
          <WeekDay
            key={date.toISOString()}
            date={date}
            events={getEventsForDate(date)}
            onPress={handleDayPress}
            colours={colours}
          />
        ))}
      </ScrollView>

      {/* FAB */}
      <View style={styles.fabContainer}>
        <Pressable style={[styles.fab, { backgroundColor: colours.card }]}>
          <Plus size={24} color={colours.text} />
        </Pressable>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1 },
  scroll: { flex: 1 },
  scrollContent: { paddingBottom: 100 },
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
