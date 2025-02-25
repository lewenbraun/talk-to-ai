<template>
    <div class="min-h-screen flex flex-col">
      <!-- Header -->
      <header class="bg-orange-600 shadow-md">
        <div class="container mx-auto flex items-center justify-between py-4 px-6">
          <h1
            class="text-white text-xl font-bold cursor-pointer"
            @click="$router.push({ name: 'main' })"
          >
            Auth
          </h1>
          <div class="flex space-x-4">
            <router-link
              :to="{ name: 'login' }"
              class="text-white hover:text-gray-200"
            >
              Sign in
            </router-link>
            <router-link
              :to="{ name: 'register' }"
              class="text-white hover:text-gray-200"
            >
              Sign up
            </router-link>
          </div>
        </div>
      </header>

      <!-- Page Container -->
      <main class="flex-grow container mx-auto p-6">
        <router-view />
      </main>
    </div>
  </template>

  <script setup lang="ts">
  </script>
