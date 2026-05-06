import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        name: "Projects",
        path: "/projects",
        component: () => import("../views/projects/index.vue"),
    },
    {
        name: "Documentation",
        path: "/project/:id/documentation/1",
        component: () => import("../views/projects/documentation/index.vue"),
        children: [
            {
                name: "Docs 1",
                path: "/project/:id/documentation/1",
                component: () => import("../views/projects/documentation/[id].vue"),
            }
        ]
    }
]
const router = createRouter({
    history: createWebHistory(),
    routes: routes,
  });
export default router;