import { defineStore } from "pinia";

interface Message {
  id: number;
  user: string;
  content: string;
  timestamp: Date;
  type: string;
}

interface Contact {
  id: number;
  name: string;
  avatar: string;
  lastMessage: string;
}

export const useChatStore = defineStore("chatStore", {
  state: () => ({
    currentUser: "Alice",
    currentContact: null as Contact | null,
    contacts: [
      {
        id: 1,
        name: "Alice",
        avatar:
          "https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato",
        lastMessage: "Hoorayy!!",
      },
      {
        id: 2,
        name: "Martin",
        avatar:
          "https://placehold.co/200x/ad922e/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato",
        lastMessage: "That pizza place was amazing!",
      },
    ] as Contact[],
    messagesByContact: {} as Record<number, Message[]>,
  }),
  getters: {
    currentMessages(state) {
      if (!state.currentContact) return [];
      return state.messagesByContact[state.currentContact.id] || [];
    },
  },
  actions: {
    async setCurrentContact(contact: Contact) {
      this.currentContact = contact;
      if (!this.messagesByContact[contact.id]) {
        await this.loadMessagesForContact(contact);
      }
    },
    async loadMessagesForContact(contact: Contact) {
      const dummyMessages: Message[] = [
        {
          id: 1,
          user: contact.name,
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
      this.messagesByContact[contact.id] = dummyMessages;
    },
    sendMessage(content: string) {
      if (!this.currentContact) return;
      const newMsg = {
        id: Date.now(),
        user: this.currentUser,
        content,
        timestamp: new Date(),
        type: "outgoing",
      };
      if (!this.messagesByContact[this.currentContact.id]) {
        this.messagesByContact[this.currentContact.id] = [];
      }
      this.messagesByContact[this.currentContact.id].push(newMsg);
    },
    loadMoreMessages() {
      console.log("Load more messages");
    },
  },
});
