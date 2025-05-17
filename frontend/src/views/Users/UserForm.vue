<template>
    <div>
        <Spinner v-if="loading" class="mx-auto" />
    <div v-else class="bg-white p-4 rounded-lg shadow ">
        <h2 class="text-xl font-semibold mb-4">
            {{ user.id ? `Edit User: "${user.name}"` : "Create New User" }}
        </h2>


        <form @submit.prevent="onSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput v-model="user.name" label="Name" class="mb-2" :required="true" />       
                <CustomInput v-model="user.username" label="Username" class="mb-2" :required="true" />       
                <CustomInput type="email" v-model="user.email" label="Email" class="mb-2" :required="true" />            
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput v-model="user.phone" label="Phone" class="mb-2" :required="true" />       
                <CustomInput v-model="user.mobile" label="Mobile" class="mb-2"  />       
                <CustomInput type="date" v-model="user.dob" label="Date of Berth" class="mb-2"  />  
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput type="password" v-model="user.password" label="Password" class="mb-2" :required="true" />       
                <!-- <CustomInput type="password" v-model="user.confirmation_password" label="Confirmation Password" class="mb-2" :required="true" />        -->
                <CustomInput type="file" v-model="user.image" label="Image" class="mb-2"
                @change="file => user.image = file" info="Max Width/Height: 1000px * 1000px & Size: 1MB"
                 />
            </div>

            <hr class="text-gray-200 my-4">
            <div class="flex justify-center items-center space-x-2">
                <button type="submit" class="py-2 px-10 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Save
                </button>
                <button type="button" @click="goBack"
                    class="py-2 px-10 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useUserStore } from '../../store/user';
import Spinner from '../../components/core/Spinner.vue';
import CustomInput from '../../components/core/CustomInput.vue';
import router from '../../router';
import { useToast } from "vue-toastification"

const toast = useToast();
const userStore = useUserStore();
const route = useRoute();

const loading = ref(false);
const DEFAULT_USER = {
    id: '',
    name: '',
    username: '',
    email: '',
    password: '',
    confirmation_password: '',
    phone: '',
    mobile: '',
    dob: '',
    image: null,

};

const user = ref({ ...DEFAULT_USER });



onMounted(() => {
    if (route.params.id) {
        getUser(route.params.id);
    }
});

const getUser = (id) => {
  loading.value = true;
  userStore.getUser(id).then(( {data} ) => {
    user.value = data;
    loading.value = false;
    console.log(data);
    
  });
};



const onSubmit = () => {
    loading.value = true;


    if (user.value.id) {
        userStore.updateUser(user.value).then((
            {data}
        ) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.user_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    } else {
        userStore.createUser(user.value).then(({data}) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.user_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    }
};

const goBack = () => {
    router.push({ name: 'app.user_list' }); 
};


</script>