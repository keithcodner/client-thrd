import React, { useEffect, useRef } from 'react';
import { View, Pressable, Text, StyleSheet, Animated } from 'react-native';
import { Plus, X, Sparkle, Film } from 'lucide-react-native';
import { useFAB } from '@/context/FABContext';

export interface FABAction {
  id: string;
  label: string;
  icon?: React.ComponentType<any>;
  onPress: () => void;
  color?: string; // Optional custom color for text or icon
}

interface FABProps {
  colors: any;
  actions?: FABAction[];
  onCoordinate?: () => void;
  onCreateCircle?: () => void;
  onCreatePost?: () => void;
}

export const FAB = ({ 
  colors, 
  actions,
  onCoordinate = () => {}, 
  onCreateCircle = () => {}, 
  onCreatePost = () => {},
}: FABProps) => {
  const { isExpanded, toggleFAB, closeFAB } = useFAB();
  const animatedValues = useRef<Animated.Value[]>([]).current;

  const handleCoordinate = () => {
    onCoordinate();
    closeFAB();
  };

  const handleCreateCircle = () => {
    onCreateCircle();
    closeFAB();
  };

  const handleCreatePost = () => {
    onCreatePost();
    closeFAB();
  }
  // Default actions if none provided
  const defaultActions: FABAction[] = [
    {
      id: 'create-post',
      label: 'Post',
      icon: Film,
      onPress: handleCreatePost,
    },
    {
      id: 'coordinate',
      label: 'Coordinate',
      icon: Sparkle,
      onPress: handleCoordinate,
    },
    {
      id: 'create-circle',
      label: 'Create Circle',
      icon: Plus,
      onPress: handleCreateCircle,
    },
  ];

  const displayActions = actions || defaultActions;

  // Initialize animated values for each action
  useEffect(() => {
    if (animatedValues.length === 0) {
      animatedValues.push(
        ...displayActions.map(() => new Animated.Value(0))
      );
    }
  }, [displayActions.length]);

  // Animate actions when expanded/collapsed
  useEffect(() => {
    if (isExpanded) {
      // Stagger animation for each action
      Animated.stagger(
        80,
        animatedValues.map((anim) =>
          Animated.timing(anim, {
            toValue: 1,
            duration: 300,
            useNativeDriver: true,
          })
        )
      ).start();
    } else {
      // Reverse animation
      Animated.parallel(
        animatedValues.map((anim) =>
          Animated.timing(anim, {
            toValue: 0,
            duration: 200,
            useNativeDriver: true,
          })
        )
      ).start();
    }
  }, [isExpanded]);

  return (
    <View style={styles.fabContainer}>
      {isExpanded && (
        <>
          {/* Backdrop */}
          <Animated.View
            style={[
              styles.backdrop,
              {
                opacity: animatedValues[0] || 0.3,
              },
            ]}
          >
            <Pressable 
              style={{ flex: 1 }}
              onPress={closeFAB}
            />
          </Animated.View>

          {/* Action Buttons */}
          {displayActions.map((action, index) => {
            const Icon = action.icon;
            const bottomOffset = 70 + (index * 60);
            const animValue = animatedValues[index] || new Animated.Value(0);

            const animatedStyle = {
              opacity: animValue,
              transform: [
                {
                  scale: animValue.interpolate({
                    inputRange: [0, 1],
                    outputRange: [0.5, 1],
                  }),
                },
                {
                  translateY: animValue.interpolate({
                    inputRange: [0, 1],
                    outputRange: [-20, 0],
                  }),
                },
              ],
            };

            return (
              <Animated.View
                key={action.id}
                style={[
                  styles.fabOptionContainer,
                  { 
                    bottom: bottomOffset,
                  },
                  animatedStyle
                ]}
              >
                <Pressable
                  style={[
                    styles.fabOption, 
                    { 
                      backgroundColor: colors.background, 
                      borderColor: colors.border,
                    }
                  ]}
                  onPress={() => {
                    action.onPress();
                    closeFAB();
                  }}
                >
                  <Text style={[styles.fabOptionText, { color: colors.text }]}>
                    {action.label}
                  </Text>

                  {/* If icon exists */}
                  {Icon && (
                    <View style={[styles.iconCircle, { backgroundColor: '#292525' }]}>
                      <Icon size={15} color={action.color} />
                    </View>
                  )}
                </Pressable>
              </Animated.View>
            );
          })}
        </>
      )}

      {/* Main FAB Button */}
      <Pressable
        style={[
          styles.fabButton,
          {
            backgroundColor: '#171d0a',
          },
        ]}
        onPress={toggleFAB}
      >
        {isExpanded ? (
          <X size={24} color="#dae0e6" />
        ) : (
          <Plus size={24} color="#dae0e6" />
        )}
      </Pressable>
    </View>
  );
};

const styles = StyleSheet.create({
  fabContainer: {
    position: 'absolute',
    bottom: 20,
    right: 25,
  },

  backdrop: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    backgroundColor: 'transparent',
  },

  fabButton: {
    width: 56,
    height: 56,
    borderRadius: 28,
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 100,
    borderWidth: 1,
    borderColor: '#41464c',
  },

  fabOption: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 14,
    paddingVertical: 15,
    borderRadius: 20,
    borderWidth: 1,
    gap: 2,
    minWidth: 125,
    borderColor: '#41464c',
  },

  fabOptionContainer: {
    position: 'absolute',
    right: 0,
    zIndex: 99,
    borderWidth: 1,
    borderColor: '#41464c',
    borderRadius: 20,
  },

  fabOptionText: {
    fontSize: 11,
    fontWeight: '600',
    flex: 1,
  },

  iconCircle: {
    width: 20,
    height: 20,
    borderRadius: 10,
    justifyContent: 'center',
    alignItems: 'center',
  },
});
