import { LLM } from "./aiService";

export interface Message {
  id?: number | null;
  content: string;
  role: string;
  created_at: string;
  timestamp: Date;
  type: string;
}

export interface Chat {
  id: number;
  name: string;
  llm: LLM;
  created_at: string;
}
