import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import ListEventItem, { ListEvent } from './ListEventItem';

const DAY_NAMES = [
  'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday',
];
const MONTH_NAMES = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December',
];

interface Props {
  /** Date key in YYYY-MM-DD format */
  dateKey: string;
  events: ListEvent[];
  onEventPress: (event: ListEvent) => void;
  colours: any;
}

const ListDaySection = ({ dateKey, events, onEventPress, colours }: Props) => {
  // Append T00:00:00 to avoid UTC offset shifting the day
  const date = new Date(`${dateKey}T00:00:00`);
  const dayName = DAY_NAMES[date.getDay()];
  const monthName = MONTH_NAMES[date.getMonth()];
  const dayNumber = date.getDate();

  return (
    <View style={styles.container}>
      <View style={[styles.header, { borderBottomColor: colours.border }]}>
        <Text style={[styles.dayName, { color: colours.secondaryText }]}>{dayName}</Text>
        <Text style={[styles.date, { color: colours.text }]}>
          {monthName} {dayNumber}
        </Text>
      </View>

      {events.map((event) => (
        <ListEventItem
          key={event.id}
          event={event}
          onPress={onEventPress}
          colours={colours}
        />
      ))}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginBottom: 24,
  },
  header: {
    paddingHorizontal: 20,
    paddingBottom: 8,
    marginBottom: 4,
    borderBottomWidth: StyleSheet.hairlineWidth,
  },
  dayName: {
    fontSize: 11,
    textTransform: 'uppercase',
    letterSpacing: 0.8,
    marginBottom: 2,
  },
  date: {
    fontSize: 16,
    fontWeight: '600',
  },
});

export default ListDaySection;
