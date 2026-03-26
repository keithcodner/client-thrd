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
    <View style={[styles.container, { borderBottomColor: colours.border }]}>
      <View style={[styles.tabsWrapper, { backgroundColor: colours.stone950 }]}>
        {TABS.map((tab) => {
          const isActive = activeTab === tab.id;
          return (
            <Pressable
              key={tab.id}
              onPress={() => onTabChange(tab.id)}
              style={[
                styles.tab,
                isActive
                  ? { backgroundColor: colours.stone800 }
                  : { backgroundColor: 'transparent' },
              ]}
            >
              <Text
                style={[
                  styles.tabText,
                  { color: isActive ? colours.text : colours.secondaryText },
                ]}
              >
                {tab.label}
              </Text>
            </Pressable>
          );
        })}
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: 16,
    paddingBottom: 30,
    borderBottomWidth: 1,
    marginBottom: 18,
  },
  tabsWrapper: {
    flexDirection: 'row',
    borderRadius: 8,
    padding: 5,
    gap: 2,
  },
  tab: {
    flex: 1,
    alignItems: 'center',
    paddingVertical: 7,
    borderRadius: 6,
  },
  tabText: {
    fontSize: 12,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
});

export default ViewTabs;
