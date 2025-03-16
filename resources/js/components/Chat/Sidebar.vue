<template>
  <div class="w-[400px] bg-white border-r border-gray-300">
    <div class="overflow-y-auto max-h-[calc(100vh-60px)] p-3">
      <ChatNewItem icon="plus" @click="startNewChat()" />
      <hr class="my-2 border-gray-400" />
      <ChatItem
        v-for="(chat, index) in chatStore.chats"
        :chat="chat"
        :key="index"
        @click="selectChat(chat)"
      />
    </div>
    <hr class="border-gray-300" />
    <div class="w-full h-[60px] flex items-center justify-between">
      <div class="relative ml-2">
        <button
          @click="openSetting = !openSetting"
          class="focus:outline-none cursor-pointer flex self-center hover:bg-gray-100 p-2 rounded-full"
        >
          <VueFeather stroke="gray" type="user" size="26" />
        </button>
      </div>
      <div class="relative mr-2">
        <button
          @click="openSetting = !openSetting"
          class="focus:outline-none cursor-pointer flex self-center hover:bg-gray-100 p-2 rounded-full"
        >
          <VueFeather stroke="gray" type="settings" size="26" />
        </button>
      </div>
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
import ChatItem from "@/components/Chat/ChatItem.vue";
import ChatNewItem from "@/components/Chat/ChatNewItem.vue";
import SettingAiService from "@/components/Setting/SettingAiService.vue";
import VueFeather from "vue-feather";

const router = useRouter();
const chatStore = useChatStore();

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
