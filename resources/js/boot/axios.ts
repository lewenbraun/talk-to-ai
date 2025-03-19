import { useUserStore } from "../stores/userStore";
import axios, { AxiosInstance } from "axios";

declare module "vue" {
  interface ComponentCustomProperties {
    $axios: AxiosInstance;
    $api: AxiosInstance;
  }
}

const api = axios.create({
  transformRequest: [
    (data) => {
      return JSON.stringify(data);
    },
  ],
  headers: {
    "Content-Type": "application/json",
  },
});

api.interceptors.request.use((config) => {
  const userStore = useUserStore();

  config.headers.Authorization = `Bearer ${userStore.token}`;
  return config;
});

export { api };
