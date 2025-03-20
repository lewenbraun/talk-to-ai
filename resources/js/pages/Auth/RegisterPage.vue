<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-xl rounded-lg w-full max-w-md">
      <!-- Header Section -->
      <div class="px-6 py-4">
        <h4 class="text-xl font-semibold text-center mb-2">Registration</h4>
      </div>
      <hr />

      <!-- Form Section -->
      <form @submit.prevent="register" class="px-6 py-4 space-y-4">
        <!-- Email Input -->
        <div>
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
        <div>
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

        <!-- Repeat Password Input -->
        <div>
          <div class="flex items-center border border-gray-300 rounded-md">
            <input
              id="password_confirmation"
              v-model="user.password_confirmation"
              type="password"
              placeholder="Confirm password"
              class="w-full py-2 px-3 focus:ring-0.5 focus:ring-gray-200 rounded-md"
            />
          </div>
        </div>

        <!-- Sign Up Button -->
        <button
          type="submit"
          class="w-full cursor-pointer border-1 hover:text-white py-2 rounded-md hover:bg-gray-600 transition-colors"
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
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useUserStore } from "@/stores/userStore";
import type { RegisterData } from "@/types/user";

const user = ref<RegisterData>({
  email: "",
  password: "",
  password_confirmation: "",
});

const router = useRouter();
const userStore = useUserStore();

function register() {
  userStore.register(user.value).then(() => {
    router.push({ name: "main" });
  });
}
</script>
