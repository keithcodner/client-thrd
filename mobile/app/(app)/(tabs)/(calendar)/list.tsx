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
import { useTheme } from '@/context/ThemeContext';
import CalendarHeader from '@/components/calendar/month/CalendarHeader';
import ViewTabs, { CalendarView } from '@/components/calendar/month/ViewTabs';
import ListEmptyState from '@/components/calendar/list/ListEmptyState';
import ListDaySection from '@/components/calendar/list/ListDaySection';
import { ListEvent } from '@/components/calendar/list/ListEventItem';
import CreateTimeBlock from '@/components/calendar/CreateTimeBlock';
import {
  fetchMonthEvents,
  createCalendarEvent,
  CalendarEventPayload,
} from '@/services/calendarService';

const MONTH_NAMES = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

// ── Helpers ───────────────────────────────────────────────────────────────────

const pad = (n: number) => String(n).padStart(2, '0');

function toDateKey(iso: string): string {
  const d = new Date(iso);
  return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
}

// ── Screen ────────────────────────────────────────────────────────────────────

export default function ListScreen() {
  const colours = useThemeColours();
  const { currentTheme } = useTheme();
  const router  = useRouter();

  const today    = new Date();
  const todayKey = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;

  const [sections, setSections] = useState<{ dateKey: string; events: ListEvent[] }[]>([]);
  const [loading, setLoading]   = useState(true);
  const [showCreate, setShowCreate] = useState(false);

  // Fetch events from today onwards — current month + next 2 months
  const loadEvents = useCallback(async () => {
    try {
      const months: { year: number; month: number }[] = [];
      for (let i = 0; i < 3; i++) {
        const d = new Date(today.getFullYear(), today.getMonth() + i, 1);
        months.push({ year: d.getFullYear(), month: d.getMonth() + 1 });
      }

      const results = await Promise.all(
        months.map(({ year, month }) => fetchMonthEvents(year, month)),
      );

      const allEvents = results.flat();

      // Build a map of dateKey → events, filtering to today and later
      const map: Record<string, ListEvent[]> = {};
      allEvents.forEach((e) => {
        const key = toDateKey(e.start_at);
        if (key >= todayKey) {
          if (!map[key]) map[key] = [];
          map[key].push({
            id:       String(e.id),
            name:     e.name,
            start_at: e.start_at,
            end_at:   e.end_at,
            color:    e.color ?? '#ADC178',
          });
        }
      });

      // Sort events within each day chronologically
      Object.values(map).forEach((arr) =>
        arr.sort((a, b) => a.start_at.localeCompare(b.start_at)),
      );

      // Build sorted section list
      const sorted = Object.keys(map)
        .sort()
        .map((dateKey) => ({ dateKey, events: map[dateKey] }));

      setSections(sorted);
    } catch {
      // fail silently — shows empty state
    } finally {
      setLoading(false);
    }
  }, [todayKey]);

  useEffect(() => {
    loadEvents();
  }, [loadEvents]);

  const handleTabChange = useCallback((tab: CalendarView) => {
    if (tab === 'list') return;
    if (tab === 'month') { router.replace('/(app)/(tabs)/(calendar)/'); return; }
    if (tab === 'week')  { router.replace('/(app)/(tabs)/(calendar)/week'); return; }
    if (tab === 'day')   { router.replace('/(app)/(tabs)/(calendar)/day'); return; }
  }, [router]);

  const handleClose = useCallback(() => router.back(), [router]);

  // Pressing an event navigates to its day view
  const handleEventPress = useCallback((event: ListEvent) => {
    const dateKey = toDateKey(event.start_at);
    router.push({ pathname: '/(app)/(tabs)/(calendar)/day', params: { date: dateKey } });
  }, [router]);

  const handleCreate = useCallback(async (payload: CalendarEventPayload) => {
    await createCalendarEvent(payload);
    setLoading(true);
    await loadEvents();
  }, [loadEvents]);

  const isEmpty = !loading && sections.length === 0;

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colours.background }]}>
      <StatusBar barStyle={currentTheme === 'dark' ? 'light-content' : 'dark-content'} backgroundColor={colours.background} />

      <CalendarHeader
        monthName={MONTH_NAMES[today.getMonth()]}
        year={today.getFullYear()}
        onClose={handleClose}
        colours={colours}
      />

      <ViewTabs activeTab="list" onTabChange={handleTabChange} colours={colours} />

      {isEmpty ? (
        <ListEmptyState colours={colours} />
      ) : (
        <ScrollView
          style={styles.scroll}
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
        >
          {sections.map(({ dateKey, events }) => (
            <ListDaySection
              key={dateKey}
              dateKey={dateKey}
              events={events}
              onEventPress={handleEventPress}
              colours={colours}
            />
          ))}
        </ScrollView>
      )}

      {/* FAB */}
      <View style={styles.fabContainer}>
        <Pressable
          onPress={() => setShowCreate(true)}
          style={[styles.fab, { backgroundColor: colours.card }]}
        >
          <Plus size={24} color={colours.text} />
        </Pressable>
      </View>

      {/* Create Time Block */}
      <CreateTimeBlock
        visible={showCreate}
        selectedDate={today}
        onClose={() => setShowCreate(false)}
        onSave={handleCreate}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1 },
  scroll: { flex: 1 },
  scrollContent: { paddingTop: 16, paddingBottom: 100 },
  fabContainer: { position: 'absolute', bottom: 32, right: 24 },
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
