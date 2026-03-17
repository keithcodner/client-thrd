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
} from 'react-native';
import { X, Users, ImageIcon } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';

interface CreatePostModalProps {
  visible: boolean;
  onClose: () => void;
  onSubmit?: (postData: any) => void;
}

export const CreatePostModal = ({
  visible,
  onClose,
  onSubmit,
}: CreatePostModalProps) => {
  const colors = useThemeColours();
  const [postTitle, setPostTitle] = useState('');
  const [postContent, setPostContent] = useState('');
  const [isPrivate, setIsPrivate] = useState(false);
  const [coverImage, setCoverImage] = useState<string | null>(null);

  const handleSubmit = () => {
    if (onSubmit) {
      onSubmit({
        title: postTitle,
        content: postContent,
        isPrivate,
        coverImage,
      });
    }
    handleClose();
  };

  const handleClose = () => {
    // Reset form
    setPostTitle('');
    setPostContent('');
    setIsPrivate(false);
    setCoverImage(null);
    onClose();
  };

  const handleImageSelect = () => {
    // TODO: Implement image picker
    console.log('Select image');
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
              <Users size={20} color={colors.text} />
              <View style={styles.headerText}>
                <Text style={[styles.title, { color: colors.text }]}>Create Post</Text>
                <Text style={[styles.subtitle, { color: colors.secondaryText }]}>
                  SHARE YOUR THOUGHTS
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
            {/* Post Title */}
            <View style={styles.inputGroup}>
              <Text style={[styles.label, { color: colors.secondaryText }]}>
                POST TITLE
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
                value={postTitle}
                onChangeText={setPostTitle}
              />
            </View>

            {/* Post Content */}
            <View style={styles.inputGroup}>
              <Text style={[styles.label, { color: colors.secondaryText }]}>
                CONTENT
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
                placeholder="What's on your mind? (e.g. 'A shared space for those who find focus together.')"
                placeholderTextColor={colors.secondaryText}
                value={postContent}
                onChangeText={setPostContent}
                multiline
                numberOfLines={4}
                textAlignVertical="top"
              />
            </View>

            {/* Cover Photo */}
            <View style={styles.inputGroup}>
              <Text style={[styles.label, { color: colors.secondaryText }]}>
                MEDIA
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
                {coverImage ? (
                  <Image source={{ uri: coverImage }} style={styles.coverImage} />
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

            {/* Privacy Toggle */}
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
                  <Text style={styles.lockEmoji}>🔒</Text>
                </View>
                <View>
                  <Text style={[styles.privacyTitle, { color: colors.text }]}>
                    Private Post
                  </Text>
                  <Text style={[styles.privacySubtitle, { color: colors.secondaryText }]}>
                    FOLLOWERS ONLY ACCESS
                  </Text>
                </View>
              </View>
              <Switch
                value={isPrivate}
                onValueChange={setIsPrivate}
                trackColor={{ false: colors.border, true: '#4c8bf5' }}
                thumbColor={isPrivate ? '#fff' : '#f4f3f4'}
              />
            </Pressable>

            <View style={{ height: 100 }} />
          </ScrollView>

          {/* Create Button */}
          <View style={[styles.footer, { backgroundColor: colors.background }]}>
            <Pressable
              style={[
                styles.createButton,
                (!postTitle || !postContent) && styles.createButtonDisabled,
              ]}
              onPress={handleSubmit}
              disabled={!postTitle || !postContent}
            >
              <Text style={styles.createButtonText}>CREATE POST ✨</Text>
            </Pressable>
          </View>
        </View>
      </View>
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
    height: '95%',
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
    minHeight: 100,
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
