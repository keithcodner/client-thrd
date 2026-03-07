import React, { createContext, useContext, useState, ReactNode } from 'react';

interface ProfileOverlayContextType {
  isVisible: boolean;
  openProfileOverlay: () => void;
  closeProfileOverlay: () => void;
}

const ProfileOverlayContext = createContext<ProfileOverlayContextType | undefined>(undefined);

export const ProfileOverlayProvider = ({ children }: { children: ReactNode }) => {
  const [isVisible, setIsVisible] = useState(false);

  const openProfileOverlay = () => setIsVisible(true);
  const closeProfileOverlay = () => setIsVisible(false);

  return (
    <ProfileOverlayContext.Provider value={{ isVisible, openProfileOverlay, closeProfileOverlay }}>
      {children}
    </ProfileOverlayContext.Provider>
  );
};

export const useProfileOverlay = () => {
  const context = useContext(ProfileOverlayContext);
  if (!context) {
    throw new Error('useProfileOverlay must be used within ProfileOverlayProvider');
  }
  return context;
};
