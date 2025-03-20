import { createRouter, RouteRecordRaw, createWebHistory } from "vue-router";
import { useUserStore } from "../stores/userStore";
import { useChatStore } from "../stores/chatStore";
import { useAiServiceStore } from "@/stores/aiServiceStore";

const routes: RouteRecordRaw[] = [
  {
    name: "main",
    path: "/",
    component: () => import("@/pages/Chat/ChatPage.vue"),
    meta: { requiresAuth: false },
    children: [
      {
        path: "/new-chat",
        component: () => import("@/pages/Chat/ChatPage.vue"),
        name: "new-chat",
        beforeEnter: async (to, from, next) => {
          const chatStore = useChatStore();
          await chatStore.loadChatList();
          chatStore.startNewChat();
          next();
        },
      },
      {
        path: "/chat/:chat_id",
        component: () => import("@/pages/Chat/ChatPage.vue"),
        name: "chat",
        beforeEnter: async (to, from, next) => {
          const chatStore = useChatStore();
          await chatStore.loadChatList();

          const chatId = to.params.chat_id;

          const chat = chatStore.chats.find(
            (item) => String(item.id) === chatId
          );

          try {
            if (chat) {
              await chatStore.setCurrentChat(chat);
              next();
            } else {
              next({ name: "new-chat" });
            }
          } catch (error) {
            console.error("Error loading chat messages in route guard:", error);
            next();
          }
        },
      },
    ],
    beforeEnter: async (to, from, next) => {
      const userStore = useUserStore();
      const aiServiceStore = useAiServiceStore();
      await userStore.loadUser();
      await aiServiceStore.loadAiServiceList();

      next();
    },
  },
  {
    name: "regiser",
    path: "/register",
    component: () => import("@/pages/Auth/RegisterPage.vue"),
  },
  {
    name: "login",
    path: "/login",
    component: () => import("@/pages/Auth/LoginPage.vue"),
  },
  {
    path: "/:catchAll(.*)*",
    redirect: { name: "new-chat" },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore();
  if (!userStore.token) {
    await userStore.createTemporaryUser();
  }
  if (
    !userStore.isTemporaryUser() &&
    (to.name === "login" || to.name === "register" || to.name === "main")
  ) {
    next({ name: "new-chat" });
  } else {
    next();
  }
});

export default router;
