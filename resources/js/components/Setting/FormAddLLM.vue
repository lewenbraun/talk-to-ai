<template>
  <div
    v-if="openFormAddLLM"
    class="absolute right-0 top-full mt-1 w-72 bg-white border border-gray-300 rounded-md shadow-lg"
  >
    <div class="sm:col-span-3 px-3 py-3">
      <label for="first-name" class="block text-sm/6 font-medium text-gray-900"
        >Name model</label
      >
      <div class="mt-2 flex flex-row">
        <input
          v-model="nameModel"
          type="text"
          placeholder="deepseek-r1:1.5b"
          class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
        />
        <button
          class="flex items-center cursor-pointer rounded-2xl ml-2 px-0.5 py-0.5"
          @click="addLLM"
        >
          <VueFeather type="check" size="18" />
        </button>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineProps, ref } from "vue";
import { useAiServiceStore } from "@/stores/aiServiceStore";
import { toast } from "vue3-toastify";
import VueFeather from "vue-feather";

const aiServiceStore = useAiServiceStore();

const nameModel = ref("");

const emit = defineEmits<{
  (emit: "closeFormAddLLM"): void;
}>();

const addLLM = async () => {
  emit("closeFormAddLLM");

  let addedLLM = await aiServiceStore.addLLM(
    props.aiServiceId,
    nameModel.value
  );
  if (addedLLM) {
    toast(`${addedLLM.name} loading started`, {
      theme: "colored",
      type: "success",
      position: "top-left",
      autoClose: 1000,
      pauseOnHover: false,
      hideProgressBar: true,
    });
  }

  nameModel.value = "";
};
const props = defineProps<{
  openFormAddLLM: boolean;
  aiServiceId: number;
}>();
</script>
