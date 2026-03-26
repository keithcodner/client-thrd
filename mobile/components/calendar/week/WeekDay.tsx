import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';
import { ChevronRight } from 'lucide-react-native';

const DAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const MONTH_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

export interface WeekEvent {
  id: string;
  title: string;
  time: string;
  color?: string;
}

interface WeekDayProps {
  date: Date;
  events: WeekEvent[];
  onPress: (date: Date) => void;
  colours: any;
}

const WeekDay = ({ date, events, onPress, colours }: WeekDayProps) => {
  const dayName = DAY_NAMES[date.getDay()].toUpperCase();
  const monthShort = MONTH_SHORT[date.getMonth()];
  const dayNum = date.getDate();
  const hasEvents = events.length > 0;

  return (
    <Pressable
      onPress={() => onPress(date)}
      style={[styles.card, { backgroundColor: colours.card, borderColor: colours.border }]}
    >
      <View style={styles.content}>
        <View style={styles.dateBlock}>
          <Text style={[styles.dayName, { color: colours.primary }]}>{dayName}</Text>
          <Text style={[styles.dateLabel, { color: colours.text }]}>
            {monthShort} {dayNum}
          </Text>
          {!hasEvents ? (
            <Text style={[styles.emptyText, { color: colours.secondaryText }]}>No plans yet</Text>
          ) : (
            <View style={styles.eventList}>
              {events.map((event) => (
                <Text key={event.id} style={[styles.eventText, { color: event.color ?? colours.primary }]}>
                  {event.title}
                </Text>
              ))}
            </View>
          )}
        </View>
        <ChevronRight size={18} color={colours.secondaryText} />
      </View>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  card: {
    marginHorizontal: 16,
    marginBottom: 10,
    borderRadius: 16,
    borderWidth: 1,
    paddingVertical: 16,
    paddingHorizontal: 16,
  },
  content: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  dateBlock: {
    flex: 1,
  },
  dayName: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 0.8,
    marginBottom: 2,
  },
  dateLabel: {
    fontSize: 22,
    fontWeight: '700',
    marginBottom: 6,
  },
  emptyText: {
    fontSize: 13,
    fontStyle: 'italic',
  },
  eventList: {
    gap: 2,
  },
  eventText: {
    fontSize: 13,
    fontWeight: '500',
  },
});

export default WeekDay;
