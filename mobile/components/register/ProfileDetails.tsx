import React from "react";
import { Text, View, TextInput, ScrollView } from "react-native";
import { Globe } from "lucide-react-native";
import { useThemeColours } from "@/hooks/useThemeColours";

type ProfileDetailsProps = {
  businessName: string;
  streetAddress: string;
  hours: string;
  capacity: string;
  website: string;
  instagram: string;
  tiktok: string;
  primaryCity: string;
  onBusinessNameChange: (value: string) => void;
  onStreetAddressChange: (value: string) => void;
  onHoursChange: (value: string) => void;
  onCapacityChange: (value: string) => void;
  onWebsiteChange: (value: string) => void;
  onInstagramChange: (value: string) => void;
  onTiktokChange: (value: string) => void;
  onPrimaryCityChange: (value: string) => void;
};

export const ProfileDetails: React.FC<ProfileDetailsProps> = ({
  businessName,
  streetAddress,
  hours,
  capacity,
  website,
  instagram,
  tiktok,
  primaryCity,
  onBusinessNameChange,
  onStreetAddressChange,
  onHoursChange,
  onCapacityChange,
  onWebsiteChange,
  onInstagramChange,
  onTiktokChange,
  onPrimaryCityChange,
}) => {
  const colors = useThemeColours();

  return (
    <ScrollView className="w-full px-8" showsVerticalScrollIndicator={false}>
      {/* Business Name */}
      <View className="mb-4">
        <Text
          style={{
            color: colors.secondaryText,
            fontSize: 12,
            fontWeight: '600',
            letterSpacing: 1,
            marginBottom: 12,
          }}
        >
          BUSINESS NAME
        </Text>
        <TextInput
          value={businessName}
          onChangeText={onBusinessNameChange}
          placeholder="The Indigo Studio"
          placeholderTextColor={colors.secondaryText}
          style={{
            backgroundColor: colors.card,
            color: colors.text,
            fontSize: 16,
            paddingVertical: 16,
            paddingHorizontal: 16,
            borderRadius: 16,
          }}
        />
      </View>

      {/* Physical Location */}
      <View className="mb-4">
        <Text
          style={{
            color: colors.secondaryText,
            fontSize: 12,
            fontWeight: '600',
            letterSpacing: 1,
            marginBottom: 12,
          }}
        >
          PHYSICAL LOCATION
        </Text>
        <TextInput
          value={streetAddress}
          onChangeText={onStreetAddressChange}
          placeholder="Street Address"
          placeholderTextColor={colors.secondaryText}
          style={{
            backgroundColor: colors.card,
            color: colors.text,
            fontSize: 16,
            paddingVertical: 16,
            paddingHorizontal: 16,
            borderRadius: 16,
            marginBottom: 12,
          }}
        />
        <View className="flex-row gap-3">
          <TextInput
            value={hours}
            onChangeText={onHoursChange}
            placeholder="Hours (e.g. 9-5)"
            placeholderTextColor={colors.secondaryText}
            style={{
              flex: 1,
              backgroundColor: colors.card,
              color: colors.text,
              fontSize: 14,
              paddingVertical: 14,
              paddingHorizontal: 14,
              borderRadius: 16,
            }}
          />
          <TextInput
            value={capacity}
            onChangeText={onCapacityChange}
            placeholder="Capacity"
            placeholderTextColor={colors.secondaryText}
            style={{
              flex: 1,
              backgroundColor: colors.card,
              color: colors.text,
              fontSize: 14,
              paddingVertical: 14,
              paddingHorizontal: 14,
              borderRadius: 16,
            }}
          />
        </View>
      </View>

      {/* Website */}
      <View className="mb-4">
        <Text
          style={{
            color: colors.secondaryText,
            fontSize: 12,
            fontWeight: '600',
            letterSpacing: 1,
            marginBottom: 12,
          }}
        >
          WEBSITE
        </Text>
        <View
          style={{
            flexDirection: 'row',
            alignItems: 'center',
            backgroundColor: colors.card,
            borderRadius: 16,
            paddingLeft: 16,
          }}
        >
          <Globe size={18} color={colors.secondaryText} strokeWidth={2} />
          <TextInput
            value={website}
            onChangeText={onWebsiteChange}
            placeholder="https://..."
            placeholderTextColor={colors.secondaryText}
            autoCapitalize="none"
            style={{
              flex: 1,
              color: colors.text,
              fontSize: 16,
              paddingVertical: 16,
              paddingHorizontal: 12,
            }}
          />
        </View>
      </View>

      {/* Social Media */}
      <View className="flex-row gap-3 mb-4">
        <View className="flex-1">
          <Text
            style={{
              color: colors.secondaryText,
              fontSize: 12,
              fontWeight: '600',
              letterSpacing: 1,
              marginBottom: 12,
            }}
          >
            INSTAGRAM
          </Text>
          <View
            style={{
              flexDirection: 'row',
              alignItems: 'center',
              backgroundColor: colors.card,
              borderRadius: 16,
              paddingLeft: 16,
            }}
          >
            <Text style={{ color: colors.secondaryText, fontSize: 16 }}>@</Text>
            <TextInput
              value={instagram}
              onChangeText={onInstagramChange}
              placeholder="handle"
              placeholderTextColor={colors.secondaryText}
              autoCapitalize="none"
              style={{
                flex: 1,
                color: colors.text,
                fontSize: 16,
                paddingVertical: 16,
                paddingHorizontal: 8,
              }}
            />
          </View>
        </View>

        <View className="flex-1">
          <Text
            style={{
              color: colors.secondaryText,
              fontSize: 12,
              fontWeight: '600',
              letterSpacing: 1,
              marginBottom: 12,
            }}
          >
            TIKTOK (OPTIONAL)
          </Text>
          <View
            style={{
              flexDirection: 'row',
              alignItems: 'center',
              backgroundColor: colors.card,
              borderRadius: 16,
              paddingLeft: 16,
            }}
          >
            <Text style={{ color: colors.secondaryText, fontSize: 16 }}>@</Text>
            <TextInput
              value={tiktok}
              onChangeText={onTiktokChange}
              placeholder="handle"
              placeholderTextColor={colors.secondaryText}
              autoCapitalize="none"
              style={{
                flex: 1,
                color: colors.text,
                fontSize: 16,
                paddingVertical: 16,
                paddingHorizontal: 8,
              }}
            />
          </View>
        </View>
      </View>

      {/* Primary City */}
      <View className="mb-6">
        <Text
          style={{
            color: colors.secondaryText,
            fontSize: 12,
            fontWeight: '600',
            letterSpacing: 1,
            marginBottom: 12,
          }}
        >
          PRIMARY CITY
        </Text>
        <TextInput
          value={primaryCity}
          onChangeText={onPrimaryCityChange}
          placeholder="e.g. Toronto"
          placeholderTextColor={colors.secondaryText}
          style={{
            backgroundColor: colors.card,
            color: colors.text,
            fontSize: 16,
            paddingVertical: 16,
            paddingHorizontal: 16,
            borderRadius: 16,
          }}
        />
      </View>
    </ScrollView>
  );
};
