import React from 'react';
import { View, Text, StyleSheet, ScrollView } from 'react-native';
import DayTimeBlock, { DayEvent } from './DayTimeBlock';

const HOUR_HEIGHT = 64;
const DAY_START_HOUR = 0;  // midnight
const HOURS = Array.from({ length: 24 }, (_, i) => i);

const formatHourLabel = (hour: number): string => {
  if (hour === 0)  return '12 AM';
  if (hour === 12) return '12 PM';
  return hour < 12 ? `${hour} AM` : `${hour - 12} PM`;
};

interface DayTimelineProps {
  events: DayEvent[];
  scrollRef?: React.RefObject<ScrollView | null>;
  onEventPress: (event: DayEvent) => void;
  colours: any;
}

const DayTimeline = ({ events, scrollRef, onEventPress, colours }: DayTimelineProps) => {
  const totalHeight = HOURS.length * HOUR_HEIGHT;

  return (
    <ScrollView
      ref={scrollRef}
      style={styles.scroll}
      showsVerticalScrollIndicator={false}
      contentContainerStyle={{ height: totalHeight }}
    >
      <View style={{ height: totalHeight, position: 'relative' }}>
        {/* Hour rows */}
        {HOURS.map((hour) => (
          <View
            key={hour}
            style={[
              styles.hourRow,
              {
                top: hour * HOUR_HEIGHT,
                borderTopColor: colours.border,
              },
            ]}
          >
            <Text style={[styles.hourLabel, { color: colours.secondaryText }]}>
              {formatHourLabel(hour)}
            </Text>
            <View style={[styles.hourLine, { backgroundColor: colours.border }]} />
          </View>
        ))}

        {/* Event blocks */}
        {events.map((event) => (
          <DayTimeBlock
            key={event.id}
            event={event}
            hourHeight={HOUR_HEIGHT}
            dayStartHour={DAY_START_HOUR}
            onPress={onEventPress}
            colours={colours}
          />
        ))}
      </View>
    </ScrollView>
  );
};

export { HOUR_HEIGHT, DAY_START_HOUR };
export default DayTimeline;

const styles = StyleSheet.create({
  scroll: {
    flex: 1,
  },
  hourRow: {
    position: 'absolute',
    left: 0,
    right: 0,
    height: HOUR_HEIGHT,
    flexDirection: 'row',
    alignItems: 'flex-start',
    borderTopWidth: StyleSheet.hairlineWidth,
  },
  hourLabel: {
    width: 52,
    paddingLeft: 12,
    paddingTop: 4,
    fontSize: 11,
    textAlign: 'left',
  },
  hourLine: {
    flex: 1,
    height: StyleSheet.hairlineWidth,
    marginTop: 11,
  },
});
