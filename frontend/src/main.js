import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import Toast from "vue-toastification"
import VueSweetalert2 from 'vue-sweetalert2';
import './style.css'
import "vue-toastification/dist/index.css"
import 'sweetalert2/dist/sweetalert2.min.css';



const app =createApp(App);
const options = {
    position: "top-right",
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: "button",
    icon: true,
    rtl: false,
}

app.use(router)
app.use(createPinia())
app.use(Toast, options)
app.use(VueSweetalert2)
app.mount('#app')
