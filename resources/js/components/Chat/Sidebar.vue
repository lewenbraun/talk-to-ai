<template>
  <div class="w-1/4 bg-white border-r border-gray-300">
    <header
      class="p-4 border-b border-gray-300 flex justify-between items-center bg-indigo-600 text-white"
    >
      <h1 class="text-2xl font-semibold">Chat Web</h1>
      <div class="relative">
        <button
          @click="menuOpen = !menuOpen"
          class="focus:outline-none cursor-pointer flex self-center"
        >
          <vue-feather type="settings"></vue-feather>
        </button>
        <div
          v-if="menuOpen"
          class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg"
        >
          <ul class="py-2 px-3">
            <li>
              <a
                href="#"
                class="block px-4 py-2 text-gray-800 hover:text-gray-400"
                >Option 1</a
              >
            </li>
            <li>
              <a
                href="#"
                class="block px-4 py-2 text-gray-800 hover:text-gray-400"
                >Option 2</a
              >
            </li>
          </ul>
        </div>
      </div>
    </header>
    <div class="overflow-y-auto h-full p-3 mb-9 pb-20">
      <div
        v-for="contact in chatStore.contacts"
        :key="contact.id"
        @click="selectContact(contact)"
        class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md"
      >
        <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
          <img
            :src="contact.avatar"
            alt="User Avatar"
            class="w-12 h-12 rounded-full"
          />
        </div>
        <div class="flex-1">
          <h2 class="text-lg font-semibold">{{ contact.name }}</h2>
          <p class="text-gray-600">{{ contact.lastMessage }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useChatStore } from "@/stores/chatStore";
import VueFeather from "vue-feather";

const chatStore = useChatStore();
const menuOpen = ref(false);

const selectContact = (contact: any) => {
  chatStore.setCurrentContact(contact);
};
</script>
