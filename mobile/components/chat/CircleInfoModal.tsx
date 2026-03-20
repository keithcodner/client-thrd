import React, { useState } from 'react';
import {
  View,
  Text,
  Modal,
  Pressable,
  ScrollView,
  StyleSheet,
  TextInput,
  ActivityIndicator,
} from 'react-native';
import { ChevronLeft, ChevronDown, Camera, FileText, UserPlus, Palette, Users, Bell, Lock, X } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { getInitials, getAvatarColor } from '@/utils/avatarUtils';
import { searchUsersForInvite } from '@/services/chatService';

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
  const [showInviteSection, setShowInviteSection] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [searchResults, setSearchResults] = useState<any[]>([]);
  const [isSearching, setIsSearching] = useState(false);
  const colors = useThemeColours();

  const toggleSection = (section: string) => {
    setExpandedSection(expandedSection === section ? null : section);
  };

  const handleInviteClick = () => {
    setShowInviteSection(!showInviteSection);
    if (!showInviteSection) {
      setSearchQuery('');
      setSearchResults([]);
    }
  };

  const handleSearchChange = async (text: string) => {
    setSearchQuery(text);
    
    if (text.trim().length === 0) {
      setSearchResults([]);
      return;
    }

    setIsSearching(true);
    try {
      const results = await searchUsersForInvite(text);
      setSearchResults(results);
    } catch (error) {
      console.error('Error searching users:', error);
      setSearchResults([]);
    } finally {
      setIsSearching(false);
    }
  };

  const handleSendInvite = (userId: number) => {
    // TODO: Implement invite functionality
    console.log('Send invite to user:', userId);
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
            <Pressable style={styles.actionButton} onPress={handleInviteClick}>
              <UserPlus size={20} color={colors.info} />
              <Text style={[styles.actionButtonText, { color: colors.info }]}>INVITE</Text>
            </Pressable>
          </View>

          {/* Invite Search Section */}
          {showInviteSection && (
            <View style={[styles.inviteSection, { backgroundColor: colors.card, borderBottomColor: colors.border }]}>
              <View style={styles.inviteHeader}>
                <Text style={[styles.inviteTitle, { color: colors.text }]}>Invite Users</Text>
                <Pressable onPress={handleInviteClick} style={styles.closeButton}>
                  <X size={20} color={colors.secondaryText} />
                </Pressable>
              </View>
              
              <TextInput
                style={[styles.searchInput, { 
                  backgroundColor: colors.background, 
                  color: colors.text,
                  borderColor: colors.border 
                }]}
                placeholder="Search users..."
                placeholderTextColor={colors.secondaryText}
                value={searchQuery}
                onChangeText={handleSearchChange}
              />

              {isSearching && (
                <View style={styles.loadingContainer}>
                  <ActivityIndicator size="small" color={colors.info} />
                </View>
              )}

              <ScrollView style={styles.searchResults} nestedScrollEnabled>
                {searchResults.map((user) => {
                  const displayName = user.firstname || user.name || user.username || 'User';
                  return (
                  <View key={user.id} style={[styles.userResultItem, { borderBottomColor: colors.border }]}>
                    <View style={styles.userResultLeft}>
                      <View style={[styles.userAvatar, { backgroundColor: getAvatarColor(displayName) }]}>
                        <Text style={styles.userAvatarText}>{getInitials(displayName)}</Text>
                      </View>
                      <Text style={[styles.userName, { color: colors.text }]}>{displayName}</Text>
                    </View>
                    <Pressable 
                      style={[styles.inviteButton, { backgroundColor: colors.info }]}
                      onPress={() => handleSendInvite(user.id)}
                    >
                      <Text style={styles.inviteButtonText}>Invite</Text>
                    </Pressable>
                  </View>
                  );
                })}
                {!isSearching && searchQuery.trim().length > 0 && searchResults.length === 0 && (
                  <Text style={[styles.noResultsText, { color: colors.secondaryText }]}>
                    No users found
                  </Text>
                )}
              </ScrollView>
            </View>
          )}

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
  inviteSection: {
    paddingHorizontal: 16,
    paddingVertical: 16,
    borderBottomWidth: 1,
  },
  inviteHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  inviteTitle: {
    fontSize: 16,
    fontWeight: '600',
  },
  closeButton: {
    padding: 4,
  },
  searchInput: {
    height: 44,
    borderRadius: 8,
    borderWidth: 1,
    paddingHorizontal: 12,
    fontSize: 16,
    marginBottom: 12,
  },
  loadingContainer: {
    padding: 20,
    alignItems: 'center',
  },
  searchResults: {
    maxHeight: 250,
  },
  userResultItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 12,
    borderBottomWidth: 1,
  },
  userResultLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  userAvatar: {
    width: 40,
    height: 40,
    borderRadius: 20,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  userAvatarText: {
    color: '#fff',
    fontSize: 16,
    fontWeight: '600',
    fontStyle: 'italic',
  },
  userName: {
    fontSize: 16,
  },
  inviteButton: {
    paddingHorizontal: 16,
    paddingVertical: 8,
    borderRadius: 6,
  },
  inviteButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '600',
  },
  noResultsText: {
    textAlign: 'center',
    paddingVertical: 20,
    fontSize: 14,
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
