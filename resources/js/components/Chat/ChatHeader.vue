<template>
  <header class="bg-white p-4 border-b border-gray-300">
    <div class="flex flex-row">
      <h1 class="text-2xl font-semibold">
        {{ chatStore.currentChat ? chatStore.currentChat.name : "New chat" }}
      </h1>
      <div class="flex ml-2 items-end relative">
        <p
          class="text-gray-600 cursor-pointer max-w-20"
          @click="openListLLMs = !openListLLMs"
        >
          Change llm
        </p>
        <AiServiceList :llmList="llmList" :openListLLMs="openListLLMs" />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useChatStore } from "@/stores/chatStore";
import { LLM } from "@/stores/aiServiceStore";
import AiServiceList from "@/components/Chat/AiServiceList.vue";

const openListLLMs = ref(false);
const chatStore = useChatStore();
const llmList = ref([] as LLM[]);
</script>
