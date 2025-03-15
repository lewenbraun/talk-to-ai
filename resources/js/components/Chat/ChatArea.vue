<template>
  <div class="flex flex-col h-full w-full bg-gray-50">
    <ChatHeader />
    <ChatMessages />
    <ChatInput />
  </div>
</template>

<script setup lang="ts">
import ChatHeader from "@/components/Chat/ChatHeader.vue";
import ChatMessages from "@/components/Chat/ChatMessages.vue";
import ChatInput from "@/components/Chat/ChatInput.vue";
</script>
