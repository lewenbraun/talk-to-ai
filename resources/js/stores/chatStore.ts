import { defineStore } from "pinia";
import { useUserStore } from "@/stores/userStore";
import { api } from "@/boot/axios";
import router from "@/router";

export interface Message {
  id?: number | null;
  content: string;
  timestamp: Date;
  type: string;
}

export interface Chat {
  id: number;
  name: string;
}

export const useChatStore = defineStore("chatStore", {
  state: () => ({
    currentChat: null as Chat | null,
    chats: [] as Chat[],
    messagesByChat: {} as Record<number, Message[]>,
    isNewChatMode: false,
  }),
  getters: {
    currentMessages(state): Message[] {
      if (!state.currentChat) return [];
      return state.messagesByChat[state.currentChat.id ?? -1] || [];
    },
    userName(): string {
      const userStore = useUserStore();
      return userStore.user.name || "Unknown User";
    },
  },
  actions: {
    async setCurrentChat(chat: Chat) {
      this.currentChat = chat;
      console.log(this.currentChat);
      this.isNewChatMode = false;
      if (chat && !this.messagesByChat[chat.id]) {
        await this.loadMessagesForChat(chat);
      }
      console.log(this.currentChat);
    },
    startNewChat() {
      this.currentChat = null;
      this.isNewChatMode = true;
      this.messagesByChat = {};
    },
    async sendMessage(content: string) {
      const newMessage = {
        id: null,
        content,
        timestamp: new Date(),
        type: "outgoing",
      } as Message;

      if (this.isNewChatMode) {
        await this.sendMessageInNewChat(newMessage);
      } else {
        if (this.currentChat) {
          await this.sendMessageInExistingChat(newMessage, this.currentChat.id);
        }
      }
    },
    async sendMessageInNewChat(message: Message) {
      try {
        const response = await api.post("/api/chat/new/send-message", {
          content: message.content,
        });
        const data = response.data;

        const newChat: Chat = {
          id: data.chat.id,
          name: "New chat",
        };
        this.messagesByChat[newChat.id] = [message];

        this.chats.unshift(newChat);
        console.log(this.currentChat);

        message.id = data.message.id;

        router.push({ name: "chat", params: { chat_id: newChat.id } });
        this.setCurrentChat(newChat);

        this.isNewChatMode = false;
      } catch (error) {
        console.error("Error sending message in new chat:", error);
        throw error;
      }
    },
    async sendMessageInExistingChat(message: Message, chat_id: number) {
      try {
        this.messagesByChat[chat_id].push(message);

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
        this.messagesByChat[chat.id] = messagesResponse.data;
      } catch (error) {
        console.error(`Error loading messages for chat ${chat.id}:`, error);
      }
    },
    loadMoreMessages() {
      console.log("Load more messages");
    },
  },
});
