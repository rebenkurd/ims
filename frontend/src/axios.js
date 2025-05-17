import axios from 'axios';
import {useStore} from './store';
import router from './router';


const axiosClient = axios.create({
    baseURL: `${import.meta.env.VITE_API_BASE_URL}/api`,
})

axiosClient.interceptors.request.use(config=>{
    const store = useStore();
    config.headers.Authorization = `Bearer ${store.user.token}`;
    return config;
});
axiosClient.interceptors.request.use(response=>{
    return response;
},error=>{
    if(error.response.status === 401){
        sessionStorage.removeItem('token');
        router.push({name:'login'});
    }
    console.log(error);
    
});


export default axiosClient;