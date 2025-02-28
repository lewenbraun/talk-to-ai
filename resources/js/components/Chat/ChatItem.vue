<template>
  <div
    @click="select"
    class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md"
  >
    <VueFeather class="mr-4" :type="icon ?? 'align-left'" size="26" stroke-width="1" />
    <p>{{ chat.name }}</p>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from "vue";
import type { Chat } from "@/stores/chatStore.ts";
import VueFeather from "vue-feather";

const props = defineProps<{
  chat: Chat;
  icon?: string;
}>();

const emit = defineEmits<{ (e: "select", chat: Chat): void }>();

const select = () => {
  emit("select", props.chat);
};
</script>
