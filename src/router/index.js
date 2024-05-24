// Default
import { createWebHistory, createRouter } from "vue-router";
const routes = [
    {
        path: "/",
        name: "Home",
        meta: {
          mainPage: "Home",
        },
        component: () => import("../views/Home.vue"),
    },
    {
        path: "/apply/:step",
        name: "Apply to job",
        meta: {
          mainPage: "Apply to job",
        },
        component: () => import("../views/Apply.vue"),
    },
]
const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior: function (to, _from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        }
        if (to.hash) {
            return { el: to.hash, behavior: "smooth" };
        } else {
            window.scrollTo(0, 0);
        }
    },
});
export default router;