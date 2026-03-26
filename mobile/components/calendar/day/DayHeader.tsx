import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';

const DAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
const MONTH_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

interface DayHeaderProps {
  date: Date;
  onWeeklyViewPress: () => void;
  colours: any;
}

const DayHeader = ({ date, onWeeklyViewPress, colours }: DayHeaderProps) => {
  const dayName   = DAY_NAMES[date.getDay()];
  const monthShrt = MONTH_SHORT[date.getMonth()];
  const dayNum    = date.getDate();

  return (
    <View style={styles.container}>
      <View style={styles.left}>
        <View style={styles.titleRow}>
          <Text style={[styles.dayName, { color: colours.text }]}>{dayName}</Text>
          <Text style={[styles.dayNum, { color: colours.text }]}> {dayNum}</Text>
        </View>
      </View>
      <Pressable onPress={onWeeklyViewPress} hitSlop={8}>
        <Text style={[styles.weeklyViewLink, { color: colours.primary }]}>WEEKLY VIEW</Text>
      </Pressable>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    alignItems: 'flex-end',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    paddingBottom: 12,
    paddingTop: 4,
  },
  left: {
    flex: 1,
  },
  titleRow: {
    flexDirection: 'row',
    alignItems: 'baseline',
  },
  dayName: {
    fontSize: 32,
    fontWeight: '700',
    fontStyle: 'italic',
  },
  dayNum: {
    fontSize: 32,
    fontWeight: '300',
  },
  weeklyViewLink: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 0.8,
    marginBottom: 6,
  },
});

export default DayHeader;
