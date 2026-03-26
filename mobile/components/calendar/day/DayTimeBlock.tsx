import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';

export interface DayEvent {
  id: string;
  name: string;
  start_at: string; // ISO
  end_at: string;   // ISO
  color: string;    // hex
}

interface DayTimeBlockProps {
  event: DayEvent;
  hourHeight: number;
  dayStartHour: number; // e.g. 0 for midnight
  onPress: (event: DayEvent) => void;
  colours: any;
}

const DayTimeBlock = ({ event, hourHeight, dayStartHour, onPress, colours }: DayTimeBlockProps) => {
  const start = new Date(event.start_at);
  const end   = new Date(event.end_at);

  const startMinutesFromDayStart =
    (start.getHours() - dayStartHour) * 60 + start.getMinutes();
  const durationMinutes =
    (end.getHours() - start.getHours()) * 60 + (end.getMinutes() - start.getMinutes());

  const top    = (startMinutesFromDayStart / 60) * hourHeight;
  const height = Math.max((durationMinutes / 60) * hourHeight, hourHeight * 0.4);

  const formatTime = (d: Date) => {
    let h = d.getHours();
    const m = d.getMinutes().toString().padStart(2, '0');
    const period = h >= 12 ? 'PM' : 'AM';
    if (h > 12) h -= 12;
    if (h === 0) h = 12;
    return `${h}:${m} ${period}`;
  };

  return (
    <Pressable
      onPress={() => onPress(event)}
      style={[
        styles.block,
        {
          top,
          height,
          backgroundColor: event.color + '33',
          borderLeftColor: event.color,
        },
      ]}
    >
      <Text style={[styles.name, { color: event.color }]} numberOfLines={1}>
        {event.name}
      </Text>
      <Text style={[styles.time, { color: event.color + 'aa' }]}>
        {formatTime(start)} – {formatTime(end)}
      </Text>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  block: {
    position: 'absolute',
    left: 60,
    right: 16,
    borderRadius: 8,
    borderLeftWidth: 3,
    paddingHorizontal: 8,
    paddingVertical: 4,
  },
  name: {
    fontSize: 13,
    fontWeight: '600',
  },
  time: {
    fontSize: 11,
    marginTop: 1,
  },
});

export default DayTimeBlock;
