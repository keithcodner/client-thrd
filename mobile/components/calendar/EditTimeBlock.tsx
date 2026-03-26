import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  Modal,
  Pressable,
  StyleSheet,
  KeyboardAvoidingView,
  Platform,
  ActivityIndicator,
} from 'react-native';
import { X, Clock, Trash2 } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import TimePickerModal, {
  TimeValue,
  formatTimeValue,
  timeValueToDate,
  dateToTimeValue,
} from './day/TimePickerModal';
import { DayEvent } from './day/DayTimeBlock';

interface EditTimeBlockProps {
  visible: boolean;
  event: DayEvent | null;
  onClose: () => void;
  onSave: (id: string, payload: { name: string; start_at: string; end_at: string; color: string }) => Promise<void>;
  onDelete: (id: string) => Promise<void>;
}

const EditTimeBlock = ({ visible, event, onClose, onSave, onDelete }: EditTimeBlockProps) => {
  const colours = useThemeColours();

  const [label, setLabel]         = useState('');
  const [startTime, setStartTime] = useState<TimeValue>({ hour: 9,  minute: 0, period: 'AM' });
  const [endTime, setEndTime]     = useState<TimeValue>({ hour: 10, minute: 0, period: 'AM' });
  const [pickerFor, setPickerFor] = useState<'start' | 'end' | null>(null);
  const [saving, setSaving]       = useState(false);
  const [deleting, setDeleting]   = useState(false);
  const [error, setError]         = useState<string | null>(null);

  // Populate form when event changes
  useEffect(() => {
    if (event) {
      setLabel(event.name);
      setStartTime(dateToTimeValue(new Date(event.start_at)));
      setEndTime(dateToTimeValue(new Date(event.end_at)));
      setError(null);
    }
  }, [event]);

  if (!event) return null;

  const handleSave = async () => {
    if (!label.trim()) { setError('Please enter a label.'); return; }
    setError(null);
    setSaving(true);
    try {
      const base  = new Date(event.start_at);
      const start = timeValueToDate(base, startTime);
      const end   = timeValueToDate(base, endTime);
      if (end <= start) { setError('End time must be after start time.'); setSaving(false); return; }
      await onSave(event.id, {
        name:     label.trim(),
        start_at: start.toISOString(),
        end_at:   end.toISOString(),
        color:    event.color,
      });
      onClose();
    } catch {
      setError('Failed to save. Please try again.');
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async () => {
    setDeleting(true);
    try {
      await onDelete(event.id);
      onClose();
    } catch {
      setError('Failed to delete.');
    } finally {
      setDeleting(false);
    }
  };

  return (
    <>
      <Modal
        transparent
        animationType="slide"
        visible={visible && pickerFor === null}
        onRequestClose={onClose}
      >
        <KeyboardAvoidingView
          behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
          style={styles.backdrop}
        >
          <Pressable style={StyleSheet.absoluteFillObject} onPress={onClose} />
          <View style={[styles.sheet, { backgroundColor: colours.card, borderColor: colours.border }]}>
            {/* Header */}
            <View style={styles.sheetHeader}>
              <Text style={[styles.sheetTitle, { color: colours.text }]}>Edit Time Block</Text>
              <View style={styles.headerActions}>
                <Pressable onPress={handleDelete} disabled={deleting} hitSlop={8} style={styles.deleteBtn}>
                  {deleting
                    ? <ActivityIndicator size="small" color={colours.error} />
                    : <Trash2 size={18} color={colours.error} />}
                </Pressable>
                <Pressable onPress={onClose} hitSlop={8}>
                  <X size={20} color={colours.secondaryText} />
                </Pressable>
              </View>
            </View>

            {/* Label */}
            <Text style={[styles.fieldLabel, { color: colours.secondaryText }]}>LABEL (REST, STUDIO, WORK)</Text>
            <TextInput
              value={label}
              onChangeText={setLabel}
              placeholder="Focus Time"
              placeholderTextColor={colours.secondaryText + '88'}
              style={[styles.input, { color: colours.text, borderColor: colours.border, backgroundColor: colours.surface }]}
              returnKeyType="done"
            />

            {/* Time row */}
            <View style={styles.timeRow}>
              <View style={styles.timeField}>
                <Text style={[styles.fieldLabel, { color: colours.secondaryText }]}>STARTS</Text>
                <Pressable
                  onPress={() => setPickerFor('start')}
                  style={[styles.timePicker, { borderColor: colours.border, backgroundColor: colours.surface }]}
                >
                  <Text style={[styles.timeText, { color: colours.text }]}>{formatTimeValue(startTime)}</Text>
                  <Clock size={16} color={colours.secondaryText} />
                </Pressable>
              </View>
              <View style={styles.timeField}>
                <Text style={[styles.fieldLabel, { color: colours.secondaryText }]}>ENDS</Text>
                <Pressable
                  onPress={() => setPickerFor('end')}
                  style={[styles.timePicker, { borderColor: colours.border, backgroundColor: colours.surface }]}
                >
                  <Text style={[styles.timeText, { color: colours.text }]}>{formatTimeValue(endTime)}</Text>
                  <Clock size={16} color={colours.secondaryText} />
                </Pressable>
              </View>
            </View>

            {error ? (
              <Text style={[styles.errorText, { color: colours.error }]}>{error}</Text>
            ) : null}

            {/* Save */}
            <Pressable
              onPress={handleSave}
              disabled={saving}
              style={[styles.saveBtn, { backgroundColor: colours.text }]}
            >
              {saving
                ? <ActivityIndicator color={colours.background} />
                : <Text style={[styles.saveBtnText, { color: colours.background }]}>Save Block</Text>}
            </Pressable>
          </View>
        </KeyboardAvoidingView>
      </Modal>

      <TimePickerModal
        visible={pickerFor !== null}
        value={pickerFor === 'start' ? startTime : endTime}
        title={pickerFor === 'start' ? 'SELECT START TIME' : 'SELECT END TIME'}
        onConfirm={(tv) => {
          if (pickerFor === 'start') setStartTime(tv);
          else setEndTime(tv);
          setPickerFor(null);
        }}
        onClose={() => setPickerFor(null)}
        colours={colours}
      />
    </>
  );
};

const styles = StyleSheet.create({
  backdrop: {
    flex: 1,
    justifyContent: 'flex-end',
    backgroundColor: '#00000066',
  },
  sheet: {
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    borderWidth: 1,
    padding: 24,
    paddingBottom: 36,
  },
  sheetHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  sheetTitle: { fontSize: 18, fontWeight: '700' },
  headerActions: { flexDirection: 'row', alignItems: 'center', gap: 16 },
  deleteBtn: { padding: 2 },
  fieldLabel: {
    fontSize: 10,
    fontWeight: '700',
    letterSpacing: 0.8,
    marginBottom: 6,
  },
  input: {
    borderWidth: 1,
    borderRadius: 12,
    padding: 14,
    fontSize: 15,
    marginBottom: 20,
  },
  timeRow: { flexDirection: 'row', gap: 12, marginBottom: 20 },
  timeField: { flex: 1 },
  timePicker: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderWidth: 1,
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 12,
  },
  timeText: { fontSize: 15, fontWeight: '500' },
  errorText: { fontSize: 13, marginBottom: 12 },
  saveBtn: { borderRadius: 14, paddingVertical: 16, alignItems: 'center' },
  saveBtnText: { fontSize: 15, fontWeight: '700' },
});

export default EditTimeBlock;
