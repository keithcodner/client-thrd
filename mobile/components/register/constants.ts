import { RegisterPhase } from "@/types/register/register";

export const PHASE_ACCOUNT_TYPE = 0;
export const PHASE_PHONE = 1;
export const PHASE_SECURITY = 2;
export const PHASE_IDENTITY = 3;
export const PHASE_PHOTO = 4;
export const PHASE_PROFILE = 5;
export const PHASE_CALENDAR = 6;
export const PHASE_INVITE_PEOPLE = 7;
export const PHASE_INFO = 8;
export const PHASE_SUCCESS = 9;

export const phases: RegisterPhase[] = [
  {
    title: "How will you use THRD?",
    subtitle: "",
  },
  {
    title: "Let's set things up.",
    subtitle: "",
  },
  {
    title: "Security.",
    subtitle: "",
  },
  {
    title: "Your Identity.",
    subtitle: "",
  },
  {
    title: "Photo.",
    subtitle: "",
  },
  {
    title: "Profile Details.",
    subtitle: "",
  },
  {
    title: "Calendar Sync.",
    subtitle: "Let THRD analyze your busy slots to find the best times for your plans automatically.",
  },
  {
    title: "Invite your people.",
    subtitle: "Invite up to 3 friends to get started.",
  },
  {
    title: "You're always in control.",
    subtitle: "",
  },
  {
    title: "You're in. 💙",
    subtitle: "Your THRD is ready for connection.",
  }
];

export type AccountType = "personal" | "community" | "business";

export type RegisterFormData = {
  accountType: AccountType | null;
  phone: string;
  email: string;
  password: string;
  fullName: string;
  photo: string | null;
  businessName: string;
  streetAddress: string;
  hours: string;
  capacity: string;
  website: string;
  instagram: string;
  tiktok: string;
  primaryCity: string;
};


