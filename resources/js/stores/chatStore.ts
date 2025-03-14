import { defineStore } from "pinia";
import { api } from "@/boot/axios";
import { LLM, useAiServiceStore } from "@/stores/aiServiceStore";
import router from "@/router";

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
    async setCurrentChat(chat: Chat) {
      this.currentChat = chat;
      this.isNewChatMode = false;
      this.aiServiceStore.currentLLM = chat.llm;
      if (chat && !this.messagesByChat[chat.id]) {
        await this.loadMessagesForChat(chat);
        this.subscribeToChannel(chat.id);
      }
    },
    startNewChat() {
      this.currentChat = null;
      this.isNewChatMode = true;
      this.messagesByChat = {};
      this.unsubscribeFromChannel();
    },
    async messageSendingProcess(content: string) {
      const newMessage = {
        id: null,
        content,
        timestamp: new Date(),
        type: "outgoing",
      } as Message;

      if (this.isNewChatMode && this.aiServiceStore.currentLLM) {
        let newChat = await this.createChat(this.aiServiceStore.currentLLM);
        this.chats.unshift(newChat);

        router.push({ name: "chat", params: { chat_id: newChat.id } });
        await this.setCurrentChat(newChat);
        await this.sendMessage(newMessage, newChat.id);
      } else {
        if (this.currentChat) {
          await this.sendMessage(newMessage, this.currentChat.id);
        }
      }
    },
    async createChat(llm: LLM): Promise<Chat> {
      const newChatResoponse = await api.post("/api/chat/create", {
        llm_id: llm.id,
      });

      const data = newChatResoponse.data;

      const newChat: Chat = {
        id: data.chat.id,
        llm: llm,
        name: "New chat",
        created_at: data.chat.created_at,
      };

      return newChat;
    },
    async sendMessage(message: Message, chat_id: number) {
      this.isGeneratingAnswer = true;
      this.currentAssistantMessage = {
        id: null,
        content: "",
        timestamp: new Date(),
        type: "incoming",
      } as Message;
      this.messagesByChat[chat_id].push(message, this.currentAssistantMessage);

      try {
        const response = await api.post("/api/chat/send-message", {
          chat_id: chat_id,
          content: message.content,
        });
        const data = response.data;
        let addedMessage = this.messagesByChat[chat_id]?.find(
          (item) => item === message
        );
        if (addedMessage) addedMessage.id = data.message.id;
      } catch (error) {
        console.error("Error sending message in existing chat:", error);
        this.isGeneratingAnswer = false;
        this.currentAssistantMessage = null;
        this.messagesByChat[chat_id].pop();

        throw error;
      }
    },
    async loadChatList() {
      try {
        let chatsResponse = await api.get("/api/chat/list");
        this.chats = chatsResponse.data as Chat[];
      } catch (error) {
        console.error("Error loading chat list:", error);
      }
    },
    async loadMessagesForChat(chat: Chat) {
      try {
        let messagesResponse = await api.get(`/api/chat/messages/${chat.id}`);
        this.messagesByChat[chat.id] = messagesResponse.data.map(
          (msg: Message) => ({
            ...msg,
            timestamp: new Date(msg.created_at),
            type: msg.role === "user" ? "outgoing" : "incoming",
          })
        );
      } catch (error) {
        console.error(`Error loading messages for chat ${chat.id}:`, error);
      }
    },
    loadMoreMessages() {
      console.log("Load more messages");
    },
    subscribeToChannel(chatId: number) {
      if (chatId) {
        window.Echo.private(`chat.${chatId}`)
          .listen(".llm.chunk.generated", (event: any) => {
            if (event.contentChunk) {
              this.currentAssistantMessage!.content += event.contentChunk;
            }
          })
          .listen(".llm.answer.generated", () => {
            this.isGeneratingAnswer = false;
          });
      }
    },
    unsubscribeFromChannel() {
      if (this.currentChat) {
        window.Echo.leaveChannel(`chat.${this.currentChat.id}`);
      }
    },
  },
});
