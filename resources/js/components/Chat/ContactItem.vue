<template>
  <div
    @click="select"
    class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md"
  >
    <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
      <img
        :src="contact.avatar"
        alt="User Avatar"
        class="w-12 h-12 rounded-full"
      />
    </div>
    <div class="flex-1">
      <h2 class="text-lg font-semibold">{{ contact.name }}</h2>
      <p class="text-gray-600">{{ contact.lastMessage }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits } from "vue";

const props = defineProps<{
  contact: {
    id: number;
    name: string;
    avatar: string;
    lastMessage: string;
  };
}>();

const emit = defineEmits<{ (e: "select", contact: any): void }>();

const select = () => {
  emit("select", props.contact);
};
</script>
