<template>
  <div class="col-span-2 row-start-2 flex flex-col gap-2">
    <LLMGroup
      v-for="aiService in aiServices"
      :aiService="aiService"
      :key="aiService.id"
    />
  </div>
</template>

<script setup lang="ts">
import { useAiServiceStore } from "@/stores/aiServiceStore";
import LLMGroup from "@/components/Setting/LLMGroup.vue";

const aiServiceStore = useAiServiceStore();
const aiServices = aiServiceStore.aiServices;
</script>

<style scoped></style>
