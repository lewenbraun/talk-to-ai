<template>
  <transition name="modal-fade">
    <div
      v-if="openSetting"
      class="fixed inset-0 z-10 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex min-h-screen items-center justify-center p-4 text-center"
      >
        <div class="absolute inset-0 bg-gray-500/75" @click="closeModal" />

        <transition name="modal-panel">
          <div
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            @click.stop
          >
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <div class="sm:items-start">
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                  <div
                    class="grid grid-cols-[110px_auto_auto] grid-rows-[auto_200px] gap-4"
                  >
                    <div class="col-span-full">
                      <h3
                        class="text-base font-semibold text-gray-900"
                        id="modal-title"
                      >
                        Setting
                      </h3>
                      <hr class="my-4 border-gray-300" />
                    </div>
                    <div class="row-start-2 h-full">
                      <div class="flex h-full flex-row">
                        <div class="flex flex-col items-start gap-2 w-full">
                          <button
                            class="block w-full cursor-pointer rounded-lg pl-2 text-left"
                            :class="{
                              'bg-gray-100': activeTab === 'AiService',
                            }"
                            :aria-selected="activeTab === 'AiService'"
                            role="tab"
                            @click="activeTab = 'AiService'"
                          >
                            Ai services
                          </button>
                          <button
                            class="block w-full cursor-pointer rounded-lg pl-2 text-left"
                            :class="{
                              'bg-gray-100': activeTab === 'LLMs',
                            }"
                            :aria-selected="activeTab === 'LLMs'"
                            role="tab"
                            @click="activeTab = 'LLMs'"
                          >
                            LLMs
                          </button>
                        </div>
                        <div class="ml-3 w-px self-stretch bg-gray-300"></div>
                      </div>
                    </div>
                    <component :is="activeComponent" />
                  </div>
                </div>
              </div>
            </div>
            <div
              class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
            >
              <button
                type="button"
                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50 sm:mt-0 sm:w-auto"
                @click="closeModal"
              >
                Close
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import {
  computed,
  defineProps,
  defineEmits,
  onMounted,
  onBeforeUnmount,
  ref,
} from "vue";
import ManageLLMs from "@/components/Setting/ManageLLMs.vue";
import ManageAiService from "@/components/Setting/ManageAiService.vue";

const activeTab = ref<"LLMs" | "AiService">("AiService");

const activeComponent = computed(() => {
  return activeTab.value === "LLMs" ? ManageLLMs : ManageAiService;
});

defineProps<{
  openSetting: boolean;
}>();

const emit = defineEmits<{
  (e: "update:openSetting", value: boolean): void;
  (e: "confirm"): void;
}>();

const closeModal = () => {
  emit("update:openSetting", false);
};

const handleKeydown = (e: KeyboardEvent) => {
  if (e.key === "Escape") {
    closeModal();
  }
};

onMounted(() => {
  document.addEventListener("keydown", handleKeydown);
});
onBeforeUnmount(() => {
  document.removeEventListener("keydown", handleKeydown);
});
</script>

<style scoped>
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.3s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
}
.modal-fade-enter-to,
.modal-fade-leave-from {
  opacity: 1;
}

.backdrop-fade-enter-active,
.backdrop-fade-leave-active {
  transition: opacity 0.3s ease;
}
.backdrop-fade-enter-from,
.backdrop-fade-leave-to {
  opacity: 0;
}
.backdrop-fade-enter-to,
.backdrop-fade-leave-from {
  opacity: 1;
}

.modal-panel-enter-active {
  transition: all 0.3s ease-out;
}
.modal-panel-leave-active {
  transition: all 0.2s ease-in;
}
.modal-panel-enter-from {
  opacity: 0;
  transform: translateY(10%) scale(0.95);
}
.modal-panel-enter-to {
  opacity: 1;
  transform: translateY(0) scale(1);
}
.modal-panel-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}
.modal-panel-leave-to {
  opacity: 0;
  transform: translateY(10%) scale(0.95);
}
</style>
