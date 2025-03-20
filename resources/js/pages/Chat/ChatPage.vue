<template>
  <div class="flex h-screen overflow-hidden">
    <Sidebar />
    <ChatArea />
  </div>
</template>

<script setup lang="ts">
import Sidebar from "@/components/Chat/Sidebar/Sidebar.vue";
import ChatArea from "@/components/Chat/ChatArea.vue";
</script>
