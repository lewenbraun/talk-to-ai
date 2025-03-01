import { defineStore } from "pinia";
import { useUserStore } from "@/stores/userStore";
import { api } from "@/boot/axios";

export interface Message {
  id?: number | null;
  content: string;
  timestamp: Date;
  type: string;
}

export interface Chat {
  id?: number | null;
  name: string;
}

export const useChatStore = defineStore("chatStore", {
  state: () => ({
    currentChat: null as Chat | null,
    chats: [
      {
        id: 1,
        name: "What is Lorem Ipsum?",
      },
      {
        id: 2,
        name: "Better keyboard in the world",
      },
    ] as Chat[],
    messagesByChat: {} as Record<number, Message[]>,
  }),
  getters: {
    currentMessages(state) {
      if (!state.currentChat) return [];
      return state.messagesByChat[state.currentChat.id ?? -1] || [];
    },
    userName() {
      const userStore = useUserStore();
      return userStore.user.name;
    },
  },
  actions: {
    async setCurrentChat(chat: Chat) {
      this.currentChat = chat;
      if (chat && !this.messagesByChat[chat.id!]) {
        await this.loadMessagesForChat(chat);
      }
    },
    async createNewChat() {
      const newChat = {
        id: null,
        name: "New chat",
      };
      await this.setCurrentChat(newChat);
    },
    async loadMessagesForChat(chat: Chat) {
      const dummyMessages: Message[] = [
        {
          id: 1,
          content: "Hello",
          timestamp: new Date(),
          type: "incoming",
        },
        {
          id: 2,
          content: "Hi",
          timestamp: new Date(),
          type: "outgoing",
        },
      ];
      this.messagesByChat[chat.id!] = dummyMessages;
    },
    async sendMessage(content: string) {
      if (!this.currentChat) return;

      const newMessage = {
        id: null,
        content,
        timestamp: new Date(),
        type: "outgoing",
      } as Message;

      if (!this.messagesByChat[this.currentChat.id!]) {
        this.messagesByChat[this.currentChat.id!] = [];
      }
      this.messagesByChat[this.currentChat.id!].push(newMessage);

      if (this.currentChat.id === null) {
        await this.sendMessageInNewChat(newMessage);
      } else {
        await this.sendMessageInExistingChat(newMessage);
      }
    },
    async sendMessageInNewChat(message: Message) {
      try {
        const response = await api.post("api/chat/new/send-message", {
          content: message.content,
        });
        const data = response.data;

        let newChat = {
          id: data.chat.id,
          name: data.chat.name,
        };

        this.setCurrentChat(newChat);

        if (this.currentChat) {
          var addedMessage = this.messagesByChat[this.currentChat.id!].find(
            (item) => item === message
          );
        }
        if (addedMessage) addedMessage.id = data.message.id;
      } catch (error) {
        console.error("Error sending message:", error);
        return null;
      }
    },
    async sendMessageInExistingChat(message: Message) {
      try {
        const response = await api.post("api/chat/send-message", {
          content: message.content,
        });
        const data = response.data;

        if (this.currentChat) {
          var addedMessage = this.messagesByChat[this.currentChat.id!].find(
            (item) => item === message
          );
        }
        if (addedMessage) addedMessage.id = data.message.id;
      } catch (error) {
        console.error("Error sending message:", error);
      }
    },
    loadMoreMessages() {
      console.log("Load more messages");
    },
  },
});
