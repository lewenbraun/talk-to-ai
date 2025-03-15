<template>
  <footer class="p-4 flex justify-center">
    <div class="flex items-center w-[750px]">
      <input
        v-model="newMessage"
        type="text"
        placeholder="Enter your message..."
        class="w-full p-2 border border-gray-300 focus:outline-none focus:border-gray-500 rounded-md bg-white text-gray-900 shadow-xs ring-gray-300 ring-inset"
        @keyup.enter="sendMessage"
      />
      <button
        @click="sendMessage"
        class="rounded-md cursor-pointer bg-white font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 px-4 py-2 ml-2"
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

const sendMessage = () => {
  if (newMessage.value.trim() !== "") {
    if (
      (chatStore.isNewChatMode && aiServiceStore.currentLLM) ||
      !chatStore.isNewChatMode
    ) {
      chatStore.messageSendingProcess(newMessage.value.trim());
      newMessage.value = "";
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
  }
};
</script>
