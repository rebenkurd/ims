<template>
    <div>
        <Spinner v-if="loading" class="mx-auto" />
        <div v-else class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">
                
                {{ supplier.id ? `Edit Supplier: "${supplier.name}"` : "Create New Supplier" }}
            </h2>

            <form @submit.prevent="onSubmit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="supplier.name" label="Supplier Name" class="mb-2" :required="true" />
                    <CustomInput v-model="supplier.mobile" label="Mobile" class="mb-2" />
                    <CustomInput type="email" v-model="supplier.email" label="Email" class="mb-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <!-- <CustomInput type="number" v-model="supplier.opening_balance" label="Opening Balance" class="mb-2" /> -->
                    <!-- <CustomInput type="select" v-model="supplier.payment_id" label="Payment Type" :options="paymentTypeOptions" class="mb-2" /> -->
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="supplier.country" label="Country" class="mb-2" />
                    <CustomInput v-model="supplier.state" label="State" class="mb-2" />
                    <CustomInput v-model="supplier.city" label="City" class="mb-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="supplier.phone" label="Phone" class="mb-2" />
                    <CustomInput v-model="supplier.postcode" label="Postcode" class="mb-2" />
                    <CustomInput type="select" v-model="supplier.status" label="Status" :options="statusOptions" class="mb-2" />

                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput type="textarea" v-model="supplier.address" label="Address" class="mb-2 col-span-2" />
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
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSupplierStore } from '@store/suppliers';
import Spinner from '@components/core/Spinner.vue';
import CustomInput from '@components/core/CustomInput.vue';
import router from '@router';
import { useToast } from "vue-toastification"

const toast = useToast();
const supplierStore = useSupplierStore();
const route = useRoute();

const loading = ref(false);
const DEFAULT_SUPPLIER = {
    id: '',
    name: '',
    mobile: '',
    email: '',
    phone: '',
    opening_balance: 0,
    payment_id: '',
    country: '',
    state: '',
    city: '',
    postcode: '',
    address: '',
    status: 1
};

const supplier = ref({ ...DEFAULT_SUPPLIER });

const resetForm = () => {
    supplier.value = { ...DEFAULT_SUPPLIER };
};


const paymentTypeOptions = ref([
    { value: 1, label: 'Cash' },
    { value: 2, label: 'Credit' },
    { value: 3, label: 'Bank Transfer' }
]);

const statusOptions = ref([
    { value: 1, label: 'Active' },
    { value: 0, label: 'Inactive' }
]);

onMounted(() => {
    if (route.params.id) {
        getSupplier(route.params.id);
    }else{
        resetForm();
    }
});

const getSupplier = (id) => {
    loading.value = true;
    supplierStore.getSupplier(id).then(({ data }) => {
        supplier.value = data;
        loading.value = false;
    }).catch(error => {
        loading.value = false;
        toast.error("Failed to load supplier data");
        router.push({ name: 'app.supplier_list' });
    });
};

const onSubmit = () => {
    loading.value = true;
    if (supplier.value.id) {
        supplierStore.updateSupplier(supplier.value).then((
            {data}
        ) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.supplier_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    } else {
        supplierStore.createSupplier(supplier.value).then(({data}) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.supplier_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    }
};

const goBack = () => {
    resetForm();
    router.push({ name: 'app.supplier_list' });
};
watch(() => route.params.id, (newId) => {
    if (newId) {
        getSupplier(newId);
    } else {
        resetForm();
    }
});
</script>