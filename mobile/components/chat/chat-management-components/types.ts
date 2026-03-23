import { LucideIcon } from 'lucide-react-native';

export interface CircleMember {
  id: number;
  name: string;
  email: string;
  type: string;
  joined_at: string;
}

export interface DropdownMenuItem {
  id: string;
  label: string;
  icon: LucideIcon;
  color?: string;
  onPress: () => void;
  showOnlyForOwner?: boolean;
}

export interface QuickActionButtonData {
  id: string;
  icon: LucideIcon;
  color: string;
  onPress: () => void;
  specialStyle?: boolean;
}
