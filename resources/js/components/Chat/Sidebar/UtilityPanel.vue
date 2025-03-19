<template>
  <div class="w-full h-[60px] flex items-center justify-between">
    <div class="relative ml-2" v-if="!isTemporaryUser">
      <button
        @click="showLogoutPopup = !showLogoutPopup"
        class="focus:outline-none cursor-pointer flex self-center hover:bg-gray-100 p-2 rounded-full"
      >
        <VueFeather stroke="gray" type="user" size="26" />
      </button>
      <div
        v-if="showLogoutPopup"
        class="absolute bottom-full left-4 mt-2 bg-white border border-gray-200 rounded-md shadow-md"
      >
        <button
          @click="handleLogout"
          class="block cursor-pointer text-left px-4 py-2 text-gray-900 hover:bg-gray-100 focus:outline-none whitespace-nowrap"
        >
          Log out
        </button>
      </div>
    </div>

    <div class="ml-2" v-else>
      <button
        class="cursor-pointer rounded-md font-semibold text-gray-900 hover:bg-gray-50 px-2 py-2"
        @click="router.push('/login')"
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
import { useRouter } from "vue-router";
import SettingAiService from "@/components/Setting/SettingAiService.vue";
import VueFeather from "vue-feather";

const userStore = useUserStore();
const isTemporaryUser = computed(() => userStore.isTemporaryUser());
const router = useRouter();

const openSetting = ref(false);

const handleClose = () => {
  openSetting.value = false;
};

const showLogoutPopup = ref(false);

const handleLogout = () => {
  userStore.logout();
  showLogoutPopup.value = false;
};
</script>

<style scoped></style>
