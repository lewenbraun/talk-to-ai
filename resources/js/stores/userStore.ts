import { defineStore } from "pinia";
import axios from "../plugins/axios";

interface UserState {
  name?: string;
  token: string | null;
}

export const useUserStore = defineStore("userStore", {
  state: (): { user: UserState } => ({
    user: {
      name: "",
      token: localStorage.getItem("TOKEN"),
    },
  }),

  getters: {
    isAuthenticated: (state) => !!state.user.token,
  },

  actions: {
    async register(user: Record<string, unknown>) {
      const { data } = await axios.post("/register", user);
      this.setUser(data.user);
      this.setToken(data.token);
      return data;
    },
    async login(user: Record<string, unknown>) {
      const { data } = await axios.post("/login", user);
      this.setUser(data.user);
      this.setToken(data.token);
    },
    async logout() {
      await axios.post("/logout");
      this.logoutUser();
    },
    async updateUser(user: UserState): Promise<UserState> {
      const { data } = await axios.post("/user/update", user);
      this.setUser(data);
      return data;
    },
    async getUser() {
      const { data } = await axios.get("/user");
      this.setUser(data);
    },
    setUser(user: UserState) {
      this.user = user;
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
  },
});
