<template>
  <header class="bg-white p-4 border-b border-gray-300">
    <div class="flex flex-row">
      <h1 class="text-2xl font-semibold">
        {{ currentModel }}
      </h1>
      <div class="flex ml-2 items-end relative">
        <p
          v-if="isNewChatMode"
          class="text-gray-600 cursor-pointer max-w-20"
          @click.stop="openListLLMs = !openListLLMs"
        >
          Change llm
        </p>
        <AiServiceList
          :aiServices="aiServiceStore.aiServices"
          :openListLLMs="openListLLMs"
          @update:openListLLMs="(value) => (openListLLMs = value)"
        />
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { useChatStore } from "@/stores/chatStore";
import { useAiServiceStore } from "@/stores/aiServiceStore";
import AiServiceList from "@/components/Chat/AiServiceList.vue";

const openListLLMs = ref(false);
const chatStore = useChatStore();
const aiServiceStore = useAiServiceStore();

const isNewChatMode = computed(() => {
  return chatStore.isNewChatMode;
});

const currentModel = computed(() => {
  if (isNewChatMode.value) {
    return aiServiceStore.currentLLM
      ? aiServiceStore.currentLLM.name
      : "Select LLM";
  } else if (chatStore.currentChat) {
    return chatStore.currentChat.llm.name;
  }
});
</script>
