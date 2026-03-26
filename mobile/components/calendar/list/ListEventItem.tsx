import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';

export interface ListEvent {
  id: string;
  name: string;
  start_at: string;
  end_at: string;
  color: string;
}

interface Props {
  event: ListEvent;
  onPress: (event: ListEvent) => void;
  colours: any;
}

function formatTime(iso: string): string {
  const d = new Date(iso);
  let h = d.getHours();
  const m = d.getMinutes().toString().padStart(2, '0');
  const period = h >= 12 ? 'PM' : 'AM';
  if (h > 12) h -= 12;
  if (h === 0) h = 12;
  return `${h}:${m} ${period}`;
}

const ListEventItem = ({ event, onPress, colours }: Props) => {
  return (
    <Pressable
      onPress={() => onPress(event)}
      style={({ pressed }) => [
        styles.container,
        { backgroundColor: pressed ? colours.card : 'transparent' },
      ]}
    >
      <View style={[styles.colorBar, { backgroundColor: event.color }]} />
      <View style={styles.content}>
        <Text style={[styles.title, { color: colours.text }]} numberOfLines={1}>
          {event.name}
        </Text>
        <Text style={[styles.time, { color: colours.secondaryText }]}>
          {formatTime(event.start_at)} – {formatTime(event.end_at)}
        </Text>
      </View>
    </Pressable>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'stretch',
    paddingRight: 16,
    paddingVertical: 10,
    marginHorizontal: 16,
    borderRadius: 8,
  },
  colorBar: {
    width: 4,
    borderRadius: 2,
    marginRight: 12,
    minHeight: 36,
  },
  content: {
    flex: 1,
    justifyContent: 'center',
  },
  title: {
    fontSize: 15,
    fontWeight: '500',
  },
  time: {
    fontSize: 13,
    marginTop: 3,
  },
});

export default ListEventItem;
