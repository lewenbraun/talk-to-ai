<template>
  <div :class="messageClass">
    <div
      v-if="isIncoming"
      class="w-9 h-9 rounded-full flex items-center justify-center mr-2"
    >
      <img
        :src="incomingAvatar"
        alt="User Avatar"
        class="w-8 h-8 rounded-full"
      />
    </div>
    <div class="flex max-w-96 p-3 rounded-lg" :class="bubbleClass">
      <p>{{ message.content }}</p>
    </div>
    <div
      v-if="!isIncoming"
      class="w-9 h-9 rounded-full flex items-center justify-center ml-2"
    >
      <img :src="outgoingAvatar" alt="My Avatar" class="w-8 h-8 rounded-full" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, defineProps } from "vue";

interface Message {
  id: number;
  user: string;
  content: string;
  timestamp: Date;
  type: "incoming" | "outgoing";
}

const props = defineProps<{ message: Message }>();
const isIncoming = computed(() => props.message.type === "incoming");
const messageClass = computed(() =>
  isIncoming.value ? "flex mb-4" : "flex justify-end mb-4"
);
const bubbleClass = computed(() =>
  isIncoming.value ? "bg-white text-gray-700" : "bg-indigo-500 text-white"
);

const incomingAvatar =
  "https://placehold.co/200x/ffa8e4/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato";
const outgoingAvatar =
  "https://placehold.co/200x/b7a8ff/ffffff.svg?text=ʕ•́ᴥ•̀ʔ&font=Lato";
</script>
