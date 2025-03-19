<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-xl rounded-lg w-full max-w-md">
      <!-- Header Section -->
      <div class="px-6 py-4">
        <h4 class="text-xl font-semibold text-center mb-2">Login</h4>
      </div>
      <hr class="border-gray-400" />

      <!-- Form Section -->
      <form @submit.prevent="login" class="px-6 py-4">
        <!-- Email Input -->
        <div class="mb-4">
          <div class="flex items-center border border-gray-300 rounded-md">
            <input
              id="email"
              v-model="user.email"
              type="email"
              placeholder="Email"
              class="w-full py-2 px-3 focus:ring-0.5 focus:ring-gray-200 rounded-md"
            />
          </div>
        </div>

        <!-- Password Input -->
        <div class="mb-4">
          <div class="flex items-center border border-gray-300 rounded-md">
            <input
              id="password"
              v-model="user.password"
              type="password"
              placeholder="Password"
              class="w-full py-2 px-3 focus:ring-0.5 focus:ring-gray-200 rounded-md"
            />
          </div>
        </div>

        <!-- Sign In Button -->
        <button
          type="submit"
          class="w-full cursor-pointer border-1 hover:text-white py-2 rounded-md hover:bg-gray-600 transition-colors"
        >
          Sign in
        </button>
      </form>
      <hr class="border-gray-400" />

      <!-- Footer / Sign Up Link -->
      <div class="text-center pt-3 pb-4">
        <router-link to="/register" class="text-blue-500 hover:underline">
          Sign up
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { LoginData, useUserStore } from "@/stores/userStore";

const user = ref<LoginData>({
  email: "",
  password: "",
});

const router = useRouter();
const userStore = useUserStore();

function login() {
  userStore.login(user.value).then(() => {
    router.push({ name: "main" });
  });
}
</script>
