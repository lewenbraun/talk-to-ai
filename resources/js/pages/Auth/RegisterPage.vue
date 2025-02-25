<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
      <div class="bg-white shadow-xl rounded-lg w-full max-w-md">
        <!-- Header Section -->
        <div class="px-6 py-4 border-b">
          <h4 class="text-xl font-semibold text-center mb-2">Registration</h4>
        </div>
        <hr />

        <!-- Form Section -->
        <form @submit.prevent="register" class="px-6 py-4 space-y-4">
          <!-- Name Input -->
          <div>
            <label for="name" class="block text-gray-700 mb-1">Name</label>
            <div class="flex items-center border border-gray-300 rounded-md">
              <span class="px-3 text-gray-500">
                <!-- Replace with your preferred icon library -->
                <i class="fas fa-user"></i>
              </span>
              <input
                id="name"
                v-model="user.name"
                type="text"
                placeholder="Name"
                class="w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-orange-600 rounded-r-md"
              />
            </div>
          </div>

          <!-- Nickname Input -->
          <div>
            <label for="nickname" class="block text-gray-700 mb-1">Nickname</label>
            <div class="flex items-center border border-gray-300 rounded-md">
              <span class="px-3 text-gray-500">
                <i class="fas fa-user"></i>
              </span>
              <input
                id="nickname"
                v-model="user.nickname"
                type="text"
                placeholder="Nickname"
                class="w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-orange-600 rounded-r-md"
              />
            </div>
          </div>

          <!-- Email Input -->
          <div>
            <label for="email" class="block text-gray-700 mb-1">Email</label>
            <div class="flex items-center border border-gray-300 rounded-md">
              <span class="px-3 text-gray-500">
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
          <div>
            <label for="password" class="block text-gray-700 mb-1">Password</label>
            <div class="flex items-center border border-gray-300 rounded-md">
              <span class="px-3 text-gray-500">
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

          <!-- Repeat Password Input -->
          <div>
            <label for="password_confirmation" class="block text-gray-700 mb-1">Repeat password</label>
            <div class="flex items-center border border-gray-300 rounded-md">
              <span class="px-3 text-gray-500">
                <i class="fas fa-lock"></i>
              </span>
              <input
                id="password_confirmation"
                v-model="user.password_confirmation"
                type="password"
                placeholder="Repeat password"
                class="w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-orange-600 rounded-r-md"
              />
            </div>
          </div>

          <!-- Sign Up Button -->
          <button
            type="submit"
            class="w-full bg-orange-600 text-white py-2 rounded-md hover:bg-orange-700 transition-colors"
          >
            Sign up
          </button>
        </form>

        <!-- Footer Section -->
        <div class="text-center py-4 border-t">
          <p class="text-gray-600 mb-2">Already registered?</p>
          <router-link to="/login" class="text-blue-500 hover:underline">
            Sign in
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
    name: '',
    nickname: '',
    email: '',
    password: '',
    password_confirmation: '',
  });

  const router = useRouter();
  const userStore = useUserStore();

  function register() {
    userStore.register(user.value).then(() => {
      router.push({ name: 'main' });
    });
  }
  </script>
