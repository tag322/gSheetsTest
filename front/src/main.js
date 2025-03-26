import { createApp } from 'vue'
import router from '@/router/index.js'
import App from './App.vue'
import '@popperjs/core/dist/umd/popper';
import 'bootstrap/dist/css/bootstrap.css'
import 'jquery/src/jquery.js'
import 'bootstrap/dist/js/bootstrap.js'
import getCookie from './helpers/index'


const app = createApp(App)
.use(router)

app.config.globalProperties.getCookie = getCookie


app.mount('#app')
