import React from 'react';
import { View, Text, Pressable, StyleSheet } from 'react-native';

export type CalendarView = 'month' | 'week' | 'day' | 'list';

const TABS: { id: CalendarView; label: string }[] = [
  { id: 'month', label: 'MONTH' },
  { id: 'week', label: 'WEEK' },
  { id: 'day', label: 'DAY' },
  { id: 'list', label: 'LIST' },
];

interface ViewTabsProps {
  activeTab: CalendarView;
  onTabChange: (tab: CalendarView) => void;
  colours: any;
}

const ViewTabs = ({ activeTab, onTabChange, colours }: ViewTabsProps) => {
  return (
    <View style={styles.container}>
      {TABS.map((tab) => {
        const isActive = activeTab === tab.id;
        return (
          <Pressable
            key={tab.id}
            onPress={() => onTabChange(tab.id)}
            style={[
              styles.tab,
              isActive
                ? { backgroundColor: colours.text }
                : { backgroundColor: 'transparent' },
            ]}
          >
            <Text
              style={[
                styles.tabText,
                { color: isActive ? colours.background : colours.secondaryText },
              ]}
            >
              {tab.label}
            </Text>
          </Pressable>
        );
      })}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    paddingBottom: 14,
    gap: 8,
  },
  tab: {
    paddingHorizontal: 14,
    paddingVertical: 6,
    borderRadius: 20,
  },
  tabText: {
    fontSize: 12,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
});

export default ViewTabs;
