import React from 'react';
import { View, Text, ScrollView, StyleSheet } from 'react-native';
import { Sparkles } from 'lucide-react-native';
import SuggestedOverlapCard, { OverlapSlot } from './SuggestedOverlapCard';

// Dummy data — replace with real API data from calendarService when endpoint is ready
const DUMMY_OVERLAPS: OverlapSlot[] = [
  {
    id: '1',
    dayLabel: 'FRIDAY',
    timeRange: '6:00 PM – 8:00 PM',
    memberNote: 'Everyone free',
  },
  {
    id: '2',
    dayLabel: 'SATURDAY',
    timeRange: '11:00 AM – 1:00 PM',
    memberNote: '4 members free',
  },
];

interface SuggestedOverlapsProps {
  overlaps?: OverlapSlot[];
  colours: any;
}

const SuggestedOverlaps = ({ overlaps = DUMMY_OVERLAPS, colours }: SuggestedOverlapsProps) => {
  const isEmpty = overlaps.length === 0;

  return (
    <View style={[styles.container, { borderColor: colours.border }]}>
      {/* Section title */}
      <View style={styles.titleRow}>
        <Sparkles size={14} color={colours.primary} />
        <Text style={[styles.title, { color: colours.primary }]}>SUGGESTED OVERLAPS</Text>
      </View>

      {isEmpty ? (
        <Text style={[styles.emptyText, { color: colours.secondaryText }]}>
          No suggested overlaps for this week yet.
        </Text>
      ) : (
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.cardsRow}
        >
          {overlaps.map((slot) => (
            <SuggestedOverlapCard key={slot.id} slot={slot} colours={colours} />
          ))}
        </ScrollView>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginHorizontal: 16,
    marginTop: 16,
    marginBottom: 18,
    borderBottomWidth: 1,
    paddingBottom: 18,
  },
  titleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 12,
  },
  title: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 1,
  },
  cardsRow: {
    gap: 10,
    paddingRight: 4,
  },
  emptyText: {
    fontSize: 13,
    fontStyle: 'italic',
  },
});

export default SuggestedOverlaps;
