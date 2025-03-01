import { createRouter, RouteRecordRaw, createWebHistory } from "vue-router";
import { useUserStore } from "../stores/userStore";

const routes: RouteRecordRaw[] = [
  {
    name: "main",
    path: "/",
    component: () => import("@/pages/Chat/ChatPage.vue"),
    meta: { requiresAuth: true },
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

// router.beforeEach((to, from, next) => {
//   const userStore = useUserStore();

//   if (to.meta.requiresAuth && !userStore.user.token) {
//     next({ name: 'login' });
//   } else if (userStore.user.token && (to.name === 'login' || to.name === 'register')) {
//     next({ name: 'main' });
//   } else {
//     next();
//   }
// });

export default router;
