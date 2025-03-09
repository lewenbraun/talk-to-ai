import "./bootstrap";
import "@/../css/app.css";
import App from "./App.vue";
import { createApp } from "vue";
import pinia from "@/stores";
import router from "@/router";

const app = createApp(App);
app.use(router);
app.use(pinia); 

app.mount("#app");
