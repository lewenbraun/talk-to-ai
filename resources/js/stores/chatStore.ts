import { defineStore } from "pinia";
import { api } from "@/boot/axios";
import { useAiServiceStore } from "@/stores/aiServiceStore";
import router from "@/router";
import { handleApiError } from "@/utils/errorHandler";
import type { Chat, Message } from "@/types/chat";
import type { LLM } from "@/types/aiService";

export const useChatStore = defineStore("chatStore", {
  state: () => ({
    currentChat: null as Chat | null,
    chats: [] as Chat[],
    messagesByChat: {} as Record<number, Message[]>,
    isNewChatMode: false,
    isGeneratingAnswer: false,
    currentAssistantMessage: null as Message | null,
  }),
  getters: {
    currentMessages(state): Message[] {
      if (!state.currentChat) return [];
      return state.messagesByChat[state.currentChat.id ?? -1] || [];
    },
    aiServiceStore() {
      return useAiServiceStore();
    },
  },
  actions: {
    async setCurrentChat(chat: Chat): Promise<void> {
      try {
        this.currentChat = chat;
        this.isNewChatMode = false;
        this.aiServiceStore.currentLLM = chat.llm;

        if (chat && !this.messagesByChat[chat.id]) {
          await this.loadMessagesForChat(chat);
          this.subscribeToChannel(chat.id);
        }
      } catch (error) {
        handleApiError(error);
      }
    },

    startNewChat(): void {
      this.currentChat = null;
      this.isNewChatMode = true;
      this.messagesByChat = {};
      this.unsubscribeFromChannel();
    },

    async messageSendingProcess(content: string): Promise<void> {
      const newMessage = {
        id: null,
        content,
        timestamp: new Date(),
        type: "outgoing",
      } as Message;

      try {
        if (this.isNewChatMode && this.aiServiceStore.currentLLM) {
          const newChat = await this.createChat(this.aiServiceStore.currentLLM);
          if (!newChat) return;

          this.chats.unshift(newChat);
          router.push({ name: "chat", params: { chat_id: newChat.id } });
          await this.setCurrentChat(newChat);
          await this.sendMessage(newMessage, newChat.id);
        } else if (this.currentChat) {
          await this.sendMessage(newMessage, this.currentChat.id);
        }
      } catch (error) {
        handleApiError(error);
      }
    },

    async createChat(llm: LLM): Promise<Chat | null> {
      try {
        const response = await api.post("/chat/create", { llm_id: llm.id });
        const chat = response.data;

        return {
          id: chat.id,
          llm: llm,
          name: "New chat",
          created_at: chat.created_at,
        };
      } catch (error) {
        handleApiError(error);
        return null;
      }
    },

    async sendMessage(message: Message, chat_id: number): Promise<void> {
      try {
        this.isGeneratingAnswer = true;
        this.currentAssistantMessage = {
          id: null,
          content: "",
          timestamp: new Date(),
          type: "incoming",
        } as Message;

        this.messagesByChat[chat_id].push(
          message,
          this.currentAssistantMessage
        );

        const response = await api.post("/chat/send-message", {
          chat_id,
          content: message.content,
        });

        const addedMessage = this.messagesByChat[chat_id]?.find(
          (item) => item === message
        );
        if (addedMessage) addedMessage.id = response.data.id;
      } catch (error) {
        handleApiError(error);
        this.isGeneratingAnswer = false;
        this.currentAssistantMessage = null;
        this.messagesByChat[chat_id].pop();
        throw error;
      }
    },

    async loadChatList(): Promise<void> {
      try {
        const response = await api.get("/chat/list");
        this.chats = response.data as Chat[];
      } catch (error) {
        handleApiError(error);
      }
    },

    async loadMessagesForChat(chat: Chat): Promise<void> {
      try {
        const response = await api.get(`/chat/messages/${chat.id}`);
        this.messagesByChat[chat.id] = response.data.map((msg: Message) => ({
          ...msg,
          timestamp: new Date(msg.created_at),
          type: msg.role === "user" ? "outgoing" : "incoming",
        }));
      } catch (error) {
        handleApiError(error);
      }
    },

    loadMoreMessages(): void {
      console.log("Load more messages");
    },

    subscribeToChannel(chatId: number): void {
      if (chatId) {
        window.Echo.private(`chat.${chatId}`)
          .listen(".llm.chunk.generated", (event: any) => {
            if (event.contentChunk && this.currentAssistantMessage) {
              this.currentAssistantMessage.content += event.contentChunk;
            }
          })
          .listen(".llm.answer.generated", () => {
            this.isGeneratingAnswer = false;
          });
      }
    },

    unsubscribeFromChannel(): void {
      if (this.currentChat) {
        window.Echo.leaveChannel(`chat.${this.currentChat.id}`);
      }
    },
  },
});
