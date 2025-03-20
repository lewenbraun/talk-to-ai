export interface LoginData {
  email: string;
  password: string;
}

export interface RegisterData {
  email: string;
  password: string;
  password_confirmation: string;
}

export interface User {
  id?: number;
  email?: string;
  is_temporary: boolean;
  created_at?: string;
  updated_at?: string;
}

export interface UserState {
  user: User;
  token: string | null;
}
