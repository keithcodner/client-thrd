import Pusher from 'pusher-js/react-native';
import { PUSHER_CONFIG } from '@/config/env';

class WebSocketService {
  private pusher: Pusher | null = null;
  private channels: Map<string, any> = new Map();
  private isConnecting: boolean = false;

  connect(authToken: string, userId: number) {
    if (this.pusher || this.isConnecting) {
      console.log('WebSocket already connected or connecting');
      return;
    }

    this.isConnecting = true;

    try {
      this.pusher = new Pusher(PUSHER_CONFIG.key, {
        wsHost: PUSHER_CONFIG.wsHost,
        wsPort: PUSHER_CONFIG.wsPort,
        wssPort: PUSHER_CONFIG.wssPort,
        forceTLS: PUSHER_CONFIG.forceTLS,
        enabledTransports: PUSHER_CONFIG.enabledTransports,
        cluster: PUSHER_CONFIG.cluster,
        authEndpoint: `${PUSHER_CONFIG.apiUrl}/broadcasting/auth`,
        auth: {
          headers: {
            Authorization: `Bearer ${authToken}`,
            Accept: 'application/json',
          },
        },
      });

      this.pusher.connection.bind('connected', () => {
        console.log('✅ WebSocket connected');
        this.isConnecting = false;
      });

      this.pusher.connection.bind('error', (error: any) => {
        console.error('❌ WebSocket error:', error);
        this.isConnecting = false;
      });

      this.pusher.connection.bind('disconnected', () => {
        console.log('⚠️ WebSocket disconnected, attempting to reconnect...');
        this.isConnecting = false;
        setTimeout(() => this.reconnect(), 3000);
      });

      this.pusher.connection.bind('failed', () => {
        console.error('💥 WebSocket connection failed');
        this.isConnecting = false;
      });
    } catch (error) {
      console.error('Failed to create Pusher instance:', error);
      this.isConnecting = false;
    }
  }

  reconnect() {
    if (this.pusher && !this.isConnecting) {
      console.log('🔄 Attempting to reconnect WebSocket...');
      this.pusher.connect();
    }
  }

  subscribeToConversation(
    conversationId: string,
    onNewMessage: (data: any) => void
  ) {
    if (!this.pusher) {
      console.error('WebSocket not connected. Call connect() first.');
      return null;
    }

    const channelName = `private-sitePrivateChat.${conversationId}`;
    
    // Check if already subscribed
    if (this.channels.has(channelName)) {
      console.log(`Already subscribed to ${channelName}`);
      return this.channels.get(channelName);
    }

    console.log(`📡 Subscribing to ${channelName}`);
    const channel = this.pusher.subscribe(channelName);

    channel.bind('pusher:subscription_succeeded', () => {
      console.log(`✅ Subscribed to ${channelName}`);
    });

    channel.bind('pusher:subscription_error', (error: any) => {
      console.error(`❌ Error subscribing to ${channelName}:`, error);
    });

    channel.bind('newMessage', (data: any) => {
      console.log('📨 New message received:', data);
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
    onMemberList: (members: any[]) => void
  ) {
    if (!this.pusher) {
      console.error('WebSocket not connected');
      return null;
    }

    const channelName = `presence-conversation.${conversationId}`;
    
    if (this.channels.has(channelName)) {
      return this.channels.get(channelName);
    }

    const channel = this.pusher.subscribe(channelName);

    channel.bind('pusher:subscription_succeeded', (members: any) => {
      const memberList = Object.values(members.members);
      onMemberList(memberList);
    });

    channel.bind('pusher:member_added', (member: any) => {
      onUserJoined(member.info);
    });

    channel.bind('pusher:member_removed', (member: any) => {
      onUserLeft(member.info);
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
