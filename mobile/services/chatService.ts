import axiosInstance from "@/config/axiosConfig";

export interface SendMessageData {
  conversation_id: number;
  content: string;
  type?: "chat" | "announcement" | "system";
  end_user_id?: number;
}

export const createCircle = async (circleData: any) => {
  try {
    const response = await axiosInstance.post("/create-circle", circleData);
    return response.data;
  } catch (error) {
    console.error("Error creating circle:", error);
    throw error;
  }
};

export const getUserCircleData = async () => {
  try {
    const response = await axiosInstance.post("/user-circles");
    return response.data;
  } catch (error) {
    console.error("Error fetching user circles:", error);
    throw error;
  }
};

export const sendMessage = async (messageData: SendMessageData) => {
  try {
    const response = await axiosInstance.post("/post-chat", messageData);
    return response.data;
  } catch (error) {
    console.error("Error sending message:", error);
    throw error;
  }
};

export const getConversationMessages = async (conversationId: number, limit: number = 30) => {
  try {
    const response = await axiosInstance.post("/chat", {
      conversation_id: conversationId,
      limit,
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching conversation messages:", error);
    throw error;
  }
};