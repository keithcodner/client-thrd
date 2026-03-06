import { Phase, WaveTargets } from "@/types/welcome/welcome";

export const phases: Phase[] = [
  {
    title: "Planning made lighter.",
    subtitle: "Find time, without the friction.",
  },
  {
    title: "Smart coordination.",
    subtitle: "AI finds the gaps so you can just show up.",
  },
  {
    title: "Your community, synced.",
    subtitle: "Friends. Circles. Third Spaces.",
  },
];

export const waveTargets: WaveTargets[] = [
  { wave1Y1: 50, wave1Y2: 100, wave2Y1: 170, wave2Y2: 120 },  // Phase 0
  { wave1Y1: 150, wave1Y2: 100, wave2Y1: 70, wave2Y2: 120 },  // Phase 1
  { wave1Y1: 100, wave1Y2: 50, wave2Y1: 120, wave2Y2: 170 },  // Phase 2
];
