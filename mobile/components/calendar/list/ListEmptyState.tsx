import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { List } from 'lucide-react-native';

interface Props {
  colours: any;
}

const ListEmptyState = ({ colours }: Props) => {
  return (
    <View style={styles.container}>
      <List size={36} color={colours.secondaryText} strokeWidth={1.5} />
      <Text style={[styles.text, { color: colours.primary }]}>Nothing scheduled yet</Text>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingBottom: 80,
  },
  text: {
    marginTop: 14,
    fontSize: 15,
    fontStyle: 'italic',
  },
});

export default ListEmptyState;
