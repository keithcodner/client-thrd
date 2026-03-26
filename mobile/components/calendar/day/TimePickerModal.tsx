import React, { useState, useRef } from 'react';
import {
  View,
  Text,
  Modal,
  Pressable,
  ScrollView,
  StyleSheet,
  Dimensions,
} from 'react-native';

export interface TimeValue {
  hour: number;   // 1–12
  minute: number; // 0 | 15 | 30 | 45
  period: 'AM' | 'PM';
}

export const timeValueToDate = (base: Date, tv: TimeValue): Date => {
  const d = new Date(base);
  let h = tv.hour % 12;
  if (tv.period === 'PM') h += 12;
  d.setHours(h, tv.minute, 0, 0);
  return d;
};

export const dateToTimeValue = (date: Date): TimeValue => {
  let h = date.getHours();
  const period: 'AM' | 'PM' = h >= 12 ? 'PM' : 'AM';
  if (h > 12) h -= 12;
  if (h === 0) h = 12;
  const rawMin = date.getMinutes();
  const minute = ([0, 15, 30, 45].reduce((prev, curr) =>
    Math.abs(curr - rawMin) < Math.abs(prev - rawMin) ? curr : prev
  )) as 0 | 15 | 30 | 45;
  return { hour: h, minute, period };
};

export const formatTimeValue = (tv: TimeValue): string => {
  const m = tv.minute.toString().padStart(2, '0');
  return `${tv.hour}:${m} ${tv.period}`;
};

const HOURS   = [1,2,3,4,5,6,7,8,9,10,11,12];
const MINUTES = [0, 15, 30, 45];
const PERIODS: ('AM' | 'PM')[] = ['AM', 'PM'];
const ITEM_H  = 44;

interface TimePickerModalProps {
  visible: boolean;
  value: TimeValue;
  title: string;
  onConfirm: (tv: TimeValue) => void;
  onClose: () => void;
  colours: any;
}

function PickerColumn<T extends string | number>({
  items,
  selected,
  onSelect,
  formatItem,
  colours,
}: {
  items: T[];
  selected: T;
  onSelect: (item: T) => void;
  formatItem?: (item: T) => string;
  colours: any;
}) {
  return (
    <View style={colStyles.column}>
      {items.map((item) => {
        const isActive = item === selected;
        return (
          <Pressable
            key={String(item)}
            onPress={() => onSelect(item)}
            style={[colStyles.item, isActive && { backgroundColor: colours.primary + '22' }]}
          >
            <Text
              style={[
                colStyles.itemText,
                { color: isActive ? colours.primary : colours.secondaryText },
                isActive && { fontWeight: '700' },
              ]}
            >
              {formatItem ? formatItem(item) : String(item)}
            </Text>
          </Pressable>
        );
      })}
    </View>
  );
}

const colStyles = StyleSheet.create({
  column: { flex: 1, alignItems: 'center' },
  item: {
    width: '100%',
    height: ITEM_H,
    alignItems: 'center',
    justifyContent: 'center',
    borderRadius: 8,
  },
  itemText: { fontSize: 18 },
});

const TimePickerModal = ({
  visible,
  value,
  title,
  onConfirm,
  onClose,
  colours,
}: TimePickerModalProps) => {
  const [draft, setDraft] = useState<TimeValue>(value);

  // Keep draft in sync when value changes externally
  React.useEffect(() => { setDraft(value); }, [value]);

  return (
    <Modal transparent animationType="fade" visible={visible} onRequestClose={onClose}>
      <Pressable style={styles.backdrop} onPress={onClose}>
        <Pressable style={[styles.panel, { backgroundColor: colours.card, borderColor: colours.border }]}>
          <Text style={[styles.title, { color: colours.secondaryText }]}>{title}</Text>

          <View style={styles.row}>
            <PickerColumn
              items={HOURS}
              selected={draft.hour}
              onSelect={(h) => setDraft((d) => ({ ...d, hour: h }))}
              colours={colours}
            />
            <Text style={[styles.colon, { color: colours.secondaryText }]}>:</Text>
            <PickerColumn
              items={MINUTES}
              selected={draft.minute}
              onSelect={(m) => setDraft((d) => ({ ...d, minute: m }))}
              formatItem={(m) => m.toString().padStart(2, '0')}
              colours={colours}
            />
            <PickerColumn
              items={PERIODS}
              selected={draft.period}
              onSelect={(p) => setDraft((d) => ({ ...d, period: p }))}
              colours={colours}
            />
          </View>

          <Pressable
            onPress={() => onConfirm(draft)}
            style={[styles.confirmBtn, { backgroundColor: colours.text }]}
          >
            <Text style={[styles.confirmText, { color: colours.background }]}>Confirm</Text>
          </Pressable>
        </Pressable>
      </Pressable>
    </Modal>
  );
};

const styles = StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: '#00000077',
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
  },
  panel: {
    width: '100%',
    borderRadius: 20,
    borderWidth: 1,
    padding: 20,
  },
  title: {
    fontSize: 11,
    fontWeight: '700',
    letterSpacing: 0.8,
    marginBottom: 12,
    textAlign: 'center',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 20,
  },
  colon: {
    fontSize: 22,
    fontWeight: '700',
    paddingHorizontal: 4,
    marginBottom: 4,
  },
  confirmBtn: {
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
  },
  confirmText: { fontSize: 15, fontWeight: '700' },
});

export default TimePickerModal;
