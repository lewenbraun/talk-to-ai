import { defineStore } from 'pinia';

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

export const useChatStore = defineStore('chatStore', {
  state: () => ({
    currentUser: 'Alice',
    currentContact: null as Contact | null,
    contacts: [
      { id: 1, name: 'Alice', avatar: 'https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato', lastMessage: 'Hoorayy!!' },
      { id: 2, name: 'Martin', avatar: 'https://placehold.co/200x/ad922e/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato', lastMessage: 'That pizza place was amazing!' },
    ] as Contact[],
    messages: [
      { id: 1, user: 'Alice', content: "Hey Bob, how's it going?", timestamp: new Date(), type: 'incoming' },
      { id: 2, user: 'Alice', content: "Hi Alice! I'm good, just finished a great book. How about you?", timestamp: new Date(), type: 'outgoing' },
    ] as Message[],
  }),
  actions: {
    sendMessage(content: string) {
      const newMessage = {
        id: Date.now(),
        user: this.currentUser,
        content,
        timestamp: new Date(),
        type: 'outgoing'
      };
      this.messages.push(newMessage);
    },
    receiveMessage(message: Message) {
      this.messages.push(message);
    },
    loadMoreMessages() {
      console.log('Load more messages...');
    },
    setCurrentContact(contact: Contact) {
      this.currentContact = contact;
    }
  }
});
