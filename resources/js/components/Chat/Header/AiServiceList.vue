<template>
  <div
    ref="dropdownRef"
    v-if="openListLLMs"
    class="absolute left-0 top-full mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg"
  >
    <ul class="py-1 px-2" v-for="aiService in aiServices" :key="aiService.id">
      <li>
        <span class="block font-medium px-2 pt-1 text-gray-800 select-none">
          {{ aiService.name }}:
        </span>
      </li>
      <ul class="px-2" v-for="llm in aiService.llms" :key="llm.id">
        <li>
          <button
            class="block px-4 py-1 text-gray-800 hover:text-gray-400 cursor-pointer"
            @click="selectLLM(llm)"
          >
            {{ llm.name }}
          </button>
        </li>
      </ul>
    </ul>
  </div>
</template>

<script lang="ts" setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useAiServiceStore } from "@/stores/aiServiceStore";
import { defineProps, defineEmits } from "vue";
import type { AiService } from "@/types/aiService";

const aiServiceStore = useAiServiceStore();

const dropdownRef = ref<HTMLElement | null>(null);

const emit = defineEmits<{
  (e: "update:openListLLMs", value: boolean): void;
}>();

defineProps<{
  openListLLMs: boolean;
  aiServices: AiService[];
}>();

const selectLLM = (llm: any) => {
  aiServiceStore.currentLLM = llm;
  emit("update:openListLLMs", false);
};

const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    emit("update:openListLLMs", false);
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>
