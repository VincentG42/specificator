import './assets/css/tailwind.css';

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import axios from 'axios'

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api';

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.mount('#app')
