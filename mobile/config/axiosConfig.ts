import axios from "axios";
import { API_BASE_URL } from "./env";
import * as SecureStore from "expo-secure-store";

const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
    },
    withCredentials: false,
});

// Add a request interceptor to include the auth token in headers
axiosInstance.interceptors.request.use(
    async (config) => {
        const token = await SecureStore.getItemAsync("session");
        if (token) {
            config.headers["Authorization"] = `Bearer ${token}`;
        }
        return config;
    }, (error) => {
        return Promise.reject(error);
    }
);

export default axiosInstance;