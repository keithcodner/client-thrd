import React, { useState } from 'react';
import {
  View,
  Text,
  Modal,
  Pressable,
  ScrollView,
  StyleSheet,
} from 'react-native';
import { ChevronLeft, ChevronDown, Camera, FileText, UserPlus, Palette, Users, Bell, Lock } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { getInitials, getAvatarColor } from '@/utils/avatarUtils';

interface CircleInfoModalProps {
  visible: boolean;
  onClose: () => void;
  circleName: string;
  circleId: string;
  isOwner?: boolean;
  onLeave?: () => void;
  onDelete?: () => void;
}

export const CircleInfoModal = ({
  visible,
  onClose,
  circleName,
  circleId,
  isOwner = false,
  onLeave,
  onDelete,
}: CircleInfoModalProps) => {
  const [expandedSection, setExpandedSection] = useState<string | null>(null);
  const colors = useThemeColours();

  const toggleSection = (section: string) => {
    setExpandedSection(expandedSection === section ? null : section);
  };

  return (
    <Modal
      visible={visible}
      animationType="slide"
      presentationStyle="pageSheet"
      onRequestClose={onClose}
    >
      <View style={[styles.container, { backgroundColor: colors.background }]}>
        {/* Header */}
        <View style={[styles.header, { backgroundColor: colors.background }]}>
          <Pressable onPress={onClose} style={styles.backButton}>
            <ChevronLeft size={24} color={colors.info} />
            <Text style={[styles.backText, { color: colors.info }]}>Back</Text>
          </Pressable>
          <Text style={[styles.headerTitle, { color: colors.text }]}>Circle Info</Text>
          <View style={{ width: 60 }} />
        </View>

        <ScrollView style={styles.content}>
          {/* Avatar Section */}
          <View style={styles.avatarSection}>
            <View style={styles.avatarContainer}>
              <View style={[styles.avatar, { backgroundColor: getAvatarColor(circleName) }]}>
                <Text style={styles.avatarText}>{getInitials(circleName)}</Text>
              </View>
              <Pressable style={[styles.cameraIcon, { borderColor: colors.background, backgroundColor: colors.info }]}>
                <Camera size={16} color="#fff" />
              </Pressable>
            </View>
            <Text style={[styles.circleName, { color: colors.text }]}>{ circleName}</Text>
          </View>

          {/* Action Buttons */}
          <View style={[styles.actionButtons, { borderBottomColor: colors.border }]}>
            <Pressable style={styles.actionButton}>
              <FileText size={20} color={colors.info} />
              <Text style={[styles.actionButtonText, { color: colors.info }]}>BOARD</Text>
            </Pressable>
            <Pressable style={styles.actionButton}>
              <UserPlus size={20} color={colors.info} />
              <Text style={[styles.actionButtonText, { color: colors.info }]}>INVITE</Text>
            </Pressable>
          </View>

          {/* Settings Sections */}
          <View style={styles.settingsContainer}>
            {/* Chat Style */}
            <Pressable
              style={[styles.settingItem, { borderBottomColor: colors.border }]}
              onPress={() => toggleSection('chatStyle')}
            >
              <View style={styles.settingLeft}>
                <Palette size={20} color={colors.secondaryText} />
                <View style={styles.settingTextContainer}>
                  <Text style={[styles.settingTitle, { color: colors.text }]}>Chat Style</Text>
                  <Text style={[styles.settingSubtitle, { color: colors.secondaryText }]}>THEME & BACKGROUND</Text>
                </View>
              </View>
              <ChevronDown size={20} color={colors.secondaryText} />
            </Pressable>

            {/* Members */}
            <Pressable
              style={[styles.settingItem, { borderBottomColor: colors.border }]}
              onPress={() => toggleSection('members')}
            >
              <View style={styles.settingLeft}>
                <Users size={20} color={colors.secondaryText} />
                <View style={styles.settingTextContainer}>
                  <Text style={[styles.settingTitle, { color: colors.text }]}>Members</Text>
                  <Text style={[styles.settingSubtitle, { color: colors.secondaryText }]}>1 PEOPLE</Text>
                </View>
              </View>
              <ChevronDown size={20} color={colors.secondaryText} />
            </Pressable>

            {/* Notifications */}
            <Pressable
              style={[styles.settingItem, { borderBottomColor: colors.border }]}
              onPress={() => toggleSection('notifications')}
            >
              <View style={styles.settingLeft}>
                <Bell size={20} color={colors.secondaryText} />
                <View style={styles.settingTextContainer}>
                  <Text style={[styles.settingTitle, { color: colors.text }]}>Notifications</Text>
                  <Text style={[styles.settingSubtitle, { color: colors.secondaryText }]}>OFF</Text>
                </View>
              </View>
              <ChevronDown size={20} color={colors.secondaryText} />
            </Pressable>

            {/* Circle Privacy */}
            <Pressable
              style={[styles.settingItem, { borderBottomColor: colors.border }]}
              onPress={() => toggleSection('privacy')}
            >
              <View style={styles.settingLeft}>
                <Lock size={20} color={colors.secondaryText} />
                <View style={styles.settingTextContainer}>
                  <Text style={[styles.settingTitle, { color: colors.text }]}>Circle Privacy</Text>
                  <Text style={[styles.settingSubtitle, { color: colors.secondaryText }]}>MEMBERS ONLY</Text>
                </View>
              </View>
              <ChevronDown size={20} color={colors.secondaryText} />
            </Pressable>
          </View>

          {/* Leave/Delete Circle Button */}
          <Pressable 
            style={[styles.leaveButton, { backgroundColor: colors.error }]}
            onPress={() => {
              if (isOwner && onDelete) {
                onDelete();
              } else if (!isOwner && onLeave) {
                onLeave();
              }
            }}
          >
            <Text style={styles.leaveButtonText}>
              {isOwner ? 'Delete Circle' : 'Leave Circle'}
            </Text>
          </Pressable>

          <View style={{ height: 40 }} />
        </ScrollView>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 50,
    paddingBottom: 16,
    paddingHorizontal: 20,
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
    width: 60,
  },
  backText: {
    fontSize: 16,
    marginLeft: 2,
  },
  headerTitle: {
    fontSize: 16,
    fontWeight: '600',
  },
  content: {
    flex: 1,
  },
  avatarSection: {
    alignItems: 'center',
    paddingVertical: 24,
  },
  avatarContainer: {
    position: 'relative',
    marginBottom: 16,
  },
  avatar: {
    width: 100,
    height: 100,
    borderRadius: 50,
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: {
    color: '#fff',
    fontSize: 40,
    fontWeight: '600',
    fontStyle: 'italic',
  },
  cameraIcon: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    width: 32,
    height: 32,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 3,
  },
  circleName: {
    fontSize: 20,
    fontWeight: '600',
  },
  actionButtons: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 40,
    paddingVertical: 20,
    borderBottomWidth: 1,
  },
  actionButton: {
    alignItems: 'center',
  },
  actionButtonText: {
    fontSize: 12,
    fontWeight: '600',
    marginTop: 8,
    letterSpacing: 0.5,
  },
  settingsContainer: {
    paddingTop: 8,
  },
  settingItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 16,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
  },
  settingLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  settingTextContainer: {
    marginLeft: 16,
    flex: 1,
  },
  settingTitle: {
    fontSize: 16,
    marginBottom: 4,
  },
  settingSubtitle: {
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
  leaveButton: {
    marginHorizontal: 16,
    marginTop: 24,
    paddingVertical: 16,
    borderRadius: 8,
    alignItems: 'center',
  },
  leaveButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
