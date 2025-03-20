<template>
  <div
    @click="select"
    class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md"
    :class="{ 'bg-gray-100': props.chat.id === currentChat?.id }"
  >
    <VueFeather
      class="mr-4"
      :type="icon ?? 'align-left'"
      size="26"
      stroke-width="1"
    />
    <p>{{ chat.name }}</p>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, computed } from "vue";
import { useChatStore } from "@/stores/chatStore";
import VueFeather from "vue-feather";
import type { Chat } from "@/types/chat";

const props = defineProps<{
  chat: Chat;
  icon?: string;
}>();

const emit = defineEmits<{ (e: "select", chat: Chat): void }>();

const chatStore = useChatStore();
const currentChat = computed(() => chatStore.currentChat);

const select = () => {
  emit("select", props.chat);
};
</script>
