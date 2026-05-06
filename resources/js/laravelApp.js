import { createApp } from 'vue';
// vue app create form larave
const laravelApp = createApp({});

import AppCard from "./components/AppCard.vue"
import AppButton from "./components/AppButton.vue"
import AppSideBar from "./components/AppSideBar.vue"
import AppIcon from "./components/AppIcon.vue"
// import AppSelect from "./components/AppSelect.vue"
import AppInput from "./components/AppInput.vue"

// import AppTaskCard from "./components/AppTaskCard.vue"
// import AppModalDrawer from "./components/AppModalDrawer.vue"

laravelApp.component("app-card", AppCard)
laravelApp.component("app-button", AppButton)
// app.use(router)
laravelApp.component("app-side-bar", AppSideBar)
laravelApp.component("app-icon", AppIcon)
// laravelApp.component("app-select",AppSelect)
laravelApp.component("app-input",AppInput)
// app.component("app-task-card",AppTaskCard)
// laravelApp.component("app-modal-drawer",AppModalDrawer)
laravelApp.mount("#root");