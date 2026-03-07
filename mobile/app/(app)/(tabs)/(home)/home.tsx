import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  ScrollView,
  Image,
  StyleSheet,
  Pressable,
  Dimensions,
} from "react-native";
import {
  Bell,
  ChevronRight,
  MessageCircle,
  ArrowRight,
  Plus,
  Settings,
  HeartHandshake,
  User,
} from "lucide-react-native";
import { format, isValid, subDays, isSameDay } from "date-fns";
import { parseSmartDate } from "@/services/calendarService";
import { colours } from "@/constants/colours";
import { useThemeColours } from "@/hooks/useThemeColours";

const { width } = Dimensions.get("window");

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
        {/* HEADER */}
        <View style={styles.header} className="mt-8">
          <Pressable style={styles.logo}>
            <View style={[styles.logoBox, { backgroundColor: colors.primary }]}>
              <Text style={[styles.logoText, { color: colors.background }]} className="font-serif">T</Text>
            </View>
            <Text style={[styles.logoWord, { color: colors.text }]}>THRD</Text>
          </Pressable>

          <View style={styles.headerActions}>
            <Pressable style={[styles.iconButton, { backgroundColor: colors.card }]} onPress={() => onNavigate?.("settings")}>
              <Settings size={20} color={colors.secondaryText} />
            </Pressable>

            <Pressable style={[styles.iconButton, { backgroundColor: colors.card }]} onPress={onOpenNotifications}>
              <Bell size={20} color={colors.secondaryText} />
              {notificationsCount > 0 && (
                <View style={styles.notificationBadge}>
                  <Text style={styles.badgeText}>
                    {notificationsCount > 9 ? "9+" : notificationsCount}
                  </Text>
                </View>
              )}
            </Pressable>

            <Pressable style={[styles.iconButton, { backgroundColor: colors.card }]} onPress={onOpenProfile}>
              <User size={20} color={colors.secondaryText} />
            </Pressable>
          </View>
        </View>

        {/* GREETING */}
        <View style={styles.greetingContainer}>
          <Text style={[styles.greeting, { color: colors.text }]}>
            {greeting},{"\n"}
            <Text style={[styles.name, { color: colors.primary }]}>{firstName}</Text>
          </Text>
        </View>

        {/* DISCOVER CARD */}
        <Pressable
          style={styles.discoverCard}
          onPress={() => onSelectSpace?.(trendingSpace?.id)}
        >
          <Image
            source={require('@/assets/main-image-home.png')}
            style={styles.discoverImage}
          />

          <View style={[styles.discoverOverlay, { backgroundColor: `rgba(0, 0, 0, ${colors.background === '#FFFFFF' ? 0.3 : 0.45})` }]} />

          <View style={styles.discoverContent}>
            <Text style={[styles.discoverLabel, { color: colors.secondaryText }]}>FEATURED SPOT</Text>
            <Text style={[styles.discoverTitle, { color: colors.text }]}>
              Discover what's on this week.
            </Text>
            <Text style={[styles.discoverSubtitle, { color: colors.text }]}>
              {trendingSpace
                ? `Check out ${trendingSpace.name}...`
                : "See local spaces"}
            </Text>
          </View>
          
          <Pressable style={[styles.discoverArrowButton, { backgroundColor: colors.background === '#FFFFFF' ? 'rgba(255, 255, 255, 0.5)' : 'rgba(0, 0, 0, 0.5)' }]}>
            <ArrowRight size={20} color={colors.text} />
          </Pressable>
        </Pressable>

        {/* MY CIRCLES */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={[styles.sectionTitle, { color: colors.text }]}>MY CIRCLES</Text>
            <Pressable onPress={() => onNavigate?.("explore")}>
              <Text style={[styles.seeAllLink, { color: colors.primary }]}>See All</Text>
            </Pressable>
          </View>

          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            style={styles.circlesScroll}
          >
            {recentGroups.length > 0 ? (
              recentGroups.map((group, idx) => (
                <Pressable
                  key={group.id}
                  style={styles.groupItem}
                  onPress={() => onSelectGroup?.(group.id)}
                >
                  <View style={[styles.groupCircle, { backgroundColor: colors.surface, borderColor: colors.border }]}>
                    {group.customization?.headerBanner ? (
                      <Image
                        source={{ uri: group.customization.headerBanner }}
                        style={styles.groupCircleImage}
                      />
                    ) : idx === 0 ? (
                      <HeartHandshake size={28} color={colors.primary} />
                    ) : (
                      <MessageCircle size={28} color={colors.primary} />
                    )}
                    {idx === 0 && (
                      <View style={[styles.circleBadge, { backgroundColor: colors.primary }]}>
                        <Text style={[styles.circleBadgeText, { color: colors.background }]}>★</Text>
                      </View>
                    )}
                  </View>
                  <Text numberOfLines={1} style={[styles.groupName, { color: colors.secondaryText }]}>
                    {group.name}
                  </Text>
                </Pressable>
              ))
            ) : (
              <Text style={[styles.emptyText, { color: colors.secondaryText }]}>No circles yet.</Text>
            )}
          </ScrollView>
        </View>

        {/* UPCOMING PLANS */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={[styles.sectionTitle, { color: colors.text }]}>UPCOMING PLANS</Text>
            <Pressable onPress={() => onNavigate?.("calendar")}>
              <Text style={[styles.seeAllLink, { color: colors.primary }]}>Full Calendar</Text>
            </Pressable>
          </View>

          {upcomingTodos.length > 0 ? (
            upcomingTodos.map((todo) => {
              const display = getTodoDisplayInfo(todo);

              return (
                <Pressable key={todo.id} style={[styles.todoCard, { backgroundColor: colors.card }]}>
                  <View style={[styles.todoDate, { backgroundColor: colors.surface }]}>
                    <Text style={[styles.todoDateText, { color: colors.text }]}>
                      {display.date.split(" ")[1]}
                    </Text>
                  </View>

                  <View style={{ flex: 1 }}>
                    <Text style={[styles.todoTitle, { color: colors.text }]}>{todo.title}</Text>
                    <Text style={[styles.todoMeta, { color: colors.secondaryText }]}>
                      {display.circleName} • {display.time}
                    </Text>
                  </View>

                  <ChevronRight size={18} color={colors.secondaryText} />
                </Pressable>
              );
            })
          ) : (
            <View style={[styles.emptyState, { borderColor: colors.border }]}>
              <Text style={[styles.emptyStateText, { color: colors.secondaryText }]}>
                No confirmed meetups yet.
              </Text>
            </View>
          )}
        </View>

        {/* MIND SPACE CARD */}
        <Pressable
          style={[styles.mindSpaceCard, { backgroundColor: colors.card }]}
          onPress={() => onNavigate?.("mind-space")}
        >
          <View style={[styles.mindSpaceIcon, { backgroundColor: colors.surface }]}>
            <HeartHandshake size={24} color={colors.primary} />
          </View>
          
          <View style={styles.mindSpaceContent}>
            <Text style={[styles.mindSpaceTitle, { color: colors.text }]}>Mind Space</Text>
            <Text style={[styles.mindSpaceSubtitle, { color: colors.secondaryText }]}>
              Quiet support for how time actually feels
            </Text>
          </View>
          
          <ArrowRight size={20} color={colors.secondaryText} />
          
          <Pressable style={[styles.mindSpacePlusButton, { backgroundColor: colors.surface }]}>
            <Plus size={20} color={colors.text} />
          </Pressable>
        </Pressable>

        <View style={{ height: 120 }} />
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },

  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 16,
    paddingTop: 16,
    paddingBottom: 16,
  },

  logo: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
  },

  logoBox: {
    width: 32,
    height: 32,
    backgroundColor: "#ADC178",
    justifyContent: "center",
    alignItems: "center",
    borderRadius: 6,
  },

  logoText: {
    color: "#15110fff",
    fontWeight: "700",
    fontSize: 18,
  },

  logoWord: {
    fontWeight: "700",
    fontSize: 16,
  },

  headerActions: {
    flexDirection: "row",
    gap: 12,
    alignItems: "center",
  },

  iconButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    justifyContent: "center",
    alignItems: "center",
  },

  notificationBadge: {
    position: "absolute",
    top: -4,
    right: -4,
    backgroundColor: "#ADC178",
    borderRadius: 10,
    minWidth: 20,
    height: 20,
    justifyContent: "center",
    alignItems: "center",
  },

  badgeText: {
    color: "#15110fff",
    fontSize: 10,
    fontWeight: "700",
  },

  avatarImage: {
    width: 40,
    height: 40,
    borderRadius: 20,
  },

  discoverCard: {
    height: 220,
    borderRadius: 24,
    marginHorizontal: 16,
    marginTop: 8,
    marginBottom: 24,
    overflow: "hidden",
    backgroundColor: "#1a1a1a",
  },

  discoverImage: {
    position: "absolute",
    width: "100%",
    height: "100%",
  },

  discoverImagePlaceholder: {
    position: "absolute",
    width: "100%",
    height: "100%",
    backgroundColor: "#2a2a2a",
  },

  discoverOverlay: {
    ...StyleSheet.absoluteFillObject,
  },

  discoverContent: {
    flex: 1,
    justifyContent: "flex-end",
    padding: 20,
  },

  discoverLabel: {
    fontSize: 10,
    fontWeight: "700",
    letterSpacing: 1.2,
    marginBottom: 8,
  },

  discoverTitle: {
    fontSize: 24,
    fontWeight: "600",
    marginBottom: 8,
    lineHeight: 30,
  },

  discoverSubtitle: {
    fontSize: 13,
  },

  discoverArrowButton: {
    position: "absolute",
    bottom: 16,
    right: 16,
    width: 44,
    height: 44,
    borderRadius: 22,
    justifyContent: "center",
    alignItems: "center",
  },

  section: {
    marginBottom: 28,
  },

  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 20,
    marginBottom: 12,
  },

  sectionTitle: {
    fontSize: 11,
    fontWeight: "800",
    color: "#fff",
    letterSpacing: 1.2,
  },

  seeAllLink: {
    fontSize: 12,
    color: colours.light.accent,
    fontWeight: "600",
  },

  circlesScroll: {
    paddingLeft: 8,
    paddingRight: 12,
  },

  groupItem: {
    alignItems: "center",
    marginHorizontal: 12,
  },

  groupCircle: {
    width: 72,
    height: 72,
    borderRadius: 36,
    justifyContent: "center",
    alignItems: "center",
    borderWidth: 2,
    marginBottom: 8,
  },

  groupCircleImage: {
    width: "100%",
    height: "100%",
    borderRadius: 36,
  },

  circleBadge: {
    position: "absolute",
    top: -4,
    right: -4,
    width: 24,
    height: 24,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
  },

  circleBadgeText: {
    fontSize: 14,
    fontWeight: "700",
  },

  groupName: {
    marginTop: 0,
    fontSize: 11,
    maxWidth: 72,
    textAlign: "center",
    fontWeight: "500",
  },

  emptyText: {
    fontSize: 13,
    marginLeft: 20,
  },

  todoCard: {
    flexDirection: "row",
    alignItems: "center",
    marginHorizontal: 16,
    marginBottom: 10,
    padding: 14,
    borderRadius: 16,
  },

  todoDate: {
    width: 44,
    height: 44,
    borderRadius: 10,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 12,
  },

  todoDateText: {
    fontWeight: "700",
    fontSize: 13,
  },

  todoTitle: {
    fontWeight: "600",
    fontSize: 14,
    marginBottom: 2,
  },

  todoMeta: {
    fontSize: 11,
  },

  emptyState: {
    marginHorizontal: 16,
    marginTop: 8,
    padding: 28,
    borderRadius: 16,
    borderWidth: 1,
    borderStyle: "dashed",
    justifyContent: "center",
    alignItems: "center",
  },

  emptyStateText: {
    fontSize: 14,
    fontWeight: "500",
  },

  mindSpaceCard: {
    flexDirection: "row",
    alignItems: "center",
    marginHorizontal: 16,
    marginTop: 12,
    padding: 16,
    borderRadius: 16,
    gap: 12,
  },

  mindSpaceIcon: {
    width: 48,
    height: 48,
    borderRadius: 24,
    justifyContent: "center",
    alignItems: "center",
  },

  mindSpaceContent: {
    flex: 1,
  },

  mindSpaceTitle: {
    fontSize: 16,
    fontWeight: "600",
    marginBottom: 2,
  },

  mindSpaceSubtitle: {
    fontSize: 12,
    lineHeight: 16,
  },

  greetingContainer: {
    paddingHorizontal: 20,
    marginTop: 10
  },

  greeting: {
    fontSize: 34,
    fontWeight: "500"
  },

  name: {},

  mindSpacePlusButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    justifyContent: "center",
    alignItems: "center",
    marginLeft: 8,
  },
});

export default Home;
