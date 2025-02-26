<template>
  <div class="flex-1 relative">
    <ChatHeader />
    <ChatMessages />
    <ChatInput />
  </div>
</template>

<script setup lang="ts">
import ChatHeader from '@/components/Chat/ChatHeader.vue'
import ChatMessages from '@/components/Chat/ChatMessages.vue'
import ChatInput from '@/components/Chat/ChatInput.vue'
</script>
