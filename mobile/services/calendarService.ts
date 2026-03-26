import { parse, isValid } from 'date-fns';
import axiosInstance from '@/config/axiosConfig';

// ─── Types ────────────────────────────────────────────────────────────────────

export interface CalendarEventPayload {
  title: string;
  description?: string;
  location?: string;
  start_at: string; // ISO 8601
  end_at: string;   // ISO 8601
  color?: string;   // hex e.g. "#ADC178"
  all_day?: boolean;
}

export interface CalendarEventResponse extends CalendarEventPayload {
  id: string;
  user_id: number;
  created_at: string;
  updated_at: string;
}

// ─── API service ──────────────────────────────────────────────────────────────

/**
 * Fetch all events for a given month.
 * GET /calendar/events?year=YYYY&month=MM
 */
export const fetchMonthEvents = async (
  year: number,
  month: number // 1-indexed
): Promise<CalendarEventResponse[]> => {
  const response = await axiosInstance.get('/calendar/events', {
    params: { year, month },
  });
  return response.data.data ?? response.data;
};

/**
 * Fetch a single event by ID.
 * GET /calendar/events/:id
 */
export const fetchEvent = async (id: string): Promise<CalendarEventResponse> => {
  const response = await axiosInstance.get(`/calendar/events/${id}`);
  return response.data.data ?? response.data;
};

/**
 * Create a new calendar event.
 * POST /calendar/events
 */
export const createCalendarEvent = async (
  payload: CalendarEventPayload
): Promise<CalendarEventResponse> => {
  const response = await axiosInstance.post('/calendar/events', payload);
  return response.data.data ?? response.data;
};

/**
 * Update an existing calendar event.
 * PUT /calendar/events/:id
 */
export const updateCalendarEvent = async (
  id: string,
  payload: Partial<CalendarEventPayload>
): Promise<CalendarEventResponse> => {
  const response = await axiosInstance.put(`/calendar/events/${id}`, payload);
  return response.data.data ?? response.data;
};

/**
 * Delete a calendar event.
 * DELETE /calendar/events/:id
 */
export const deleteCalendarEvent = async (id: string): Promise<void> => {
  await axiosInstance.delete(`/calendar/events/${id}`);
};

/**
 * Helper to parse loose date/time strings from AI into Date objects
 */
export const parseDateTime = (dayStr: string, timeStr: string) => {
  const now = new Date();
  let eventDate = new Date();
  const currentYear = now.getFullYear();
  
  // Safely handle missing dayStr or timeStr
  const safeDayStr = dayStr || '';
  const safeTimeStr = timeStr || '';
  
  // Try to parse day string (e.g. "Sat, Oct 12" or "Oct 12")
  const datePart = safeDayStr.includes(',') ? safeDayStr.split(',')[1]?.trim() : safeDayStr;
  const parsedDate = new Date(`${datePart} ${currentYear}`);
  
  if (!isNaN(parsedDate.getTime()) && safeDayStr) {
    eventDate = parsedDate;
  }
  
  const times = safeTimeStr.split('-').map((t: string) => t.trim());
  const startTimeStr = times[0];
  const endTimeStr = times[1];
  
  const parseTime = (dateBase: Date, tStr: string) => {
    const d = new Date(dateBase);
    const match = tStr?.match(/(\d+):?(\d+)?\s*(AM|PM)?/i);
    
    if (match) {
      let hours = parseInt(match[1]);
      const minutes = parseInt(match[2] || '0');
      const period = match[3]?.toUpperCase();
      
      if (period === 'PM' && hours < 12) hours += 12;
      if (period === 'AM' && hours === 12) hours = 0;
      
      d.setHours(hours, minutes, 0, 0);
    } else {
      // Default to noon if unparseable
      d.setHours(12, 0, 0, 0);
    }
    
    return d;
  };
  
  const startDate = parseTime(eventDate, startTimeStr);
  let endDate = endTimeStr 
    ? parseTime(eventDate, endTimeStr) 
    : new Date(startDate.getTime() + 2 * 60 * 60 * 1000); // Default 2 hours
  
  if (endDate < startDate) {
    endDate = new Date(endDate.getTime() + 24 * 60 * 60 * 1000);
  }
  
  return { start: startDate, end: endDate };
};

/**
 * Parse a smart date string that could be in various formats
 * Supports ISO strings, date-fns format strings, etc.
 */
export const parseSmartDate = (dateInput: string | number | Date): Date => {
  // If it's already a Date object, return it
  if (dateInput instanceof Date) {
    return dateInput;
  }

  // Try parsing as ISO string first
  const isoDate = new Date(dateInput);
  if (isValid(isoDate)) {
    return isoDate;
  }

  // If it's a string with bullet separator (from AI), use parseDateTime
  if (typeof dateInput === 'string' && dateInput.includes('•')) {
    const parts = dateInput.split('•');
    const dayPart = parts[0].trim();
    const { start } = parseDateTime(dayPart, '');
    return start;
  }

  // Try common date formats
  const commonFormats = [
    'yyyy-MM-dd',
    'MM/dd/yyyy',
    'dd-MM-yyyy',
    'yyyy-MM-dd HH:mm:ss',
    'MM/dd/yyyy HH:mm:ss',
  ];

  for (const format of commonFormats) {
    try {
      const parsed = parse(String(dateInput), format, new Date());
      if (isValid(parsed)) {
        return parsed;
      }
    } catch (e) {
      // Continue to next format
    }
  }

  // If all else fails, return current date
  console.warn(`Could not parse date: ${dateInput}, returning today`);
  return new Date();
};

/**
 * Get the display format for a date
 */
export const formatDateDisplay = (date: Date): string => {
  if (!isValid(date)) return 'N/A';
  
  const today = new Date();
  const yesterday = new Date(today);
  yesterday.setDate(yesterday.getDate() - 1);

  if (date.toDateString() === today.toDateString()) {
    return 'Today';
  }
  if (date.toDateString() === yesterday.toDateString()) {
    return 'Yesterday';
  }

  return date.toLocaleDateString();
};

/**
 * Check if a date is in the past
 */
export const isPastDate = (date: Date): boolean => {
  return date < new Date();
};

/**
 * Check if a date is today
 */
export const isToday = (date: Date): boolean => {
  const today = new Date();
  return date.toDateString() === today.toDateString();
};

/**
 * Mock async operation for adding to calendar UI feedback
 */
export const addToCalendar = async (proposal: any): Promise<boolean> => {
  await new Promise(resolve => setTimeout(resolve, 500));
  return true;
};

/**
 * Generate Google Calendar URL for a proposal
 * Note: This is for web - React Native would use linking.openURL instead
 */
export const generateGoogleCalendarUrl = (proposal: any): string => {
  const { start, end } = parseDateTime(proposal.date, proposal.time);
  
  const formatGCalDate = (d: Date) => d.toISOString().replace(/-|:|\.\d+/g, '');
  const title = encodeURIComponent(proposal.title);
  const details = encodeURIComponent(proposal.description || 'Plan coordinated via THRD');
  const location = encodeURIComponent(proposal.location || '');
  const dates = `${formatGCalDate(start)}/${formatGCalDate(end)}`;
  
  return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&details=${details}&location=${location}&dates=${dates}`;
};

/**
 * Generate ICS file content for calendar import
 */
export const generateIcsContent = (proposal: any): string => {
  const { start, end } = parseDateTime(proposal.date, proposal.time);
  
  const formatICSDate = (d: Date) => d.toISOString().replace(/-|:|\.\d+/g, '');
  
  return [
    'BEGIN:VCALENDAR',
    'VERSION:2.0',
    'BEGIN:VEVENT',
    `SUMMARY:${proposal.title}`,
    `DESCRIPTION:${proposal.description || 'Plan coordinated via THRD'}`,
    `LOCATION:${proposal.location || ''}`,
    `DTSTART:${formatICSDate(start)}`,
    `DTEND:${formatICSDate(end)}`,
    'END:VEVENT',
    'END:VCALENDAR'
  ].join('\n');
};

/**
 * Download ICS file (web only - for React Native use Share API)
 * Note: This uses browser APIs and won't work in React Native
 */
export const downloadIcsFile = (proposal: any): void => {
  try {
    const icsContent = generateIcsContent(proposal);
    const blob = new Blob([icsContent], { type: 'text/calendar;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `${proposal.title.replace(/\s+/g, '_')}.ics`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (e) {
    console.warn('Download ICS file is only supported on web', e);
  }
};

/**
 * Sync plan to external calendars (web implementation)
 * Note: For React Native, you'd want to use linking.openURL instead
 */
export const syncPlanToExternal = (proposal: any): void => {
  try {
    // 1. Google Link
    const gUrl = generateGoogleCalendarUrl(proposal);
    if (typeof window !== 'undefined') {
      window.open(gUrl, '_blank');
    }
    
    // 2. ICS Download for Apple/Outlook
    setTimeout(() => {
      downloadIcsFile(proposal);
    }, 1000);
  } catch (e) {
    console.warn('Sync to external calendars is limited in React Native', e);
  }
};
