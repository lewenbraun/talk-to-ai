import { createRouter, RouteRecordRaw, createWebHistory } from "vue-router";
import { useUserStore } from "../stores/userStore";
import { Chat, useChatStore } from "../stores/chatStore";

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
        beforeEnter: (to, from, next) => {
          const chatStore = useChatStore();
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
              await chatStore.loadMessagesForChat(chat);
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
  },
  {
    name: "regiser",
    path: "/register",
    component: () => import("@/layouts/AuthLayout.vue"),
    children: [
      {
        path: "",
        component: () => import("@/pages/Auth/RegisterPage.vue"),
        name: "register",
      },
    ],
  },
  {
    path: "/login",
    component: () => import("@/layouts/AuthLayout.vue"),
    children: [
      {
        path: "",
        component: () => import("@/pages/Auth/LoginPage.vue"),
        name: "login",
      },
    ],
  },
  {
    path: "/:catchAll(.*)*",
    component: () => import("@/pages/ErrorNotFound.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore();
  if (!userStore.user.token) {
    await userStore.createTemporaryUser();
  }
  if (
    userStore.user.token &&
    (to.name === "login" || to.name === "register" || to.name === "main")
  ) {
    next({ name: "new-chat" });
  } else {
    next();
  }
});

export default router;
