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
import { useAiServiceStore } from "@/stores/aiServiceStore";
import { toast } from "vue3-toastify";

const newMessage = ref("");
const chatStore = useChatStore();
const aiServiceStore = useAiServiceStore();

const send = () => {
  if (chatStore.isNewChatMode && aiServiceStore.currentLLM) {
    if (newMessage.value.trim() !== "") {
      chatStore.sendMessage(newMessage.value.trim(), aiServiceStore.currentLLM);
      newMessage.value = "";
    }
  } else {
    toast("First choose llm", {
      theme: "colored",
      type: "error",
      position: "top-left",
      autoClose: 1500,
      pauseOnHover: false,
      hideProgressBar: true,
    });
  }
};
</script>
