import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  Pressable,
  Modal,
  ScrollView,
  StyleSheet,
  Switch,
  Image,
  Platform,
  Alert,
} from 'react-native';
import { X, Users, ImageIcon, Globe } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import * as ImagePicker from 'expo-image-picker';
import { ImageAdjustmentModal } from './ImageAdjustmentModal';

interface CreateCircleModalProps {
  visible: boolean;
  onClose: () => void;
  onSubmit?: (circleData: any) => void;
}

export const CreateCircleModal = ({
  visible,
  onClose,
  onSubmit,
}: CreateCircleModalProps) => {
  const colors = useThemeColours();
  const [circleName, setCircleName] = useState('');
  const [purpose, setPurpose] = useState('');
  const [isPrivate, setIsPrivate] = useState(false);
  const [coverPhoto, setCoverPhoto] = useState<string | null>(null);
  const [toneIndicator, setToneIndicator] = useState('');
  const [selectedImageUri, setSelectedImageUri] = useState<string | null>(null);
  const [showImageAdjustment, setShowImageAdjustment] = useState(false);

  const handleSubmit = () => {
    if (onSubmit) {
      onSubmit({
        name: circleName,
        purpose,
        isPrivate,
        coverPhoto,
        toneIndicator: !isPrivate ? toneIndicator : undefined,
      });
    }
    handleClose();
  };

  const handleClose = () => {
    // Reset form
    setCircleName('');
    setPurpose('');
    setIsPrivate(false);
    setCoverPhoto(null);
    setToneIndicator('');
    setSelectedImageUri(null);
    setShowImageAdjustment(false);
    onClose();
  };

  const handleImageSelect = async () => {
    try {
      // Request permission
      const { status } = await ImagePicker.requestMediaLibraryPermissionsAsync();
      
      if (status !== 'granted') {
        Alert.alert(
          'Permission Required',
          'Sorry, we need camera roll permissions to select a photo.'
        );
        return;
      }

      // Launch image picker
      const result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        allowsEditing: false,
        quality: 1,
      });

      if (!result.canceled && result.assets && result.assets[0]) {
        setSelectedImageUri(result.assets[0].uri);
        setShowImageAdjustment(true);
      }
    } catch (error) {
      console.error('Error selecting image:', error);
      Alert.alert('Error', 'Failed to select image. Please try again.');
    }
  };

  const handleImageAdjustmentApply = (adjustedImage: any) => {
    setCoverPhoto(adjustedImage.uri);
    setShowImageAdjustment(false);
    setSelectedImageUri(null);
  };

  const handleImageAdjustmentClose = () => {
    setShowImageAdjustment(false);
    setSelectedImageUri(null);
  };

  return (
    <Modal
      visible={visible}
      transparent={true}
      animationType="slide"
      onRequestClose={handleClose}
    >
      <View style={styles.modalOverlay}>
        <View style={[styles.modalContent, { backgroundColor: colors.background }]}>
          {/* Header */}
          <View style={styles.header}>
            <View style={styles.headerLeft}>
              {!isPrivate ? (
                <Globe size={23} color={colors.accent} className='pt-3' />
              ) : (
                <Users size={23} color={colors.accent} className='pt-3' />
              )}
              <View style={styles.headerText}>
                <Text style={[styles.title, { fontFamily: Platform.OS === 'ios' ? 'Georgia' : 'serif', color: colors.text }]}>
                  {!isPrivate ? 'Launch Community Hub' : 'Create Circle'}
                </Text>
                <Text style={[styles.subtitle, { color: colors.secondaryText }]}>
                  {!isPrivate ? 'INTENTIONAL COMMUNITY SPACE' : 'START COORDINATION SPACE'}
                </Text>
              </View>
            </View>
            <Pressable onPress={handleClose} style={styles.closeButton}>
              <X size={24} color={colors.text} />
            </Pressable>
          </View>

          <ScrollView 
            style={styles.scrollContent}
            showsVerticalScrollIndicator={false}
          >
            {/* Circle/Hub Name */}
            <View style={styles.inputGroup}>
              <Text style={[styles.label, { color: colors.secondaryText }]}>
                {!isPrivate ? 'HUB NAME' : 'CIRCLE NAME'}
              </Text>
              <TextInput
                style={[
                  styles.input,
                  {
                    backgroundColor: colors.surface,
                    color: colors.text,
                    borderColor: colors.border,
                  },
                ]}
                placeholder="e.g. Sunday Morning Tennis"
                placeholderTextColor={colors.secondaryText}
                value={circleName}
                onChangeText={setCircleName}
              />
            </View>

            {/* Purpose */}
            <View style={styles.inputGroup}>
              <Text style={[styles.label, { color: colors.secondaryText }]}>
                PURPOSE
              </Text>
              <TextInput
                style={[
                  styles.textArea,
                  {
                    backgroundColor: colors.surface,
                    color: colors.text,
                    borderColor: colors.border,
                  },
                ]}
                placeholder="Why does this exist? (e.g. 'A shared making space for those who find focus together.')"
                placeholderTextColor={colors.secondaryText}
                value={purpose}
                onChangeText={setPurpose}
                multiline
                numberOfLines={3}
                textAlignVertical="top"
              />
            </View>

            {/* Tone Indicator - Only for Public Hubs */}
            {!isPrivate && (
              <View style={styles.inputGroup}>
                <Text style={[styles.label, { color: colors.secondaryText }]}>
                  TONE INDICATOR
                </Text>
                <TextInput
                  style={[
                    styles.input,
                    {
                      backgroundColor: colors.surface,
                      color: colors.text,
                      borderColor: colors.border,
                    },
                  ]}
                  placeholder="Low pressure"
                  placeholderTextColor={colors.secondaryText}
                  value={toneIndicator}
                  onChangeText={setToneIndicator}
                />
                <Text style={[styles.helpText, { color: colors.secondaryText }]}>
                  Helps members understand the social energy of the hub.
                </Text>
              </View>
            )}

            {/* Hub Identity - Cover Photo */}
            <View style={styles.inputGroup}>
              <Text style={[styles.label, { color: colors.secondaryText }]}>
                HUB IDENTITY
              </Text>
              <Pressable
                style={[
                  styles.coverPhotoContainer,
                  {
                    backgroundColor: colors.surface,
                    borderColor: colors.border,
                  },
                ]}
                onPress={handleImageSelect}
              >
                {coverPhoto ? (
                  <Image source={{ uri: coverPhoto }} style={styles.coverImage} />
                ) : (
                  <>
                    <ImageIcon size={32} color={colors.secondaryText} />
                    <Text style={[styles.coverPhotoText, { color: colors.secondaryText }]}>
                      COVER PHOTO
                    </Text>
                  </>
                )}
              </Pressable>
            </View>

            {/* Public/Private Toggle */}
            <Pressable
              style={[
                styles.privacyToggle,
                {
                  backgroundColor: colors.surface,
                  borderColor: colors.border,
                },
              ]}
              onPress={() => setIsPrivate(!isPrivate)}
            >
              <View style={styles.privacyLeft}>
                <View style={styles.lockIcon}>
                  {!isPrivate ? (
                    <Text style={styles.lockEmoji}>🌍</Text>
                  ) : (
                    <Text style={styles.lockEmoji}>🔒</Text>
                  )}
                </View>
                <View>
                  <Text style={[styles.privacyTitle, { color: colors.text }]}>
                    {!isPrivate ? 'Public Hub' : 'Private Circle'}
                  </Text>
                  <Text style={[styles.privacySubtitle, { color: colors.secondaryText }]}>
                    {!isPrivate ? 'COMMUNITY DISCOVERABLE' : 'INVITE-ONLY ACCESS'}
                  </Text>
                </View>
              </View>
              <Switch
                value={!isPrivate}
                onValueChange={(value) => setIsPrivate(!value)}
                trackColor={{ false: colors.border, true: colors.accent }}
                thumbColor={!isPrivate ? '#fff' : '#f4f3f4'}
              />
            </Pressable>

            <View style={{ height: 100 }} />
          </ScrollView>

          {/* Create Button */}
          <View style={[styles.footer, { backgroundColor: colors.background }]}>
            <Pressable
              style={[
                styles.createButton,
                (!circleName || !purpose) && styles.createButtonDisabled,
              ]}
              onPress={handleSubmit}
              disabled={!circleName || !purpose}
            >
              <Text style={styles.createButtonText}>CREATE CIRCLE ✨</Text>
            </Pressable>
          </View>
        </View>
      </View>

      {/* Image Adjustment Modal */}
      {selectedImageUri && (
        <ImageAdjustmentModal
          visible={showImageAdjustment}
          imageUri={selectedImageUri}
          isPublicHub={!isPrivate}
          onClose={handleImageAdjustmentClose}
          onApply={handleImageAdjustmentApply}
        />
      )}
    </Modal>
  );
};

const styles = StyleSheet.create({
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    height: '100%',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
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
  scrollContent: {
    flex: 1,
    paddingHorizontal: 20,
    paddingTop: 40,
    
  },
  inputGroup: {
    marginBottom: 24,
  },
  label: {
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.5,
    marginBottom: 8,
  },
  input: {
    height: 50,
    borderRadius: 12,
    borderWidth: 1,
    paddingHorizontal: 16,
    fontSize: 15,
  },
  textArea: {
    minHeight: 80,
    borderRadius: 12,
    borderWidth: 1,
    paddingHorizontal: 16,
    paddingVertical: 12,
    fontSize: 14,
  },
  coverPhotoContainer: {
    height: 180,
    borderRadius: 12,
    borderWidth: 2,
    borderStyle: 'dashed',
    justifyContent: 'center',
    alignItems: 'center',
    gap: 8,
  },
  coverImage: {
    width: '100%',
    height: '100%',
    borderRadius: 12,
  },
  coverPhotoText: {
    fontSize: 12,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
  privacyToggle: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: 16,
    borderRadius: 20,
    borderWidth: 1,
  },
  privacyLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  lockIcon: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  lockEmoji: {
    fontSize: 16,
  },
  privacyTitle: {
    fontSize: 14,
    fontWeight: '600',
  },
  privacySubtitle: {
    fontSize: 10,
    fontWeight: '600',
    letterSpacing: 0.5,
    marginTop: 2,
  },
  helpText: {
    fontSize: 11,
    fontStyle: 'italic',
    marginTop: 6,
    lineHeight: 16,
  },
  footer: {
    paddingHorizontal: 20,
    paddingTop: 16,
    paddingBottom: 32,
    borderTopWidth: 1,
    borderTopColor: 'rgba(255, 255, 255, 0.1)',
  },
  createButton: {
    backgroundColor: '#171d0a',
    paddingVertical: 16,
    borderRadius: 12,
    alignItems: 'center',
  },
  createButtonDisabled: {
    opacity: 0.5,
  },
  createButtonText: {
    color: '#dae0e6',
    fontSize: 14,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
});
