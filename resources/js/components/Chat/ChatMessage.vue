<template>
  <div class="flex items-center" :class="messageClass">
    <VueFeather
      v-if="message.content === ''"
      type="loader"
      animation="spin"
      animation-speed="Normal"
    />

    <div v-else class="flex max-w-258 p-3 rounded-lg" :class="bubbleClass">
      <vue-markdown :source="message.content" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, defineProps } from "vue";
import VueFeather from "vue-feather";
import VueMarkdown from "vue-markdown-render";
import type { Message } from "@/types/chat";

const props = defineProps<{ message: Message }>();
const isIncoming = computed(() => props.message.type === "incoming");
const messageClass = computed(() =>
  isIncoming.value ? "flex mb-4" : "flex justify-end mb-4"
);
const bubbleClass = computed(() =>
  isIncoming.value ? "bg-white text-gray-700" : "bg-indigo-500 text-white"
);
</script>
