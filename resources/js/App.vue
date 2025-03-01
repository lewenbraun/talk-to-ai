<template>
  <router-view />
</template>

<script setup lang="ts">
import { defineOptions } from "vue";
import { useUserStore } from "@/stores/userStore";

const userStore = useUserStore();

if (!userStore.userAuth()) {
  userStore.createTemporaryUser();
}

defineOptions({
  name: "App",
});
</script>

<style></style>
