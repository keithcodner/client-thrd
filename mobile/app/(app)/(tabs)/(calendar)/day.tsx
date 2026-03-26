import React, { useCallback, useEffect, useRef, useState } from 'react';
import {
  View,
  Pressable,
  StyleSheet,
  SafeAreaView,
  StatusBar,
  ScrollView,
} from 'react-native';
import { useRouter, useLocalSearchParams } from 'expo-router';
import { Plus } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import CalendarHeader from '@/components/calendar/month/CalendarHeader';
import ViewTabs, { CalendarView } from '@/components/calendar/month/ViewTabs';
import DayHeader from '@/components/calendar/day/DayHeader';
import DayTimeline from '@/components/calendar/day/DayTimeline';
import { DayEvent } from '@/components/calendar/day/DayTimeBlock';
import CreateTimeBlock from '@/components/calendar/CreateTimeBlock';
import EditTimeBlock from '@/components/calendar/EditTimeBlock';
import {
  fetchMonthEvents,
  createCalendarEvent,
  updateCalendarEvent,
  deleteCalendarEvent,
} from '@/services/calendarService';

const MONTH_NAMES = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

// Determine which hour to initially scroll to
const HOUR_HEIGHT = 64;
const DEFAULT_SCROLL_HOUR = 8; // scroll to 8 AM on open

export default function Day() {
  const colours = useThemeColours();
  const router  = useRouter();
  const params  = useLocalSearchParams<{ date?: string }>();

  // Parse date from route param — append T00:00:00 so JS treats it as local midnight,
  // not UTC midnight (which shifts the date back by one day in timezones behind UTC).
  const selectedDate = params.date ? new Date(`${params.date}T00:00:00`) : new Date();

  const timelineRef = useRef<ScrollView>(null);

  const [events, setEvents]               = useState<DayEvent[]>([]);
  const [showCreate, setShowCreate]       = useState(false);
  const [editingEvent, setEditingEvent]   = useState<DayEvent | null>(null);

  const pad = (n: number) => String(n).padStart(2, '0');
  const todayStr = `${selectedDate.getFullYear()}-${pad(selectedDate.getMonth() + 1)}-${pad(selectedDate.getDate())}`;

  // Load events for this day's month
  const loadEvents = useCallback(async () => {
    try {
      const data = await fetchMonthEvents(selectedDate.getFullYear(), selectedDate.getMonth() + 1);
      // Filter down to this specific day
      const dayEvents: DayEvent[] = data
        .filter((e) => {
          const d = new Date(e.start_at);
          return (
            d.getFullYear() === selectedDate.getFullYear() &&
            d.getMonth()    === selectedDate.getMonth() &&
            d.getDate()     === selectedDate.getDate()
          );
        })
        .map((e) => ({
          id:       String(e.id),
          name:     e.name,
          start_at: e.start_at,
          end_at:   e.end_at,
          color:    e.color ?? '#ADC178',
        }));
      setEvents(dayEvents);
    } catch {
      // fail silently — timeline just shows empty
    }
  }, [todayStr]);

  useEffect(() => {
    loadEvents();
    // Scroll to a reasonable starting hour
    setTimeout(() => {
      timelineRef.current?.scrollTo({ y: DEFAULT_SCROLL_HOUR * HOUR_HEIGHT, animated: false });
    }, 200);
  }, [loadEvents]);

  const handleTabChange = useCallback((tab: CalendarView) => {
    if (tab === 'day') return;
    if (tab === 'month') { router.replace('/(app)/(tabs)/(calendar)/'); return; }
    if (tab === 'week')  { router.replace('/(app)/(tabs)/(calendar)/week'); return; }
    if (tab === 'list')  { router.replace('/(app)/(tabs)/(calendar)/list'); return; }
  }, [router]);

  const handleClose = useCallback(() => router.back(), [router]);

  const handleWeeklyView = useCallback(() => {
    router.replace('/(app)/(tabs)/(calendar)/week');
  }, [router]);

  const handleCreate = useCallback(async (payload: { name: string; start_at: string; end_at: string; color: string }) => {
    await createCalendarEvent(payload);
    await loadEvents();
  }, [loadEvents]);

  const handleUpdate = useCallback(async (id: string, payload: { name: string; start_at: string; end_at: string; color: string }) => {
    await updateCalendarEvent(id, payload);
    await loadEvents();
  }, [loadEvents]);

  const handleDelete = useCallback(async (id: string) => {
    await deleteCalendarEvent(id);
    await loadEvents();
  }, [loadEvents]);

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colours.background }]}>
      <StatusBar barStyle="light-content" backgroundColor={colours.background} />

      <CalendarHeader
        monthName={MONTH_NAMES[selectedDate.getMonth()]}
        year={selectedDate.getFullYear()}
        onClose={handleClose}
        colours={colours}
      />

      <ViewTabs activeTab="day" onTabChange={handleTabChange} colours={colours} />

      {/* "Thursday 26  WEEKLY VIEW" sub-header */}
      <DayHeader
        date={selectedDate}
        onWeeklyViewPress={handleWeeklyView}
        colours={colours}
      />

      <View style={[styles.divider, { backgroundColor: colours.border }]} />

      <DayTimeline
        events={events}
        scrollRef={timelineRef}
        onEventPress={(event) => setEditingEvent(event)}
        colours={colours}
      />

      {/* FAB */}
      <View style={styles.fabContainer}>
        <Pressable
          onPress={() => setShowCreate(true)}
          style={[styles.fab, { backgroundColor: colours.card }]}
        >
          <Plus size={24} color={colours.text} />
        </Pressable>
      </View>

      <CreateTimeBlock
        visible={showCreate}
        selectedDate={selectedDate}
        onClose={() => setShowCreate(false)}
        onSave={handleCreate}
      />

      <EditTimeBlock
        visible={editingEvent !== null}
        event={editingEvent}
        onClose={() => setEditingEvent(null)}
        onSave={handleUpdate}
        onDelete={handleDelete}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safeArea: { flex: 1 },
  divider: { height: StyleSheet.hairlineWidth, marginBottom: 4 },
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
