<template>
  <div class="col-span-2 row-start-2 ml-2">
    <details class="flex flex-col justify-start">
      <summary class="cursor-pointer flex flex-row items-center">
        <div class="mr-4">{{ props.aiService.name }}</div>
        <VueFeather type="edit-2" size="14" />
      </summary>
      <div class="text-sm mt-1 leading-6 text-gray-600">
        <label
          for="first-name"
          class="block text-sm/6 font-medium text-gray-900"
          >Url api</label
        >
        <div class="mt-1 flex flex-row">
          <input
            v-model="url_api"
            type="text"
            placeholder="localhost:11434"
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6"
          />
          <button
            class="flex items-center cursor-pointer rounded-2xl ml-2 px-0.5 py-0.5"
            @click="updateUrl"
          >
            <VueFeather type="check" size="18" />
          </button>
        </div>
      </div>
    </details>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { defineProps } from "vue";
import { useAiServiceStore } from "@/stores/aiServiceStore";
import { toast } from "vue3-toastify";
import VueFeather from "vue-feather";

const props = defineProps<{
  aiService: {
    id: number;
    name: string;
    url_api: string;
    api_key?: string;
  };
}>();

const url_api = ref(props.aiService.url_api);

const aiServiceStore = useAiServiceStore();

const updateUrl = async () => {
  let response = await aiServiceStore.updateAiServiceUrl(
    props.aiService.id,
    url_api.value
  );
  if (response) {
    toast("Success", {
      theme: "colored",
      type: "success",
      position: "top-left",
      autoClose: 1000,
      pauseOnHover: false,
      hideProgressBar: true,
    });
  }
};
</script>

<style scoped>
details > summary {
  list-style-type: "";
}
</style>
