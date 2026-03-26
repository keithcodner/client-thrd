import React from 'react';
import { View, Text, StyleSheet } from 'react-native';

interface WeekListItemProps {
  title: string;
  time: string;
  color?: string;
  colours: any;
}

const WeekListItem = ({ title, time, color, colours }: WeekListItemProps) => {
  const dotColor = color ?? colours.primary;
  return (
    <View style={styles.container}>
      <View style={[styles.dot, { backgroundColor: dotColor }]} />
      <View>
        <Text style={[styles.title, { color: colours.text }]}>{title}</Text>
        <Text style={[styles.time, { color: colours.secondaryText }]}>{time}</Text>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 10,
    paddingVertical: 4,
  },
  dot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginTop: 5,
  },
  title: {
    fontSize: 13,
    fontWeight: '600',
  },
  time: {
    fontSize: 12,
    marginTop: 1,
  },
});

export default WeekListItem;
