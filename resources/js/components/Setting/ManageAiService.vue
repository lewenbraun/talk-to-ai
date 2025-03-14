<template>
  <div>
    <AiServiceItem
      v-for="aiService in aiServices"
      :aiService="aiService"
      :key="aiService.id"
    />
  </div>
</template>

<script setup lang="ts">
import AiServiceItem from "@/components/Setting/AiServiceItem.vue";
import { useAiServiceStore } from "@/stores/aiServiceStore";

const aiServiceStore = useAiServiceStore();
const aiServices = aiServiceStore.aiServices;
</script>

<style scoped>
details > summary {
  list-style-type: "";
}
</style>
