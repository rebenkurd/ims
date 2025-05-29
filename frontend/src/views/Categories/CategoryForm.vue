<template>
    <div>
        <Spinner v-if="loading" class="mx-auto" />
    <div v-else class="bg-white p-4 rounded-lg shadow ">
        <h2 class="text-xl font-semibold mb-4">
            {{ category.id ? `Edit Category: "${category.name}"` : "Create New Category" }}
        </h2>


        <form @submit.prevent="onSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2.5">
                <CustomInput v-model="category.name" label="Name" class="mb-2" :required="true" />       
                <CustomInput type="textarea" v-model="category.description" label="Description" class="mb-2" />            
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
import { useCategoryStore } from '@store/category';
import Spinner from '@components/core/Spinner.vue';
import CustomInput from '@components/core/CustomInput.vue';
import router from '@router';
import { useToast } from "vue-toastification"

const toast = useToast();
const categoryStore = useCategoryStore();
const route = useRoute();

const loading = ref(false);
const DEFAULT_CATEGORY = {
    id: '',
    name: '',
    description: '',
};

const category = ref({ ...DEFAULT_CATEGORY });



onMounted(() => {
    if (route.params.id) {
        getCategory(route.params.id);
    }
});

const getCategory = (id) => {
  loading.value = true;
  categoryStore.getCategory(id).then(( {data} ) => {
    category.value = data;
    loading.value = false;    
  });
};



const onSubmit = () => {
    loading.value = true;


    if (category.value.id) {
        categoryStore.updateCategory(category.value).then((
            {data}
        ) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.category_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    } else {
        categoryStore.createCategory(category.value).then(({data}) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.category_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    }
};

const goBack = () => {
    router.push({ name: 'app.category_list' }); 
};


</script>