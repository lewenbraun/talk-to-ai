import { defineStore } from "pinia";
import { api } from "@/boot/axios";
import { handleApiError } from "@/utils/errorHandler";
import { LoginData, RegisterData, UserState, User } from "@/types/user";

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
      try {
        const { data } = await api.post("/register", user);
        this.setUser(data.user);
        this.setToken(data.token);
      } catch (error) {
        handleApiError(error);
      }
    },
    async login(user: LoginData): Promise<void> {
      try {
        const { data } = await api.post("/login", user);
        this.setUser(data.user);
        this.setToken(data.token);
      } catch (error) {
        handleApiError(error);
      }
    },
    async logout(): Promise<void> {
      try {
        await api.post("/logout");
        this.logoutUser();
      } catch (error) {
        handleApiError(error);
      }
    },
    async loadUser(): Promise<void> {
      const { data } = await api.get("/user");
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
        try {
          const { data } = await api.post("/create-temporary-user");
          this.setUser(data.user);
          this.setToken(data.token);
        } catch (error) {
          handleApiError(error);
        }
      }
    },
  },
});
