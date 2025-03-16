<template>
  <div
    class="flex items-center mb-4 hover:bg-gray-100 p-2 rounded-md"
    :class="{
      'cursor-pointer': !isRouteNewChat,
      'select-none bg-gray-100 text-gray-400': isRouteNewChat,
    }"
  >
    <VueFeather
      class="mr-4"
      :type="icon ?? 'align-left'"
      size="26"
      stroke-width="1"
    />
    <p>New chat</p>
  </div>
</template>

<script setup lang="ts">
import { defineProps, computed } from "vue";
import { useChatStore } from "@/stores/chatStore";
import VueFeather from "vue-feather";

defineProps<{
  icon?: string;
}>();

const chatStore = useChatStore();
const isRouteNewChat = computed(() => chatStore.isNewChatMode);
</script>
