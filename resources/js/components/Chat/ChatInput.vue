<template>
  <footer class="bg-white border-t border-gray-300 p-4">
    <div class="flex items-center">
      <input
        v-model="newMessage"
        type="text"
        placeholder="Enter your message..."
        class="w-full p-2 rounded-md border border-gray-400 focus:outline-none focus:border-blue-500"
        @keyup.enter="send"
      />
      <button
        @click="send"
        class="bg-indigo-500 text-white px-4 py-2 rounded-md ml-2"
      >
        Send
      </button>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useChatStore } from "@/stores/chatStore";

const newMessage = ref("");
const chatStore = useChatStore();

const send = () => {
  if (newMessage.value.trim() !== "") {
    chatStore.sendMessage(newMessage.value.trim());
    newMessage.value = "";
  }
};
</script>
