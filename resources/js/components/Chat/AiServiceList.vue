<template>
  <div
    v-if="openListLLMs"
    class="absolute left-0 top-full mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg"
  >
    <ul class="py-1 px-2">
      <li>
        <span class="block font-medium px-2 pt-1 text-gray-800 select-none">
          Ollama:
        </span>
      </li>
      <ul class="px-2">
        <li>
          <button
            class="block px-4 py-1 text-gray-800 hover:text-gray-400 cursor-pointer"
          >
            deepseek-r1:1.5b
          </button>
        </li>
        <li>
          <button
            class="block px-4 py-1 text-gray-800 hover:text-gray-400 cursor-pointer"
          >
            llama3.2:1b
          </button>
        </li>
      </ul>
    </ul>
  </div>
</template>

<script lang="ts" setup>
import { LLM } from "@/stores/aiServiceStore";
import { defineProps } from "vue";

defineProps<{
  openListLLMs: boolean;
  llmList: LLM[];
}>();
</script>
