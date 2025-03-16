<template>
  <div class="w-full h-[60px] flex items-center justify-between">
    <div class="relative ml-2" v-if="userAuth">
      <button
        @click="openSetting = !openSetting"
        class="focus:outline-none cursor-pointer flex self-center hover:bg-gray-100 p-2 rounded-full"
      >
        <VueFeather stroke="gray" type="user" size="26" />
      </button>
    </div>
    <div class="ml-2" v-else>
      <button
        class="cursor-pointer rounded-md font-semibold text-gray-900 hover:bg-gray-50 px-2 py-2"
      >
        Sign in
      </button>
    </div>
    <div class="relative mr-2">
      <button
        @click="openSetting = !openSetting"
        class="focus:outline-none cursor-pointer flex self-center hover:bg-gray-100 p-2 rounded-full"
      >
        <VueFeather stroke="gray" type="settings" size="26" />
      </button>
    </div>
  </div>
  <SettingAiService v-model:openSetting="openSetting" @confirm="handleClose" />
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
import { useUserStore } from "@/stores/userStore";
import SettingAiService from "@/components/Setting/SettingAiService.vue";
import VueFeather from "vue-feather";

const userStore = useUserStore();
const userAuth = computed(() => userStore.userAuth());

const openSetting = ref(false);

const handleClose = () => {
  openSetting.value = false;
};
</script>

<style scoped></style>
