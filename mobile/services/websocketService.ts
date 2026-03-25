import Pusher from 'pusher-js/react-native';
import { PUSHER_CONFIG } from '@/config/env';
import { Platform } from 'react-native';

class WebSocketService {
  private pusher: Pusher | null = null;
  private channels: Map<string, any> = new Map();
  private isConnecting: boolean = false;

  connect(authToken: string, userId: number) {
    if (this.pusher || this.isConnecting) {
      console.log('WebSocket already connected or connecting');
      return;
    }

    console.log('🔌 Initializing WebSocket connection...', {
      platform: Platform.OS,
      wsHost: PUSHER_CONFIG.wsHost,
      wsPort: PUSHER_CONFIG.wsPort,
      apiUrl: PUSHER_CONFIG.apiUrl,
      key: PUSHER_CONFIG.key,
      cluster: PUSHER_CONFIG.cluster,
    });

    this.isConnecting = true;

    try {
      this.pusher = new Pusher(PUSHER_CONFIG.key, {
        wsHost: PUSHER_CONFIG.wsHost,
        wsPort: PUSHER_CONFIG.wsPort,
        wssPort: PUSHER_CONFIG.wssPort,
        forceTLS: PUSHER_CONFIG.forceTLS,
        enabledTransports: ['ws', 'wss'],
        cluster: PUSHER_CONFIG.cluster,
        authEndpoint: `${PUSHER_CONFIG.apiUrl}/api/broadcasting/auth`,
        auth: {
          headers: {
            Authorization: `Bearer ${authToken}`,
            Accept: 'application/json',
          },
        },
      });

      console.log('📡 Pusher instance created, attempting connection...');
      console.log('🔐 Auth endpoint:', `${PUSHER_CONFIG.apiUrl}/api/broadcasting/auth`);

      this.pusher.connection.bind('connected', () => {
        console.log('✅ WebSocket connected successfully!', {
          platform: Platform.OS,
          socketId: this.pusher?.connection.socket_id,
        });
        this.isConnecting = false;
      });

      this.pusher.connection.bind('error', (error: any) => {
        console.error('❌ WebSocket error:', {
          platform: Platform.OS,
          error,
          wsHost: PUSHER_CONFIG.wsHost,
          wsPort: PUSHER_CONFIG.wsPort,
        });
        this.isConnecting = false;
      });

      this.pusher.connection.bind('disconnected', () => {
        console.log('⚠️ WebSocket disconnected, attempting to reconnect...', {
          platform: Platform.OS,
        });
        this.isConnecting = false;
        setTimeout(() => this.reconnect(), 3000);
      });

      this.pusher.connection.bind('failed', () => {
        console.error('💥 WebSocket connection failed', {
          platform: Platform.OS,
          wsHost: PUSHER_CONFIG.wsHost,
          wsPort: PUSHER_CONFIG.wsPort,
        });
        this.isConnecting = false;
      });
    } catch (error) {
      console.error('Failed to create Pusher instance:', {
        platform: Platform.OS,
        error,
        config: PUSHER_CONFIG,
      });
      this.isConnecting = false;
    }
  }

  reconnect() {
    if (this.pusher && !this.isConnecting) {
      console.log('🔄 Attempting to reconnect WebSocket...', {
        platform: Platform.OS,
      });
      this.pusher.connect();
    }
  }

  subscribeToConversation(
    conversationId: string,
    onNewMessage: (data: any) => void
  ) {
    if (!this.pusher) {
      console.error('WebSocket not connected. Call connect() first.', {
        platform: Platform.OS,
      });
      return null;
    }

    const channelName = `private-sitePrivateChat.${conversationId}`;
    
    // Check if already subscribed
    if (this.channels.has(channelName)) {
      console.log(`Already subscribed to ${channelName}`, {
        platform: Platform.OS,
      });
      return this.channels.get(channelName);
    }

    console.log(`📡 Subscribing to ${channelName}...`, {
      platform: Platform.OS,
      conversationId,
    });
    const channel = this.pusher.subscribe(channelName);

    channel.bind('pusher:subscription_succeeded', () => {
      console.log(`✅ Successfully subscribed to ${channelName}`, {
        platform: Platform.OS,
      });
    });

    channel.bind('pusher:subscription_error', (error: any) => {
      console.error(`❌ Error subscribing to ${channelName}:`, {
        platform: Platform.OS,
        error,
      });
    });

    channel.bind('newMessage', (data: any) => {
      console.log('📨 New message received:', {
        platform: Platform.OS,
        channelName,
        data,
      });
      onNewMessage(data);
    });

    this.channels.set(channelName, channel);
    return channel;
  }

  unsubscribeFromConversation(conversationId: string) {
    const channelName = `private-sitePrivateChat.${conversationId}`;
    const channel = this.channels.get(channelName);

    if (channel) {
      this.pusher?.unsubscribe(channelName);
      this.channels.delete(channelName);
      console.log(`🔕 Unsubscribed from ${channelName}`);
    }
  }

  subscribeToTyping(
    conversationId: string,
    onTypingChange: (data: { user_id: number; user_name: string; is_typing: boolean }) => void
  ) {
    if (!this.pusher) {
      console.error('WebSocket not connected');
      return null;
    }

    const channelName = `private-typing.${conversationId}`;
    
    if (this.channels.has(channelName)) {
      return this.channels.get(channelName);
    }

    const channel = this.pusher.subscribe(channelName);

    channel.bind('typingStatus', (data: any) => {
      onTypingChange(data);
    });

    this.channels.set(channelName, channel);
    return channel;
  }

  unsubscribeFromTyping(conversationId: string) {
    const channelName = `private-typing.${conversationId}`;
    const channel = this.channels.get(channelName);

    if (channel) {
      this.pusher?.unsubscribe(channelName);
      this.channels.delete(channelName);
    }
  }

  subscribeToPresence(
    conversationId: string,
    onUserJoined: (member: any) => void,
    onUserLeft: (member: any) => void,
    onMemberList: (members: any[]) => void,
    onError?: (error: any) => void
  ) {
    if (!this.pusher) {
      console.error('❌ WebSocket not connected');
      if (onError) onError({ message: 'WebSocket not connected' });
      return null;
    }

    const channelName = `presence-conversation.${conversationId}`;
    
    console.log('🔔 ========== SUBSCRIBING TO PRESENCE CHANNEL ==========');
    console.log('🔔 Channel name:', channelName);
    console.log('🔔 Conversation ID:', conversationId, 'Type:', typeof conversationId);
    console.log('🔌 WebSocket state:', this.pusher.connection.state);
    console.log('🔌 Socket ID:', this.pusher.connection.socket_id);
    
    if (this.channels.has(channelName)) {
      console.log('♻️ Channel already exists, reusing:', channelName);
      return this.channels.get(channelName);
    }

    const channel = this.pusher.subscribe(channelName);

    // Handle subscription errors
    channel.bind('pusher:subscription_error', (error: any) => {
      console.error('❌ ========== PRESENCE SUBSCRIPTION ERROR ==========');
      console.error('❌ Channel:', channelName);
      console.error('❌ Error object:', JSON.stringify(error, null, 2));
      console.error('❌ Error type:', error.type);
      console.error('❌ Error status:', error.status);
      console.error('❌ Error error:', error.error);
      console.error('❌ Socket ID:', this.pusher?.connection.socket_id);
      console.error('❌ ========== END PRESENCE ERROR ==========');
      if (onError) onError(error);
    });

    channel.bind('pusher:subscription_succeeded', (members: any) => {
      console.log('✅ ========== PRESENCE SUBSCRIPTION SUCCEEDED ==========');
      console.log('✅ Channel:', channelName);
      console.log('📊 Raw members object:', members);
      console.log('📊 Members.members:', members.members);
      console.log('📊 Members.count:', members.count);
      console.log('✅ ========== END PRESENCE SUCCESS ==========');
      
      // Extract member list - Pusher uses user IDs as keys
      const memberList = Object.keys(members.members).map(userId => ({
        id: parseInt(userId),
        ...members.members[userId]
      }));
      
      console.log('📊 Parsed member list:', memberList);
      onMemberList(memberList);
    });

    channel.bind('pusher:member_added', (member: any) => {
      console.log('➕ Member joined:', member);
      console.log('➕ Member ID:', member.id);
      console.log('➕ Member info:', member.info);
      // Pusher sends member.id and member.info
      onUserJoined({ id: parseInt(member.id), ...member.info });
    });

    channel.bind('pusher:member_removed', (member: any) => {
      console.log('➖ Member left:', member);
      console.log('➖ Member ID:', member.id);
      console.log('➖ Member info:', member.info);
      // Pusher sends member.id and member.info
      onUserLeft({ id: parseInt(member.id), ...member.info });
    });

    this.channels.set(channelName, channel);
    return channel;
  }

  unsubscribeFromPresence(conversationId: string) {
    const channelName = `presence-conversation.${conversationId}`;
    const channel = this.channels.get(channelName);

    if (channel) {
      this.pusher?.unsubscribe(channelName);
      this.channels.delete(channelName);
    }
  }

  disconnect() {
    if (this.pusher) {
      console.log('👋 Disconnecting WebSocket...');
      this.channels.forEach((_, channelName) => {
        this.pusher?.unsubscribe(channelName);
      });
      this.channels.clear();
      this.pusher.disconnect();
      this.pusher = null;
      console.log('✅ WebSocket disconnected');
    }
  }

  getConnectionState(): string {
    return this.pusher?.connection.state || 'disconnected';
  }
}

export default new WebSocketService();
