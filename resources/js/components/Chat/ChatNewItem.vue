<template>
  <div
    class="flex items-center mb-4 hover:bg-gray-100 p-2 rounded-md"
    :class="{
      'cursor-pointer': !isRouteNewChat,
      'select-none bg-gray-100 text-gray-400': isRouteNewChat,
    }"
  >
    <VueFeather
      class="mr-4"
      :type="icon ?? 'align-left'"
      size="26"
      stroke-width="1"
    />
    <p>New chat</p>
  </div>
</template>

<script setup lang="ts">
import { defineProps, defineEmits, computed } from "vue";
import { useRoute } from "vue-router";
import VueFeather from "vue-feather";

defineProps<{
  icon?: string;
}>();

const route = useRoute();
const isRouteNewChat = computed(() => route.path === "/new-chat");
</script>
