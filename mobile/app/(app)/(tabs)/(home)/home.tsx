import React, { useState, useEffect } from "react";
import {
  View,
  ScrollView,
  StyleSheet,
} from "react-native";
import { format, isValid, subDays, isSameDay } from "date-fns";
import { parseSmartDate } from "@/services/calendarService";
import { useThemeColours } from "@/hooks/useThemeColours";
import { HomeHeader } from "@/components/home/HomeHeader";
import { HomeGreeting } from "@/components/home/HomeGreeting";
import { DiscoverCard } from "@/components/home/DiscoverCard";
import { MyCircles } from "@/components/home/MyCircles";
import { UpcomingPlans } from "@/components/home/UpcomingPlans";
import { MindSpaceCard } from "@/components/home/MindSpaceCard";
import { FAB } from "@/components/FAB";

interface HomeProps {
  currentUser?: any;
  spaces?: any[];
  groups?: any[];
  todos?: any[];
  notificationsCount?: number;
  onNavigate?: (screen: string) => void;
  onOpenProfile?: () => void;
  onSelectGroup?: (id: string) => void;
  onSelectSpace?: (id: string) => void;
  onOpenNotifications?: () => void;
  onAddEvent?: () => void;
  onHostEvent?: () => void;
  onCreateGroup?: () => void;
  showHint?: boolean;
}

// Dummy data for development
const DUMMY_SPACES = [
  {
    id: "1",
    name: "Coffee Lovers",
    description: "For coffee enthusiasts",
    image: "https://via.placeholder.com/400x220?text=Coffee+Lovers",
    trendingScore: 85,
    ownerId: "user1",
  },
  {
    id: "2",
    name: "Tech Meetups",
    description: "Weekly tech discussions",
    image: "https://via.placeholder.com/400x220?text=Tech+Meetups",
    trendingScore: 72,
    ownerId: "user2",
  },
];

const DUMMY_GROUPS = [
  {
    id: "g1",
    name: "THRD",
    description: "Main group",
    customization: {
      headerBanner: null,
    },
    isPinned: true,
  },
  {
    id: "g2",
    name: "Friends",
    description: "Close friends",
    customization: {
      headerBanner: null,
    },
    isPinned: true,
  },
  {
    id: "g3",
    name: "Work Squad",
    description: "Colleagues",
    customization: {
      headerBanner: null,
    },
    isPinned: true,
  },
  {
    id: "g4",
    name: "Gym Buddies",
    description: "Fitness enthusiasts",
    customization: {
      headerBanner: null,
    },
    isPinned: true,
  },
  {
    id: "g5",
    name: "Book Club",
    description: "Monthly reads",
    customization: {
      headerBanner: null,
    },
    isPinned: true,
  },
  {
    id: "g6",
    name: "Foodies",
    description: "Restaurant explorers",
    customization: {
      headerBanner: null,
    },
    isPinned: false,
  },
  {
    id: "g7",
    name: "Gamers",
    description: "Gaming nights",
    customization: {
      headerBanner: null,
    },
    isPinned: false,
  },
  {
    id: "g8",
    name: "Hikers",
    description: "Nature lovers",
    customization: {
      headerBanner: null,
    },
    isPinned: false,
  },
];

const DUMMY_TODOS = [
  {
    id: "t1",
    title: "Coffee with Sarah",
    description: "Chat at the local cafe",
    date: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000).toISOString(),
    status: "confirmed",
    taggedGroupIds: ["g1"],
  },
  {
    id: "t2",
    title: "Team Standup",
    description: "Weekly sync",
    date: new Date(Date.now() + 1 * 24 * 60 * 60 * 1000).toISOString(),
    status: "confirmed",
    taggedGroupIds: ["g3"],
  },
  {
    id: "t3",
    title: "Gym Session",
    description: "Evening workout",
    date: new Date(Date.now() + 3 * 24 * 60 * 60 * 1000).toISOString(),
    status: "confirmed",
    taggedGroupIds: ["g2"],
  },
];

export const Home = ({
  currentUser = { name: "Alex", avatar: null },
  spaces = DUMMY_SPACES,
  groups = DUMMY_GROUPS,
  todos = DUMMY_TODOS,
  notificationsCount = 2,
  onNavigate = () => {},
  onOpenProfile = () => {},
  onSelectGroup = () => {},
  onSelectSpace = () => {},
  onOpenNotifications = () => {},
  onAddEvent = () => {},
  onHostEvent = () => {},
  onCreateGroup = () => {},
  showHint = false,
}: HomeProps = {}) => {
  const colors = useThemeColours();
  const [reflectionTodo, setReflectionTodo] = useState(null);
  const firstName = (currentUser?.name || "Friend").split(" ")[0];
  const hour = new Date().getHours();
  const greeting =
    hour < 12 ? "Good morning" : hour < 18 ? "Good afternoon" : "Good evening";

  const trendingSpace =
    spaces.length > 0
      ? spaces.find((s) => s.trendingScore > 80) || spaces[0]
      : null;

  const mySpaces = spaces.filter((s) => s.ownerId === currentUser?.id);

  const recentGroups = [...groups]
    .sort((a, b) => (a.isPinned === b.isPinned ? 0 : a.isPinned ? -1 : 1))
    .slice(0, 8);

  const upcomingTodos = todos
    .filter((t) => t.status === "confirmed")
    .slice(0, 4);

  useEffect(() => {
    const yesterday = subDays(new Date(), 1);

    const completedYesterday = todos.find((t) => {
      if (!t.date || t.reflection) return false;
      const parsed = parseSmartDate(t.date);
      return isSameDay(parsed, yesterday) && t.status === "confirmed";
    });

    if (completedYesterday) setReflectionTodo(completedYesterday);
  }, [todos]);

  const getTodoDisplayInfo = (todo: any) => {
    let dateDisplay = "Soon";
    let timeDisplay = "";

    if (todo.date) {
      const parsed = new Date(todo.date);

      if (isValid(parsed)) {
        dateDisplay = format(parsed, "EEE, MMM d");
        timeDisplay = format(parsed, "h:mm a");
      }
    }

    const associatedGroup = groups.find((g: any) =>
      (todo.taggedGroupIds || []).includes(g.id)
    );

    return {
      date: dateDisplay,
      time: timeDisplay,
      circleName: associatedGroup?.name || "Personal Plan",
    };
  };

  return (
    <View style={[styles.container, { backgroundColor: colors.background }]}>
      <ScrollView showsVerticalScrollIndicator={false}>
        <HomeHeader
          colors={colors}
          notificationsCount={notificationsCount}
          onNavigate={onNavigate}
          onOpenNotifications={onOpenNotifications}
          onOpenProfile={onOpenProfile}
        />

        <HomeGreeting
          colors={colors}
          greeting={greeting}
          firstName={firstName}
        />

        <DiscoverCard
          colors={colors}
          trendingSpace={trendingSpace}
          onPress={() => onSelectSpace?.(trendingSpace?.id)}
        />

        <MyCircles
          colors={colors}
          groups={recentGroups}
          onNavigate={onNavigate}
          onSelectGroup={onSelectGroup}
        />

        <UpcomingPlans
          colors={colors}
          todos={upcomingTodos}
          groups={groups}
          onNavigate={onNavigate}
        />

        <MindSpaceCard
          colors={colors}
          onPress={() => onNavigate?.("mind-space")}
        />

        <View style={{ height: 120 }} />
      </ScrollView>

      <FAB
        colors={colors}
        onCoordinate={() => onAddEvent?.()}
        onCreateCircle={() => onCreateGroup?.()}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
});

export default Home;
