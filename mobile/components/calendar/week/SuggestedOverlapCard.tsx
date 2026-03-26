import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { Clock } from 'lucide-react-native';

export interface OverlapSlot {
  id: string;
  dayLabel: string;   // e.g. "FRIDAY"
  timeRange: string;  // e.g. "6:00 PM – 8:00 PM"
  memberNote: string; // e.g. "Everyone free" | "4 members free"
}

interface SuggestedOverlapCardProps {
  slot: OverlapSlot;
  colours: any;
}

const SuggestedOverlapCard = ({ slot, colours }: SuggestedOverlapCardProps) => {
  return (
    <View style={[styles.card, { backgroundColor: colours.card, borderColor: colours.border }]}>
      <Text style={[styles.dayLabel, { color: colours.secondaryText }]}>{slot.dayLabel}</Text>
      <Text style={[styles.timeRange, { color: colours.text }]}>{slot.timeRange}</Text>
      <View style={styles.noteRow}>
        <Clock size={12} color={colours.secondaryText} />
        <Text style={[styles.noteText, { color: colours.secondaryText }]}>{slot.memberNote}</Text>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    flex: 1,
    borderRadius: 12,
    borderWidth: 1,
    padding: 12,
  },
  dayLabel: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 0.6,
    marginBottom: 4,
  },
  timeRange: {
    fontSize: 14,
    fontWeight: '700',
    marginBottom: 6,
  },
  noteRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  noteText: {
    fontSize: 11,
  },
});

export default SuggestedOverlapCard;
