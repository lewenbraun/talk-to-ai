import { defineStore } from 'pinia';
import axios  from '../plugins/axios';

export interface UserParameters {
  maxNutrients: MaxNutrients;
  profileData: ProfileData;
}

export interface MaxNutrients {
  proteins?: number;
  fats?: number;
  carbs?: number;
  calories?: number;
}

export interface ProfileData {
  name: string;
  weight?: number;
}

interface UserState {
  data: UserParameters;
  token: string | null;
}

export const useUserStore = defineStore('userStore', {
  state: (): { user: UserState } => ({
    user: {
      data: {
        maxNutrients: {},
        profileData: {
          name: '',
        },
      },
      token: localStorage.getItem('TOKEN'),
    },
  }),

  getters: {
    isAuthenticated: (state) => !!state.user.token,
    maxCountNutrients: (state) => {
      const { proteins, fats, carbs, calories } = state.user.data.maxNutrients;
      return {
        proteins: proteins,
        fats: fats,
        carbs: carbs,
        calories: calories,
      };
    },
  },

  actions: {
    async register(user: Record<string, unknown>) {
      const { data } = await axios.post('/register', user);
      this.setUser(data.user);
      this.setToken(data.token);
      return data;
    },
    async login(user: Record<string, unknown>) {
      const { data } = await axios.post('/login', user);
      this.setUser(data.user);
      this.setToken(data.token);
    },
    async logout() {
      await axios.post('/logout');
      this.logoutUser();
    },
    async updateUser(user: UserParameters): Promise<UserParameters> {
      const { data } = await axios.post('/user/update', user);
      this.setUser(data);
      return data;
    },
    async getUser() {
      const { data } = await axios.get('/user');
      this.setUser(data);
    },
    async getProfileData() {
      const { data } = await axios.get('/user/profile-data');
      this.setProfileData(data);
    },
    async getMaxNutrients() {
      const { data } = await axios.get('/user/max-nutrients');
      this.setMaxNutrients(data);
    },
    async setMaxNutrients(maxNutrients: MaxNutrients) {
      this.user.data.maxNutrients = maxNutrients;
    },
    async setProfileData(profileData: ProfileData) {
      this.user.data.profileData = profileData;
    },
    setUser(user: UserParameters) {
      this.user.data = user;
    },
    setToken(token: string) {
      this.user.token = token;
      localStorage.setItem('TOKEN', token);
    },
    userAuth() {
      return this.user.token ? true : false;
    },
    logoutUser() {
      this.user.token = null;
      this.user.data = {
        maxNutrients: {},
        profileData: {
          name: '',
        },
      };
      localStorage.removeItem('TOKEN');
    },
  },
});
