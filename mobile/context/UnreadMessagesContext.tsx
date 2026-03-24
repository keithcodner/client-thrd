import React, { createContext, useContext, ReactNode } from 'react';
import { useUnreadMessages } from '@/hooks/useUnreadMessages';

interface UnreadMessagesContextType {
  totalUnread: number;
  unreadByConversation: Record<string, number>;
  isLoading: boolean;
  refresh: () => Promise<void>;
}

const UnreadMessagesContext = createContext<UnreadMessagesContextType | undefined>(undefined);

export const UnreadMessagesProvider = ({ children }: { children: ReactNode }) => {
  const unreadData = useUnreadMessages(30000); // Poll every 30 seconds

  return (
    <UnreadMessagesContext.Provider value={unreadData}>
      {children}
    </UnreadMessagesContext.Provider>
  );
};

export const useUnreadMessagesContext = () => {
  const context = useContext(UnreadMessagesContext);
  if (!context) {
    throw new Error('useUnreadMessagesContext must be used within UnreadMessagesProvider');
  }
  return context;
};
