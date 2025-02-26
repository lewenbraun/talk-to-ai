<template>
  <div ref="messagesContainer" class="h-full overflow-y-auto p-4 pb-36" @scroll="onScroll">
    <ChatMessage
      v-for="message in messages"
      :key="message.id"
      :message="message"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, watch, nextTick } from 'vue'
import { useChatStore } from '@/stores/chatStore'
import ChatMessage from './ChatMessage.vue'

const store = useChatStore();
const messages = store.messages;

const messagesContainer = ref<HTMLDivElement | null>(null);

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
  }
}

watch(() => store.messages.length, async () => {
  await nextTick();
  scrollToBottom();
});

const onScroll = () => {
  if (messagesContainer.value && messagesContainer.value.scrollTop === 0) {
    store.loadMoreMessages();
  }
}
</script>
