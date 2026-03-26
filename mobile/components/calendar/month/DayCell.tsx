import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';

export interface CalendarEvent {
  id: string;
  color: string;
  date: string; // YYYY-MM-DD
}

interface DayCellProps {
  day: number;
  isCurrentMonth: boolean;
  isToday: boolean;
  isSelected?: boolean;
  events?: CalendarEvent[];
  onPress?: (day: number) => void;
  colours: any;
}

const DayCell = ({
  day,
  isCurrentMonth,
  isToday,
  isSelected,
  events = [],
  onPress,
  colours,
}: DayCellProps) => {
  const firstEventColor = events.length > 0 ? events[0].color : null;

  const textColor = !isCurrentMonth
    ? colours.stone500
    : isToday
    ? colours.background
    : firstEventColor
    ? firstEventColor
    : colours.text;

  const bgColor = isToday
    ? colours.primaryDark
    : isSelected
    ? colours.primary + '33'
    : 'transparent';

  return (
    <Pressable
      onPress={() => isCurrentMonth && onPress?.(day)}
      style={styles.cell}
    >
      <View style={[styles.dayCircle, { backgroundColor: bgColor }]}>
        <Text style={[styles.dayText, { color: textColor, fontWeight: isToday ? '700' : '400' }]}>
          {day}
        </Text>
      </View>
      <View style={styles.dotsRow}>
        {events.slice(0, 3).map((event) => (
          <View
            key={event.id}
            style={[styles.dot, { backgroundColor: event.color }]}
          />
        ))}
      </View>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  cell: {
    flex: 1,
    alignItems: 'center',
    paddingVertical: 5,
  },
  dayCircle: {
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
  },
  dayText: {
    fontSize: 14,
  },
  dotsRow: {
    flexDirection: 'row',
    gap: 2,
    height: 6,
    alignItems: 'center',
    marginTop: 1,
  },
  dot: {
    width: 4,
    height: 4,
    borderRadius: 2,
  },
});

export default DayCell;
