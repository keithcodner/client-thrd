import React, { useState, useRef } from 'react';
import {
  View,
  Text,
  Image,
  Pressable,
  StyleSheet,
  Dimensions,
  Modal,
  Platform,
  PanResponder,
} from 'react-native';
import { X, RotateCcw, ZoomIn, ZoomOut, Globe, Users } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');

interface ImageAdjustmentModalProps {
  visible: boolean;
  imageUri: string;
  isPublicHub?: boolean;
  onClose: () => void;
  onApply: (adjustedImage: any) => void;
}

export const ImageAdjustmentModal = ({
  visible,
  imageUri,
  isPublicHub = false,
  onClose,
  onApply,
}: ImageAdjustmentModalProps) => {
  const colors = useThemeColours();
  const [zoom, setZoom] = useState(1);
  const [position, setPosition] = useState({ x: 0, y: 0 });
  const sliderWidth = SCREEN_WIDTH - 120;

  const handleSliderMove = (gestureState: any) => {
    const { moveX } = gestureState;
    const newZoom = 1 + ((moveX - 60) / sliderWidth) * 2;
    setZoom(Math.max(1, Math.min(3, newZoom)));
  };

  const panResponder = useRef(
    PanResponder.create({
      onStartShouldSetPanResponder: () => true,
      onMoveShouldSetPanResponder: () => true,
      onPanResponderMove: (evt, gestureState) => {
        handleSliderMove(gestureState);
      },
    })
  ).current;

  const handleReset = () => {
    setZoom(1);
    setPosition({ x: 0, y: 0 });
  };

  const handleApply = () => {
    // TODO: Apply transformations to image
    onApply({
      uri: imageUri,
      zoom,
      position,
    });
  };

  const handleCancel = () => {
    handleReset();
    onClose();
  };

  return (
    <Modal
      visible={visible}
      transparent={false}
      animationType="slide"
      onRequestClose={handleCancel}
    >
      <View style={[styles.container, { backgroundColor: colors.background }]}>
        {/* Header */}
        <View style={styles.header}>
          <View style={styles.headerLeft}>
            {isPublicHub ? (
              <Globe size={26} color={colors.accent} />
            ) : (
              <Users size={26} color={colors.accent} />
            )}
            <View style={styles.headerText}>
              <Text
                style={[
                  styles.title,
                  {
                    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
                    color: colors.text,
                  },
                ]}
              >
                {isPublicHub ? 'Launch Community Hub' : 'Create Circle'}
              </Text>
              <Text style={[styles.subtitle, { color: colors.secondaryText }]}>
                {isPublicHub
                  ? 'INTENTIONAL COMMUNITY SPACE'
                  : 'START COORDINATION SPACE'}
              </Text>
            </View>
          </View>
          <Pressable onPress={handleCancel} style={styles.closeButton}>
            <X size={24} color={colors.text} />
          </Pressable>
        </View>

        {/* Adjust Photo Section */}
        <View style={styles.adjustSection}>
          <Text style={[styles.adjustTitle, { color: colors.text }]}>
            Adjust Photo
          </Text>
          <Text style={[styles.adjustInstructions, { color: colors.secondaryText }]}>
            DRAG TO POSITION • PINCH/SLIDE TO ZOOM
          </Text>

          {/* Image Container with Grid */}
          <View style={styles.imageContainer}>
            <View style={styles.imageWrapper}>
              <Image
                source={{ uri: imageUri }}
                style={[
                  styles.image,
                  {
                    transform: [
                      { scale: zoom },
                      { translateX: position.x },
                      { translateY: position.y },
                    ],
                  },
                ]}
                resizeMode="cover"
              />
              {/* Grid Overlay */}
              <View style={styles.gridOverlay}>
                <View style={[styles.gridLine, styles.gridVertical1]} />
                <View style={[styles.gridLine, styles.gridVertical2]} />
                <View style={[styles.gridLine, styles.gridHorizontal1]} />
                <View style={[styles.gridLine, styles.gridHorizontal2]} />
              </View>
            </View>
          </View>

          {/* Zoom Controls */}
          <View style={styles.zoomControls}>
            <Pressable onPress={() => setZoom(Math.max(1, zoom - 0.1))}>
              <ZoomOut size={20} color={colors.text} />
            </Pressable>
            <View style={styles.sliderContainer} {...panResponder.panHandlers}>
              <View
                style={[
                  styles.sliderTrack,
                  { backgroundColor: colors.border },
                ]}
              >
                <View
                  style={[
                    styles.sliderFill,
                    {
                      backgroundColor: colors.accent,
                      width: `${((zoom - 1) / 2) * 100}%`,
                    },
                  ]}
                />
              </View>
              <View
                style={[
                  styles.sliderThumb,
                  {
                    backgroundColor: colors.accent,
                    left: `${((zoom - 1) / 2) * 100}%`,
                  },
                ]}
              />
            </View>
            <Pressable onPress={() => setZoom(Math.min(3, zoom + 0.1))}>
              <ZoomIn size={20} color={colors.text} />
            </Pressable>
          </View>
        </View>

        {/* Bottom Controls */}
        <View style={styles.bottomControls}>
          <Pressable
            style={[styles.button, styles.cancelButton]}
            onPress={handleCancel}
          >
            <Text style={[styles.buttonText, { color: colors.text }]}>
              CANCEL
            </Text>
          </Pressable>

          <Pressable
            style={[
              styles.button,
              styles.resetButton,
              { borderColor: colors.border },
            ]}
            onPress={handleReset}
          >
            <RotateCcw size={20} color={colors.text} />
          </Pressable>

          <Pressable
            style={[styles.button, styles.applyButton]}
            onPress={handleApply}
          >
            <Text style={[styles.buttonText, { color: colors.text }]}>
              ✓ APPLY
            </Text>
          </Pressable>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    paddingTop: 20,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    paddingBottom: 20,
    borderBottomWidth: 1,
    borderBottomColor: 'rgba(255, 255, 255, 0.1)',
  },
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 12,
  },
  headerText: {
    gap: 2,
  },
  title: {
    fontSize: 18,
    fontWeight: '600',
  },
  subtitle: {
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
  closeButton: {
    padding: 4,
  },
  adjustSection: {
    flex: 1,
    paddingHorizontal: 20,
    paddingTop: 40,
    alignItems: 'center',
  },
  adjustTitle: {
    fontSize: 20,
    fontWeight: '600',
    fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif',
    marginBottom: 8,
  },
  adjustInstructions: {
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.5,
    marginBottom: 30,
  },
  imageContainer: {
    width: SCREEN_WIDTH - 80,
    height: (SCREEN_WIDTH - 80) * 0.6,
    marginBottom: 30,
  },
  imageWrapper: {
    flex: 1,
    borderRadius: 12,
    overflow: 'hidden',
    position: 'relative',
  },
  image: {
    width: '100%',
    height: '100%',
  },
  gridOverlay: {
    ...StyleSheet.absoluteFillObject,
    pointerEvents: 'none',
  },
  gridLine: {
    position: 'absolute',
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
  },
  gridVertical1: {
    left: '33.33%',
    top: 0,
    bottom: 0,
    width: 1,
  },
  gridVertical2: {
    left: '66.66%',
    top: 0,
    bottom: 0,
    width: 1,
  },
  gridHorizontal1: {
    top: '33.33%',
    left: 0,
    right: 0,
    height: 1,
  },
  gridHorizontal2: {
    top: '66.66%',
    left: 0,
    right: 0,
    height: 1,
  },
  zoomControls: {
    flexDirection: 'row',
    alignItems: 'center',
    width: '100%',
    gap: 16,
  },
  sliderContainer: {
    flex: 1,
    height: 40,
    justifyContent: 'center',
    position: 'relative',
  },
  sliderTrack: {
    height: 4,
    borderRadius: 2,
    overflow: 'hidden',
  },
  sliderFill: {
    height: '100%',
  },
  sliderThumb: {
    position: 'absolute',
    width: 16,
    height: 16,
    borderRadius: 8,
    top: '50%',
    marginTop: -8,
    marginLeft: -8,
  },
  bottomControls: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingVertical: 20,
    gap: 12,
    justifyContent: 'space-between',
  },
  button: {
    paddingVertical: 14,
    paddingHorizontal: 24,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  cancelButton: {
    flex: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
  },
  resetButton: {
    width: 50,
    borderWidth: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.05)',
  },
  applyButton: {
    flex: 1,
    backgroundColor: '#171d0a',
  },
  buttonText: {
    fontSize: 13,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
});
