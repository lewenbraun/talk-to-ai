<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
      <div class="bg-white shadow-xl rounded-lg w-full max-w-md">
        <!-- Header Section -->
        <div class="px-6 py-4 border-b">
          <h4 class="text-xl font-semibold text-center mb-2">Login</h4>
        </div>
        <hr />

        <!-- Form Section -->
        <form @submit.prevent="login" class="px-6 py-4">
          <!-- Email Input -->
          <div class="mb-4">
            <label for="email" class="block text-gray-700 mb-1">Email</label>
            <div class="flex items-center border border-gray-300 rounded-md">
              <span class="px-3 text-gray-500">
                <!-- Replace with your preferred icon library -->
                <i class="fas fa-envelope"></i>
              </span>
              <input
                id="email"
                v-model="user.email"
                type="email"
                placeholder="Email"
                class="w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-orange-600 rounded-r-md"
              />
            </div>
          </div>

          <!-- Password Input -->
          <div class="mb-4">
            <label for="password" class="block text-gray-700 mb-1">Password</label>
            <div class="flex items-center border border-gray-300 rounded-md">
              <span class="px-3 text-gray-500">
                <!-- Replace with your preferred icon library -->
                <i class="fas fa-lock"></i>
              </span>
              <input
                id="password"
                v-model="user.password"
                type="password"
                placeholder="Password"
                class="w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-orange-600 rounded-r-md"
              />
            </div>
          </div>

          <!-- Sign In Button -->
          <button
            type="submit"
            class="w-full bg-orange-600 text-white py-2 rounded-md hover:bg-orange-700 transition-colors"
          >
            Sign in
          </button>
        </form>

        <!-- Footer / Sign Up Link -->
        <div class="text-center py-4 border-t">
          <router-link to="/register" class="text-blue-500 hover:underline">
            Sign up
          </router-link>
        </div>
      </div>
    </div>
  </template>

  <script setup lang="ts">
  import { ref } from 'vue';
  import { useRouter } from 'vue-router';
  import { useUserStore } from '../../stores/userStore';

  const user = ref({
    email: '',
    password: '',
  });

  const router = useRouter();
  const userStore = useUserStore();

  function login() {
    userStore.login(user.value).then(() => {
      router.push({ name: 'profile' });
    });
  }
  </script>
