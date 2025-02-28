<template>
  <div class="flex-1 overflow-hidden">
    <div
      ref="messagesContainer"
      class="flex-1 overflow-y-auto p-4 min-h-0"
      @scroll="onScroll"
    >
      <ChatMessage
        v-for="message in chatStore.currentMessages"
        :key="message.id"
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
