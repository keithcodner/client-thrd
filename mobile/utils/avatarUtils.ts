/**
 * Avatar Utilities
 * 
 * Shared utilities for avatar display across the app
 * Ensures consistent avatar colors between chat list and chat detail pages
 */

/**
 * Get initials from a name
 * @param name - Full name
 * @returns Uppercase initials (max 2 characters)
 */
export const getInitials = (name: string): string => {
  return name
    .split(' ')
    .map(word => word[0])
    .join('')
    .toUpperCase()
    .slice(0, 2);
};

/**
 * Get avatar background color based on name
 * Uses consistent color palette for all avatars
 * Same name will always get the same color
 * 
 * @param name - Name to generate color for
 * @returns Hex color code
 */
export const getAvatarColor = (name: string): string => {
  const colors = [
    '#8B7355', // sage brown
    '#6B7280', // stone gray
    '#92400E', // clay brown
    '#D97706', // amber
    '#7C2D12', // dusk brown
    '#B45309', // sand orange
    '#059669', // emerald
    '#0891B2', // cyan
    '#4F46E5', // indigo
    '#7C3AED', // violet
  ];
  
  const firstLetter = name.charAt(0).toUpperCase();
  const index = firstLetter.charCodeAt(0) % colors.length;
  return colors[index];
};
