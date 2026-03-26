import React, { useState } from 'react';
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
import { BlurView } from 'expo-blur';
import { X, Clock } from 'lucide-react-native';
import { useThemeColours } from '@/hooks/useThemeColours';
import { useTheme } from '@/context/ThemeContext';
import TimePickerModal, {
  TimeValue,
  formatTimeValue,
  timeValueToDate,
} from './day/TimePickerModal';

const DEFAULT_START: TimeValue = { hour: 9,  minute: 0, period: 'AM' };
const DEFAULT_END:   TimeValue = { hour: 10, minute: 0, period: 'AM' };

interface CreateTimeBlockProps {
  visible: boolean;
  selectedDate: Date;   // the day the user is adding the event to
  onClose: () => void;
  onSave: (payload: { name: string; start_at: string; end_at: string; color: string }) => Promise<void>;
}

const CreateTimeBlock = ({ visible, selectedDate, onClose, onSave }: CreateTimeBlockProps) => {
  const colours = useThemeColours();
  const { currentTheme } = useTheme();

  const [label, setLabel]         = useState('');
  const [startTime, setStartTime] = useState<TimeValue>(DEFAULT_START);
  const [endTime, setEndTime]     = useState<TimeValue>(DEFAULT_END);
  const [pickerFor, setPickerFor] = useState<'start' | 'end' | null>(null);
  const [saving, setSaving]       = useState(false);
  const [error, setError]         = useState<string | null>(null);
  const reset = () => {
    setLabel('');
    setStartTime(DEFAULT_START);
    setEndTime(DEFAULT_END);
    setError(null);
  };

  const handleClose = () => {
    reset();
    onClose();
  };

  const handleSave = async () => {
    if (!label.trim()) { setError('Please enter a label.'); return; }
    setError(null);
    setSaving(true);
    try {
      const start = timeValueToDate(selectedDate, startTime);
      const end   = timeValueToDate(selectedDate, endTime);
      if (end <= start) { setError('End time must be after start time.'); setSaving(false); return; }
      await onSave({
        name:     label.trim(),
        start_at: start.toISOString(),
        end_at:   end.toISOString(),
        color:    '#ADC178',
      });
      reset();
      onClose();
    } catch (e) {
      setError('Failed to save. Please try again.');
    } finally {
      setSaving(false);
    }
  };

  return (
    <>
      <Modal
        transparent
        animationType="fade"
        visible={visible && pickerFor === null}
        onRequestClose={handleClose}
      >
        <BlurView
          intensity={50}
          tint={currentTheme === 'dark' ? 'dark' : 'light'}
          style={styles.backdrop}
        >
          <Pressable style={StyleSheet.absoluteFillObject} onPress={handleClose} />
          <KeyboardAvoidingView
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
            style={styles.kav}
          >
            <View style={[styles.sheet, { backgroundColor: colours.card, borderColor: colours.border }]}>
              {/* Header */}
              <View style={styles.sheetHeader}>
                <Text style={[styles.sheetTitle, { color: colours.text }]}>Add Time Block</Text>
                <Pressable onPress={handleClose} hitSlop={8}>
                  <X size={20} color={colours.secondaryText} />
                </Pressable>
              </View>

              {/* Label */}
              <Text style={[styles.fieldLabel, { color: colours.secondaryText }]}>LABEL (REST, STUDIO, WORK)</Text>
              <TextInput
                value={label}
                onChangeText={setLabel}
                placeholder="Focus Time"
                placeholderTextColor={colours.secondaryText + '88'}
                style={[styles.input, { color: colours.text, borderColor: colours.stone500, backgroundColor: colours.surface }]}
                returnKeyType="done"
              />

              {/* Time row */}
              <View style={styles.timeRow}>
                <View style={styles.timeField}>
                  <Text style={[styles.fieldLabel, { color: colours.secondaryText }]}>STARTS</Text>
                  <Pressable
                    onPress={() => setPickerFor('start')}
                    style={[styles.timePicker, { borderColor: colours.stone500, backgroundColor: colours.surface }]}
                  >
                    <Text style={[styles.timeText, { color: colours.text }]}>{formatTimeValue(startTime)}</Text>
                    <Clock size={16} color={colours.secondaryText} />
                  </Pressable>
                </View>
                <View style={styles.timeField}>
                  <Text style={[styles.fieldLabel, { color: colours.secondaryText }]}>ENDS</Text>
                  <Pressable
                    onPress={() => setPickerFor('end')}
                    style={[styles.timePicker, { borderColor: colours.stone500, backgroundColor: colours.surface }]}
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
                style={[styles.saveBtn, { backgroundColor: colours.surface, borderColor: colours.stone500 }]}
              >
                {saving
                  ? <ActivityIndicator color={colours.text} />
                  : <Text style={[styles.saveBtnText, { color: colours.text }]}>Save Block</Text>}
              </Pressable>
            </View>
          </KeyboardAvoidingView>
        </BlurView>
      </Modal>

      {/* Time picker sub-modal */}
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
    justifyContent: 'center',
    padding: 24,
    backgroundColor: 'rgba(0,0,0,0.45)',
  },
  kav: {
    width: '100%',
  },
  sheet: {
    borderRadius: 24,
    borderWidth: 1,
    padding: 24,
    paddingBottom: 28,
  },
  sheetHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 20,
  },
  sheetTitle: {
    fontSize: 18,
    fontWeight: '700',
  },
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
  timeRow: {
    flexDirection: 'row',
    gap: 12,
    marginBottom: 20,
  },
  timeField: {
    flex: 1,
  },
  timePicker: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderWidth: 1,
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 12,
  },
  timeText: {
    fontSize: 15,
    fontWeight: '500',
  },
  errorText: {
    fontSize: 13,
    marginBottom: 12,
  },
  saveBtn: {
    borderRadius: 14,
    borderWidth: 1,
    paddingVertical: 16,
    alignItems: 'center',
  },
  saveBtnText: {
    fontSize: 15,
    fontWeight: '700',
  },
});

export default CreateTimeBlock;
