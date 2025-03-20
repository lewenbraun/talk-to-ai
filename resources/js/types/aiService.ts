export interface LLM {
  id: number;
  name: string;
  isLoaded: boolean;
}

export interface AiService {
  id: number;
  name: string;
  url_api: string;
  api_key?: string;
  llms: LLM[];
}
