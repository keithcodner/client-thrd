import React, { createContext, useContext, useState, ReactNode } from 'react';

interface FABContextType {
  isExpanded: boolean;
  toggleFAB: () => void;
  closeFAB: () => void;
  openFAB: () => void;
}

const FABContext = createContext<FABContextType | undefined>(undefined);

export const FABProvider = ({ children }: { children: ReactNode }) => {
  const [isExpanded, setIsExpanded] = useState(false);

  const toggleFAB = () => setIsExpanded(!isExpanded);
  const closeFAB = () => setIsExpanded(false);
  const openFAB = () => setIsExpanded(true);

  return (
    <FABContext.Provider value={{ isExpanded, toggleFAB, closeFAB, openFAB }}>
      {children}
    </FABContext.Provider>
  );
};

export const useFAB = () => {
  const context = useContext(FABContext);
  if (!context) {
    throw new Error('useFAB must be used within FABProvider');
  }
  return context;
};
