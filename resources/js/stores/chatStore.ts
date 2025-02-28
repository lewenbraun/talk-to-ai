import { defineStore } from "pinia";

export interface Message {
  id: number;
  user: string;
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
    currentUser: "Alice",
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
  },
  actions: {
    async setCurrentChat(chat: Chat) {
      this.currentChat = chat;
      if (!this.messagesByChat[chat.id!]) {
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
          user: chat.name,
          content: "Hello",
          timestamp: new Date(),
          type: "incoming",
        },
        {
          id: 2,
          user: this.currentUser,
          content: "Hi",
          timestamp: new Date(),
          type: "outgoing",
        },
      ];
      this.messagesByChat[chat.id!] = dummyMessages;
    },
    sendMessage(content: string) {
      if (!this.currentChat) return;
      const newMsg = {
        id: Date.now(),
        user: this.currentUser,
        content,
        timestamp: new Date(),
        type: "outgoing",
      };
      if (!this.messagesByChat[this.currentChat.id!]) {
        this.messagesByChat[this.currentChat.id!] = [];
      }
      this.messagesByChat[this.currentChat.id!].push(newMsg);
    },
    loadMoreMessages() {
      console.log("Load more messages");
    },
  },
});
