<template>
  <div class="chat-window flex-1 overflow-hidden">
    <div
      ref="messagesContainer"
      class="messages-container flex-1 overflow-y-auto p-4 min-h-0"
      @scroll="onScroll"
    >
      <ChatMessage
        v-for="(message, index) in chatStore.currentMessages"
        :key="index"
        :message="message"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, nextTick, watch } from "vue";
import { useChatStore } from "@/stores/chatStore";
import ChatMessage from "@/components/Chat/ChatMessage.vue";

const chatStore = useChatStore();
const messagesContainer = ref<HTMLDivElement | null>(null);

watch(
  () => chatStore.currentMessages.length,
  async () => {
    await nextTick();
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  }
);

const onScroll = () => {
  if (messagesContainer.value && messagesContainer.value.scrollTop === 0) {
    chatStore.loadMoreMessages();
  }
};
</script>

<style scoped>
.chat-window {
  display: flex;
  flex-direction: column;
  height: 400px;
  border: 1px solid #ccc;
}

.messages-container {
  background-color: #f9f9f9;
  padding: 10px;
}
</style>
