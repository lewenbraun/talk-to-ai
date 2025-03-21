import { defineStore } from "pinia";
import { api } from "@/boot/axios";
import { handleApiError } from "@/utils/errorHandler";
import type { AiService, LLM } from "@/types/aiService";

export const useAiServiceStore = defineStore("aiServiceStore", {
  state: () => ({
    aiServices: [] as AiService[],
    currentLLM: null as LLM | null,
  }),
  actions: {
    async loadAiServiceList(): Promise<void> {
      try {
        const response = await api.get("/ai-service/list");
        this.aiServices = response.data.data as AiService[];
      } catch (error) {
        handleApiError(error);
      }
    },
    async loadAiLLMListByAiServiceId(ai_service_id: number): Promise<void> {
      try {
        const response = await api.get(`/ai-service/llm/list/${ai_service_id}`);

        this.aiServices.forEach((service) => {
          if (service.id === ai_service_id) {
            service.llms = response.data as LLM[];
          }
        });
      } catch (error) {
        handleApiError(error);
      }
    },
    async addLLM(ai_service_id: number, llm_name: string): Promise<LLM | null> {
      try {
        const response = await api.post(`/ai-service/llm/add`, {
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
        return null;
      }
    },
    async deleteLLM(ai_service_id: number, llm_id: number): Promise<boolean> {
      try {
        const response = await api.post(`/ai-service/llm/delete`, {
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
        return false;
      } catch (error) {
        handleApiError(error);
        return false;
      }
    },
    async updateAiServiceUrl(
      ai_service_id: number,
      new_url: string
    ): Promise<boolean> {
      try {
        const service = this.aiServices.find((s) => s.id === ai_service_id);
        if (service) {
          const response = await api.post(`/ai-service/url-api/set`, {
            ai_service_id: ai_service_id,
            url_api: new_url,
          });
          return response.status === 200;
        }
        return false;
      } catch (error) {
        handleApiError(error);
        return false;
      }
    },
  },
});
