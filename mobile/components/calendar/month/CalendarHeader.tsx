import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';
import { X } from 'lucide-react-native';

interface CalendarHeaderProps {
  monthName: string;
  year: number;
  onClose: () => void;
  colours: any;
}

const CalendarHeader = ({ monthName, year, onClose, colours }: CalendarHeaderProps) => {
  return (
    <View style={styles.container}>
      <View>
        <Text style={[styles.monthTitle, { color: colours.text }]}>{monthName}</Text>
        <Text style={[styles.yearText, { color: colours.secondaryText }]}>{year}</Text>
      </View>
      <Pressable onPress={onClose} style={styles.closeButton} hitSlop={8}>
        <X size={22} color={colours.secondaryText} />
      </Pressable>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    paddingHorizontal: 20,
    paddingTop: 20,
    paddingBottom: 12,
  },
  monthTitle: {
    fontSize: 36,
    fontWeight: '700',
    lineHeight: 40,
  },
  yearText: {
    fontSize: 13,
    marginTop: 2,
  },
  closeButton: {
    marginTop: 6,
    padding: 4,
  },
});

export default CalendarHeader;
