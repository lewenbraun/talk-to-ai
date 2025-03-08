import { defineStore } from "pinia";
import { api } from "@/boot/axios";

interface UserState {
  id: number | null;
  name?: string;
  token: string | null;
}

export const useUserStore = defineStore("userStore", {
  state: (): { user: UserState } => ({
    user: {
      id: Number(localStorage.getItem("USER_ID")),
      name: "",
      token: localStorage.getItem("TOKEN"),
    },
  }),

  getters: {
    isAuthenticated: (state) => !!state.user.token,
  },

  actions: {
    async register(user: Record<string, unknown>) {
      const { data } = await api.post("/api/register", user);
      this.setUser(data.user);
      this.setToken(data.token);
      return data;
    },
    async login(user: Record<string, unknown>) {
      const { data } = await api.post("/api/login", user);
      this.setUser(data.user);
      this.setToken(data.token);
    },
    async logout() {
      await api.post("/api/logout");
      this.logoutUser();
    },
    async updateUser(user: UserState): Promise<UserState> {
      const { data } = await api.post("/api/user/update", user);
      this.setUser(data);
      return data;
    },
    async getUser() {
      const { data } = await api.get("/api/user");
      this.setUser(data);
    },
    async getUserId() {
      const { data } = await api.get("/api/user");
      this.setUserId(data);
    },
    setUser(user: UserState) {
      this.user = user;
    },
    setUserId(user: UserState) {
      this.user.id = user.id;
      localStorage.setItem("USER_ID", String(user.id));
    },
    setToken(token: string) {
      this.user.token = token;
      localStorage.setItem("TOKEN", token);
    },
    userAuth() {
      return this.user.token ? true : false;
    },
    logoutUser() {
      this.user.token = null;
      this.user.name = "";
      localStorage.removeItem("TOKEN");
    },
    async createTemporaryUser() {
      if (!this.userAuth()) {
        const { data } = await api.post("/api/create-temporary-user");
        this.setToken(data.token);
        this.setUserId(data.user);
      }
    },
  },
});
