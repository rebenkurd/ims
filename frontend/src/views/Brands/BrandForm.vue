<template>
    <div>
        <Spinner v-if="loading" class="mx-auto" />
    <div v-else class="bg-white p-4 rounded-lg shadow ">
        <h2 class="text-xl font-semibold mb-4">
            {{ brand.id ? `Edit Brand: "${brand.name}"` : "Create New Brand" }}
        </h2>


        <form @submit.prevent="onSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2.5">
                <CustomInput v-model="brand.name" label="Name" class="mb-2" :required="true" />       
                <CustomInput type="textarea" v-model="brand.description" label="Description" class="mb-2" />            
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
import { useBrandStore } from '@store/brand';
import Spinner from '@components/core/Spinner.vue';
import CustomInput from '@components/core/CustomInput.vue';
import router from '@router';
import { useToast } from "vue-toastification"

const toast = useToast();
const brandStore = useBrandStore();
const route = useRoute();

const loading = ref(false);
const DEFAULT_BRAND = {
    id: '',
    name: '',
    description: '',
};

const brand = ref({ ...DEFAULT_BRAND });



onMounted(() => {
    if (route.params.id) {
        getBrand(route.params.id);
    }
});

const getBrand = (id) => {
  loading.value = true;
  brandStore.getBrand(id).then(( {data} ) => {
    brand.value = data;
    loading.value = false;    
  });
};



const onSubmit = () => {
    loading.value = true;


    if (brand.value.id) {
        brandStore.updateBrand(brand.value).then((
            {data}
        ) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.brand_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    } else {
        brandStore.createBrand(brand.value).then(({data}) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.brand_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    }
};

const goBack = () => {
    router.push({ name: 'app.brand_list' }); 
};


</script>