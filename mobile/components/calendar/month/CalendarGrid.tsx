import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import DayCell, { CalendarEvent } from './DayCell';

const DAY_LABELS = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

interface CalendarGridProps {
  year: number;
  month: number; // 0-indexed (0 = January)
  events: CalendarEvent[];
  selectedDay?: number | null;
  onDayPress?: (year: number, month: number, day: number) => void;
  colours: any;
}

const CalendarGrid = ({
  year,
  month,
  events,
  selectedDay,
  onDayPress,
  colours,
}: CalendarGridProps) => {
  const today = new Date();
  const firstDayOfWeek = new Date(year, month, 1).getDay(); // 0 = Sunday
  const daysInMonth = new Date(year, month + 1, 0).getDate();
  const daysInPrevMonth = new Date(year, month, 0).getDate();

  // Build cells dynamically: 5 rows (35 cells) when possible, 6 rows (42) otherwise
  const cells: { day: number; isCurrentMonth: boolean }[] = [];

  for (let i = firstDayOfWeek - 1; i >= 0; i--) {
    cells.push({ day: daysInPrevMonth - i, isCurrentMonth: false });
  }
  for (let d = 1; d <= daysInMonth; d++) {
    cells.push({ day: d, isCurrentMonth: true });
  }
  const numRows = Math.ceil(cells.length / 7);
  const totalCells = numRows * 7;
  const remaining = totalCells - cells.length;
  for (let d = 1; d <= remaining; d++) {
    cells.push({ day: d, isCurrentMonth: false });
  }

  const getEventsForDay = (day: number): CalendarEvent[] => {
    const pad = (n: number) => String(n).padStart(2, '0');
    const dateStr = `${year}-${pad(month + 1)}-${pad(day)}`;
    return events.filter((e) => e.date === dateStr);
  };

  const rows = Array.from({ length: numRows }, (_, i) => cells.slice(i * 7, i * 7 + 7));

  return (
    <View style={styles.wrapper}>
      {/* Day header labels — outside the bordered grid */}
      <View style={styles.headerRow}>
        {DAY_LABELS.map((label, i) => (
          <View key={i} style={styles.headerCell}>
            <Text style={[styles.headerText, { color: colours.secondaryText }]}>
              {label}
            </Text>
          </View>
        ))}
      </View>

      <View style={[styles.container, { borderColor: colours.border }]}>
        {/* Calendar rows */}
        {rows.map((row, rowIndex) => (
          <View key={rowIndex} style={styles.row}>
            {row.map((cell, colIndex) => (
              <DayCell
                key={colIndex}
                day={cell.day}
                isCurrentMonth={cell.isCurrentMonth}
                isToday={
                  cell.isCurrentMonth &&
                  cell.day === today.getDate() &&
                  month === today.getMonth() &&
                  year === today.getFullYear()
                }
                isSelected={cell.isCurrentMonth && cell.day === selectedDay}
                events={cell.isCurrentMonth ? getEventsForDay(cell.day) : []}
                onPress={(day) => onDayPress?.(year, month, day)}
                colours={colours}
              />
            ))}
          </View>
        ))}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  wrapper: {
    marginHorizontal: 16,
  },
  container: {
    borderRadius: 16,
    borderWidth: 1,
    paddingBottom: 12,
    overflow: 'hidden',
  },
  headerRow: {
    flexDirection: 'row',
    paddingHorizontal: 0,
    paddingBottom: 6,
    paddingTop: 4,
  },
  headerCell: {
    flex: 1,
    alignItems: 'center',
  },
  headerText: {
    fontSize: 12,
    fontWeight: '500',
  },
  row: {
    flexDirection: 'row',
  },
});

export default CalendarGrid;
