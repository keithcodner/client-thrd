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

interface CircleInfoModalProps {
  visible: boolean;
  onClose: () => void;
  circleName: string;
  circleId: string;
}

export const CircleInfoModal = ({
  visible,
  onClose,
  circleName,
  circleId,
}: CircleInfoModalProps) => {
  const [expandedSection, setExpandedSection] = useState<string | null>(null);

  const toggleSection = (section: string) => {
    setExpandedSection(expandedSection === section ? null : section);
  };

  const getInitials = (name: string) => {
    return name
      .split(' ')
      .map(word => word[0])
      .join('')
      .toUpperCase()
      .slice(0, 1);
  };

  return (
    <Modal
      visible={visible}
      animationType="slide"
      presentationStyle="pageSheet"
      onRequestClose={onClose}
    >
      <View style={styles.container}>
        {/* Header */}
        <View style={styles.header}>
          <Pressable onPress={onClose} style={styles.backButton}>
            <ChevronLeft size={24} color="#4A9EFF" />
            <Text style={styles.backText}>Back</Text>
          </Pressable>
          <Text style={styles.headerTitle}>Circle Info</Text>
          <View style={{ width: 60 }} />
        </View>

        <ScrollView style={styles.content}>
          {/* Avatar Section */}
          <View style={styles.avatarSection}>
            <View style={styles.avatarContainer}>
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{getInitials(circleName)}</Text>
              </View>
              <Pressable style={styles.cameraIcon}>
                <Camera size={16} color="#fff" />
              </Pressable>
            </View>
            <Text style={styles.circleName}>{circleName}</Text>
          </View>

          {/* Action Buttons */}
          <View style={styles.actionButtons}>
            <Pressable style={styles.actionButton}>
              <FileText size={20} color="#4A9EFF" />
              <Text style={styles.actionButtonText}>BOARD</Text>
            </Pressable>
            <Pressable style={styles.actionButton}>
              <UserPlus size={20} color="#4A9EFF" />
              <Text style={styles.actionButtonText}>INVITE</Text>
            </Pressable>
          </View>

          {/* Settings Sections */}
          <View style={styles.settingsContainer}>
            {/* Chat Style */}
            <Pressable
              style={styles.settingItem}
              onPress={() => toggleSection('chatStyle')}
            >
              <View style={styles.settingLeft}>
                <Palette size={20} color="#aaa" />
                <View style={styles.settingTextContainer}>
                  <Text style={styles.settingTitle}>Chat Style</Text>
                  <Text style={styles.settingSubtitle}>THEME & BACKGROUND</Text>
                </View>
              </View>
              <ChevronDown size={20} color="#aaa" />
            </Pressable>

            {/* Members */}
            <Pressable
              style={styles.settingItem}
              onPress={() => toggleSection('members')}
            >
              <View style={styles.settingLeft}>
                <Users size={20} color="#aaa" />
                <View style={styles.settingTextContainer}>
                  <Text style={styles.settingTitle}>Members</Text>
                  <Text style={styles.settingSubtitle}>1 PEOPLE</Text>
                </View>
              </View>
              <ChevronDown size={20} color="#aaa" />
            </Pressable>

            {/* Notifications */}
            <Pressable
              style={styles.settingItem}
              onPress={() => toggleSection('notifications')}
            >
              <View style={styles.settingLeft}>
                <Bell size={20} color="#aaa" />
                <View style={styles.settingTextContainer}>
                  <Text style={styles.settingTitle}>Notifications</Text>
                  <Text style={styles.settingSubtitle}>OFF</Text>
                </View>
              </View>
              <ChevronDown size={20} color="#aaa" />
            </Pressable>

            {/* Circle Privacy */}
            <Pressable
              style={styles.settingItem}
              onPress={() => toggleSection('privacy')}
            >
              <View style={styles.settingLeft}>
                <Lock size={20} color="#aaa" />
                <View style={styles.settingTextContainer}>
                  <Text style={styles.settingTitle}>Circle Privacy</Text>
                  <Text style={styles.settingSubtitle}>MEMBERS ONLY</Text>
                </View>
              </View>
              <ChevronDown size={20} color="#aaa" />
            </Pressable>
          </View>

          {/* Leave Circle Button */}
          <Pressable style={styles.leaveButton}>
            <Text style={styles.leaveButtonText}>Leave Circle</Text>
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
    backgroundColor: '#1a1a1a',
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingTop: 50,
    paddingBottom: 16,
    paddingHorizontal: 20,
    backgroundColor: '#1a1a1a',
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
    width: 60,
  },
  backText: {
    color: '#4A9EFF',
    fontSize: 16,
    marginLeft: 2,
  },
  headerTitle: {
    color: '#fff',
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
    backgroundColor: '#6B7A4F',
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
    backgroundColor: '#4A9EFF',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 3,
    borderColor: '#1a1a1a',
  },
  circleName: {
    color: '#fff',
    fontSize: 20,
    fontWeight: '600',
  },
  actionButtons: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 40,
    paddingVertical: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#2a2a2a',
  },
  actionButton: {
    alignItems: 'center',
  },
  actionButtonText: {
    color: '#4A9EFF',
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
    borderBottomColor: '#2a2a2a',
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
    color: '#fff',
    fontSize: 16,
    marginBottom: 4,
  },
  settingSubtitle: {
    color: '#999',
    fontSize: 11,
    fontWeight: '600',
    letterSpacing: 0.5,
  },
  leaveButton: {
    marginHorizontal: 16,
    marginTop: 24,
    paddingVertical: 16,
    backgroundColor: '#8B1A1A',
    borderRadius: 8,
    alignItems: 'center',
  },
  leaveButtonText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
  },
});
