import { defineStore } from "pinia";
import { api } from "@/boot/axios";
import { handleApiError } from "@/utils/errorHandler";

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

export const useAiServiceStore = defineStore("aiServiceStore", {
  state: () => ({
    aiServices: [] as AiService[],
    currentLLM: null as LLM | null,
  }),
  actions: {
    async loadAiServiceList() {
      try {
        const response = await api.get("/api/ai-service/list");
        this.aiServices = response.data.data as AiService[];
      } catch (error) {
        handleApiError(error);
      }
    },
    async loadAiLLMListByAiServiceId(ai_service_id: number) {
      try {
        const response = await api.get(
          `/api/ai-service/llm/list/${ai_service_id}`
        );

        this.aiServices.forEach((service) => {
          if (service.id === ai_service_id) {
            service.llms = response.data as LLM[];
          }
        });
      } catch (error) {
        handleApiError(error);
      }
    },
    async addLLM(ai_service_id: number, llm_name: string) {
      try {
        const response = await api.post(`/api/ai-service/llm/add`, {
          ai_service_id: ai_service_id,
          llm_name: llm_name,
        });

        let addedLLM = response.data as LLM;

        this.aiServices.forEach((service) => {
          if (service.id === ai_service_id) {
            service.llms.push({
              id: addedLLM.id,
              name: addedLLM.name,
              isLoaded: false,
            });
          }
        });

        return addedLLM;
      } catch (error) {
        handleApiError(error);
      }
    },
    async deleteLLM(ai_service_id: number, llm_id: number) {
      try {
        const response = await api.post(`/api/ai-service/llm/delete`, {
          ai_service_id: ai_service_id,
          llm_id: llm_id,
        });
        if (response.status === 200) {
          this.aiServices.forEach((service) => {
            if (service.id === ai_service_id) {
              service.llms = service.llms.filter((llm) => llm.id !== llm_id);
            }
          });
          return true;
        }
      } catch (error) {
        handleApiError(error);
      }
    },
    async updateAiServiceUrl(ai_service_id: number, new_url: string) {
      try {
        const service = this.aiServices.find((s) => s.id === ai_service_id);
        if (service) {
          let response = await api.post(`/api/ai-service/api-url/set`, {
            ai_service_id: ai_service_id,
            url_api: new_url,
          });
          if (response.status === 200) {
            service.url_api = new_url;
            return true;
          }
        }
      } catch (error) {
        handleApiError(error);
      }
    },
  },
});
