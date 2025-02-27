<template>
  <header class="bg-white p-4 border-b border-gray-300">
    <h1 class="text-2xl font-semibold">
      {{
        chatStore.currentContact
          ? chatStore.currentContact.name
          : "Select contact"
      }}
    </h1>
  </header>
</template>

<script setup lang="ts">
import { useChatStore } from "@/stores/chatStore";

const chatStore = useChatStore();
</script>
