import axiosInstance from "@/config/axiosConfig";

export const createCircle = async (circleData: any) => {
  try {
    const response = await axiosInstance.post("/create-circle", circleData);
    return response.data;
  } catch (error) {
    console.error("Error creating circle:", error);
    throw error;
  }
};