import { defineStore } from "pinia";
import { api } from "@/boot/axios";

export interface LoginData {
  email: string;
  password: string;
}

export interface RegisterData {
  email: string;
  password: string;
  password_confirmation: string;
}

interface User {
  id?: number;
  email?: string;
  is_temporary: boolean;
  created_at?: string;
  updated_at?: string;
}

interface UserState {
  user: User;
  token: string | null;
}

export const useUserStore = defineStore("userStore", {
  state: (): UserState => ({
    user: {
      is_temporary: true,
    },
    token: localStorage.getItem("TOKEN"),
  }),

  getters: {},

  actions: {
    async register(user: RegisterData): Promise<void> {
      const { data } = await api.post("/api/register", user);
      this.setUser(data.user);
      this.setToken(data.token);
    },
    async login(user: LoginData): Promise<void> {
      const { data } = await api.post("/api/login", user);
      this.setUser(data.user);
      this.setToken(data.token);
    },
    async logout(): Promise<void> {
      await api.post("/api/logout");
      this.logoutUser();
    },
    async updateUser(user: User): Promise<User> {
      const { data } = await api.post("/api/user/update", user);
      this.setUser(data);
      return data;
    },
    async getUser(): Promise<void> {
      const { data } = await api.get("/api/user");
      this.setUser(data);
    },
    async loadUser(): Promise<void> {
      const { data } = await api.get("/api/user");
      this.setUser(data);
    },
    setUser(user: User): void {
      this.user = user;
    },
    setToken(token: string): void {
      this.token = token;
      localStorage.setItem("TOKEN", token);
    },
    isUserAuth(): boolean {
      return this.token ? true : false;
    },
    isTemporaryUser(): boolean {
      return this.user.is_temporary ? true : false;
    },
    logoutUser(): void {
      this.token = null;
      localStorage.removeItem("TOKEN");
      window.location.reload();
    },
    async createTemporaryUser(): Promise<void> {
      if (!this.isUserAuth()) {
        const { data } = await api.post("/api/create-temporary-user");
        this.setUser(data.user);
        this.setToken(data.token);
      }
    },
  },
});
