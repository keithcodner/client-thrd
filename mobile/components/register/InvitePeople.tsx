import React, { useState } from "react";
import { Text, View, TouchableOpacity } from "react-native";
import { UserPlus } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

type InvitePeopleProps = {
  onContinue: () => void;
  onSkip: () => void;
};

export const InvitePeople: React.FC<InvitePeopleProps> = ({
  onContinue,
  onSkip,
}) => {
  const colors = useThemeColours();
  const [invites, setInvites] = useState<(string | null)[]>([null, null, null]);

  const handleAddInvite = (index: number) => {
    // TODO: Implement phone/email input for invites
    const newInvites = [...invites];
    newInvites[index] = "invited";
    setInvites(newInvites);
  };

  return (
    <View className="w-full px-8 items-center">
      {/* Icon */}
      <View
        style={{
          width: 120,
          height: 120,
          borderRadius: 60,
          backgroundColor: 'rgba(59, 130, 246, 0.15)',
          alignItems: 'center',
          justifyContent: 'center',
          marginBottom: 32,
        }}
      >
        <View
          style={{
            width: 80,
            height: 80,
            borderRadius: 40,
            backgroundColor: 'rgba(59, 130, 246, 0.3)',
            alignItems: 'center',
            justifyContent: 'center',
          }}
        >
          <UserPlus size={40} color="#3b82f6" strokeWidth={2} />
        </View>
      </View>

      {/* Invite Slots */}
      <View
        style={{
          flexDirection: 'row',
          justifyContent: 'space-between',
          width: '100%',
          maxWidth: 300,
          marginBottom: 48,
        }}
      >
        {[0, 1, 2].map((index) => (
          <TouchableOpacity
            key={index}
            onPress={() => handleAddInvite(index)}
            style={{
              width: 80,
              height: 80,
              borderRadius: 40,
              borderWidth: 2,
              borderStyle: 'dashed',
              borderColor: colors.secondaryText,
              alignItems: 'center',
              justifyContent: 'center',
              opacity: invites[index] ? 0.5 : 1,
            }}
          >
            <Text
              style={{
                fontSize: 32,
                color: colors.secondaryText,
                fontWeight: '300',
              }}
            >
              +
            </Text>
          </TouchableOpacity>
        ))}
      </View>

    </View>
  );
};
