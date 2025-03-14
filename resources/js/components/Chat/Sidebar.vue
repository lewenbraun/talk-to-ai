<template>
  <div class="w-1/4 bg-white border-r border-gray-300">
    <header
      class="p-4 border-b border-gray-300 flex justify-between items-center bg-indigo-600 text-white"
    >
      <h1 class="text-2xl font-semibold">Ai chat</h1>
      <div class="relative">
        <button
          @click="openSetting = !openSetting"
          class="focus:outline-none cursor-pointer flex self-center"
        >
          <VueFeather type="settings" />
        </button>
        <AiServiceList :openListLLMs="openListLLMs" />
      </div>
    </header>
    <div class="overflow-y-auto h-full p-3 mb-9 pb-20">
      <ChatNewItem icon="plus" @click="startNewChat()" />
      <hr class="my-2" />
      <ChatItem
        v-for="(chat, index) in chatStore.chats"
        :chat="chat"
        :key="index"
        @click="selectChat(chat)"
      />
    </div>
  </div>
  <SettingAiService
    v-model:openSetting="openSetting"
    @confirm="handleConfirm"
  />
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useChatStore, Chat } from "@/stores/chatStore";
import AiServiceList from "@/components/Chat/AiServiceList.vue";
import ChatItem from "@/components/Chat/ChatItem.vue";
import ChatNewItem from "@/components/Chat/ChatNewItem.vue";
import SettingAiService from "@/components/Setting/SettingAiService.vue";
import VueFeather from "vue-feather";

const router = useRouter();
const chatStore = useChatStore();

const openListLLMs = ref(false);
const openSetting = ref(false);

const selectChat = (chat: Chat) => {
  router.push({ name: "chat", params: { chat_id: chat.id } });
  chatStore.setCurrentChat(chat);
};

const handleConfirm = () => {
  openSetting.value = false;
};

const startNewChat = () => {
  router.push({ name: "new-chat" });
  chatStore.startNewChat();
};
</script>
