import React from "react";
import { View, Text, Pressable, StyleSheet } from "react-native";
import { ChevronRight } from "lucide-react-native";
import { format, isValid } from "date-fns";

interface UpcomingPlansProps {
  colors: any;
  todos: any[];
  groups: any[];
  onNavigate?: (screen: string) => void;
}

export const UpcomingPlans = ({
  colors,
  todos,
  groups,
  onNavigate = () => {},
}: UpcomingPlansProps) => {
  const upcomingTodos = todos
    .filter((t) => t.status === "confirmed")
    .slice(0, 4);

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
    <View style={styles.section}>
      <View style={styles.sectionHeader}>
        <Text style={[styles.sectionTitle, { color: colors.text }]}>
          UPCOMING PLANS
        </Text>
        <Pressable onPress={() => onNavigate("calendar")}>
          <Text style={[styles.seeAllLink, { color: colors.primary }]}>
            Full Calendar
          </Text>
        </Pressable>
      </View>

      {upcomingTodos.length > 0 ? (
        upcomingTodos.map((todo) => {
          const display = getTodoDisplayInfo(todo);

          return (
            <Pressable
              key={todo.id}
              style={[styles.todoCard, { backgroundColor: colors.card }]}
            >
              <View style={[styles.todoDate, { backgroundColor: colors.surface }]}>
                <Text style={[styles.todoDateText, { color: colors.text }]}>
                  {display.date.split(" ")[1]}
                </Text>
              </View>

              <View style={{ flex: 1 }}>
                <Text style={[styles.todoTitle, { color: colors.text }]}>
                  {todo.title}
                </Text>
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
  );
};

const styles = StyleSheet.create({
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
    letterSpacing: 1.2,
  },

  seeAllLink: {
    fontSize: 12,
    fontWeight: "600",
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
});
