<template>
  <div class="col-span-2 row-start-2 flex flex-col gap-2">
    <div class="flex flex-row items-center pl-2">
      <span>{{ props.aiService.name }}</span>
      <div class="ml-auto flex flex-row gap-1.5">
        <div>
          <button
            class="flex cursor-pointer items-center rounded-2xl px-0.5 py-0.5 hover:bg-gray-200"
            @click="updateLLMList()"
          >
            <VueFeather v-if="!isLoadingLLMList" type="refresh-cw" size="14" />
            <VueFeather
              v-else
              animation="spin"
              animation-speed="Fast"
              type="refresh-cw"
              size="14"
            />
          </button>
        </div>
        <div class="relative">
          <button
            class="flex cursor-pointer items-center rounded-2xl px-0.5 py-0.5 hover:bg-gray-200"
            @click="openFormAddLLM = !openFormAddLLM"
          >
            <VueFeather type="plus" size="14" />
          </button>
          <FormAddLLM
            :aiServiceId="props.aiService.id"
            :openFormAddLLM="openFormAddLLM"
            @update:openFormAddLLM="(value) => (openFormAddLLM = value)"
          />
        </div>
      </div>
    </div>

    <hr class="mt-1 border-gray-200" />
    <LLMItem
      v-for="llm in props.aiService.llms"
      :aiServiceId="props.aiService.id"
      :llm="llm"
      :key="llm.id"
    />
  </div>
</template>

<script setup lang="ts">
import { defineProps, ref } from "vue";
import { useAiServiceStore } from "@/stores/aiServiceStore";
import VueFeather from "vue-feather";
import FormAddLLM from "@/components/Setting/FormAddLLM.vue";
import LLMItem from "@/components/Setting/LLMItem.vue";
import type { LLM } from "@/types/aiService";

const openFormAddLLM = ref(false);

const aiServiceStore = useAiServiceStore();

let isLoadingLLMList = ref(false);
const updateLLMList = async () => {
  isLoadingLLMList.value = true;
  await aiServiceStore.loadAiLLMListByAiServiceId(props.aiService.id);
  isLoadingLLMList.value = false;
};

const props = defineProps<{
  aiService: {
    id: number;
    name: string;
    url_api: string;
    api_key?: string;
    llms: LLM[];
  };
}>();
</script>

<style scoped></style>
