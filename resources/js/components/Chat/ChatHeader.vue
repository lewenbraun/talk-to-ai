<template>
  <header class="bg-white p-4 border-b border-gray-300">
    <h1 class="text-2xl font-semibold">{{ currentContact?.name || 'Select contact' }}</h1>
  </header>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useChatStore } from '@/stores/chatStore'

const store = useChatStore();
const currentContact = computed(() => store.currentContact)
</script>
