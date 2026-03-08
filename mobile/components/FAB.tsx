import React from 'react';
import { View, Pressable, Text, StyleSheet, Animated } from 'react-native';
import { Plus, X } from 'lucide-react-native';
import { useFAB } from '@/context/FABContext';

interface FABProps {
  colors: any;
  onCoordinate?: () => void;
  onCreateCircle?: () => void;
}

export const FAB = ({ colors, onCoordinate = () => {}, onCreateCircle = () => {} }: FABProps) => {
  const { isExpanded, toggleFAB, closeFAB } = useFAB();

  const handleCoordinate = () => {
    onCoordinate();
    closeFAB();
  };

  const handleCreateCircle = () => {
    onCreateCircle();
    closeFAB();
  };

  return (
    <View style={styles.fabContainer}>
      {isExpanded && (
        <>
          {/* Backdrop */}
          <Pressable 
            style={styles.backdrop} 
            onPress={closeFAB}
          />

          {/* Create Circle Button */}
          <Pressable
            style={[styles.fabOption, { backgroundColor: colors.card, borderColor: colors.border }]}
            onPress={handleCreateCircle}
          >
            <Text style={[styles.fabOptionText, { color: colors.text }]}>
              Create Circle
            </Text>
            <Plus size={18} color={colors.accent} />
          </Pressable>

          {/* Coordinate Button */}
          <Pressable
            style={[styles.fabOption, { backgroundColor: colors.card, borderColor: colors.border }]}
            onPress={handleCoordinate}
          >
            <Text style={[styles.fabOptionText, { color: colors.text }]}>
              Coordinate
            </Text>
          </Pressable>
        </>
      )}

      {/* Main FAB Button */}
      <Pressable
        style={[
          styles.fabButton,
          {
            backgroundColor: colors.accent,
          },
        ]}
        onPress={toggleFAB}
      >
        {isExpanded ? (
          <X size={24} color={colors.background} />
        ) : (
          <Plus size={24} color={colors.background} />
        )}
      </Pressable>
    </View>
  );
};

const styles = StyleSheet.create({
  fabContainer: {
    position: 'absolute',
    bottom: 20,
    right: 20,
  },

  backdrop: {
    ...StyleSheet.absoluteFillObject,
  },

  fabButton: {
    width: 56,
    height: 56,
    borderRadius: 28,
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 100,
  },

  fabOption: {
    position: 'absolute',
    bottom: 70,
    right: 0,
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 12,
    borderWidth: 1,
    gap: 8,
    minWidth: 140,
  },

  fabOptionText: {
    fontSize: 13,
    fontWeight: '600',
    flex: 1,
  },
});
