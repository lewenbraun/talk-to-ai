<template>
  <div
    class="w-[400px] bg-white border-r border-gray-300 flex flex-col h-full relative"
  >
    <div class="overflow-y-auto max-h-[calc(100vh-60px)] p-3">
      <NewChatItem icon="plus" @click="startNewChat()" />
      <hr class="my-2 border-gray-400" />
      <ChatItem
        v-for="(chat, index) in chatStore.chats"
        :chat="chat"
        :key="index"
        @click="selectChat(chat)"
      />
    </div>
    <div class="absolute bottom-0 w-full">
      <hr class="border-gray-300" />
      <UtilityPanel />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from "vue-router";
import { useChatStore, Chat } from "@/stores/chatStore";
import ChatItem from "@/components/Chat/Sidebar/ChatItem.vue";
import NewChatItem from "@/components/Chat/Sidebar/NewChatItem.vue";
import UtilityPanel from "@/components/Chat/Sidebar/UtilityPanel.vue";

const router = useRouter();
const chatStore = useChatStore();

const selectChat = (chat: Chat) => {
  router.push({ name: "chat", params: { chat_id: chat.id } });
  chatStore.setCurrentChat(chat);
};

const startNewChat = () => {
  router.push({ name: "new-chat" });
  chatStore.startNewChat();
};
</script>
