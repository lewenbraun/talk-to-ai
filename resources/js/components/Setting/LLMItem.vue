<template>
  <div class="ml-4 flex flex-row items-center">
    <span>{{ llm.name }}</span>
    <button
      class="ml-auto flex cursor-pointer items-center rounded-2xl px-0.5 py-0.5 hover:bg-gray-200"
      v-if="llm.isLoaded"
    >
      <VueFeather type="trash" size="14" @click="deleteLLM" />
    </button>
    <VueFeather
      v-else
      class="ml-auto flex items-center rounded-2xl px-0.5 py-0.5"
      type="loader"
      animation="spin"
      animation-speed="Normal"
      size="14"
    />
  </div>
</template>

<script setup lang="ts">
import { useAiServiceStore } from "@/stores/aiServiceStore";
import { toast } from "vue3-toastify";
import VueFeather from "vue-feather";

const aiServiceStore = useAiServiceStore();

const deleteLLM = async () => {
  const response = await aiServiceStore.deleteLLM(
    props.aiServiceId,
    props.llm.id
  );

  if (response) {
    toast(`${props.llm.name} deleted`, {
      theme: "colored",
      type: "success",
      position: "top-left",
      autoClose: 1000,
      pauseOnHover: false,
      hideProgressBar: true,
    });
  }
};

const props = defineProps<{
  aiServiceId: number;
  llm: {
    id: number;
    name: string;
    api_key?: string;
    isLoaded: boolean;
  };
}>();
</script>

<style scoped></style>
