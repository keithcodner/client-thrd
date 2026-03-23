import React from 'react';
import { View, Text, ScrollView, ActivityIndicator } from 'react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { getInitials, getAvatarColor } from '@/utils/avatarUtils';
import { CircleMember } from './types';

interface MembersListProps {
  visible: boolean;
  members: CircleMember[];
  onlineUsers: Set<number>;
  isLoading: boolean;
}

/**
 * MembersList Component
 * 
 * Displays a scrollable list of circle members with their online/offline status.
 * Shows loading state while fetching members and empty state when no members found.
 * 
 * Features:
 * - Avatar with user initials
 * - Online/offline status indicator (green dot for online, gray for offline)
 * - Owner badge for circle owners
 * - Scrollable list with max height
 * 
 * @param visible - Whether the members list is visible
 * @param members - Array of circle members
 * @param onlineUsers - Set of user IDs that are currently online
 * @param isLoading - Whether members are currently being loaded
 */
export const MembersList: React.FC<MembersListProps> = ({
  visible,
  members,
  onlineUsers,
  isLoading,
}) => {
  const colours = useThemeColours();

  if (!visible) return null;

  return (
    <ScrollView
      style={{ 
        backgroundColor: colours.card,
        maxHeight: 300,
      }}
      className="mx-5 mt-2 rounded-lg"
    >
      {isLoading ? (
        <View className="py-8 items-center">
          <ActivityIndicator size="small" color={colours.primary} />
          <Text className="mt-2 text-sm" style={{ color: colours.secondaryText }}>
            Loading members...
          </Text>
        </View>
      ) : members.length === 0 ? (
        <View className="py-8 items-center">
          <Text className="text-sm" style={{ color: colours.secondaryText }}>
            No members found
          </Text>
        </View>
      ) : (
        members.map((member, index) => {
          const isOnline = onlineUsers.has(member.id);
          const isOwner = member.type === 'owner';
          const isLastItem = index === members.length - 1;
          
          return (
            <View
              key={member.id}
              className={`flex-row items-center px-4 py-3 ${!isLastItem ? 'border-b' : ''}`}
              style={{ borderBottomColor: colours.border }}
            >
              {/* Avatar */}
              <View className="relative">
                <View
                  className="w-10 h-10 rounded-full items-center justify-center"
                  style={{ backgroundColor: getAvatarColor(member.name) }}
                >
                  <Text className="text-white font-semibold text-sm">
                    {getInitials(member.name)}
                  </Text>
                </View>
                {/* Online Status Indicator */}
                <View
                  className="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2"
                  style={{
                    backgroundColor: isOnline ? '#10B981' : '#6B7280',
                    borderColor: colours.card,
                  }}
                />
              </View>

              {/* Member Info */}
              <View className="flex-1 ml-3">
                <View className="flex-row items-center">
                  <Text
                    className="text-base font-medium"
                    style={{ color: colours.text }}
                  >
                    {member.name}
                  </Text>
                  {isOwner && (
                    <View
                      className="ml-2 px-2 py-0.5 rounded"
                      style={{ backgroundColor: colours.primary }}
                    >
                      <Text className="text-xs font-semibold text-white">
                        OWNER
                      </Text>
                    </View>
                  )}
                </View>
                <Text
                  className="text-xs mt-0.5"
                  style={{ color: colours.secondaryText }}
                >
                  {isOnline ? 'Online' : 'Offline'}
                </Text>
              </View>
            </View>
          );
        })
      )}
    </ScrollView>
  );
};
