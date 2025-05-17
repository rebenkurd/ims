<template>
    <div>
        <Spinner v-if="loading" class="mx-auto" />
        <div v-else class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">
                {{ customer.id ? `Edit Customer: "${customer.name}"` : "Create New Customer" }}
            </h2>

            <form @submit.prevent="onSubmit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="customer.name" label="Customer Name" class="mb-2" :required="true" />
                    <CustomInput v-model="customer.mobile" label="Mobile" class="mb-2" />
                    <CustomInput type="email" v-model="customer.email" label="Email" class="mb-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="customer.country" label="Country" class="mb-2" />
                    <CustomInput v-model="customer.state" label="State" class="mb-2" />
                    <CustomInput v-model="customer.city" label="City" class="mb-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="customer.phone" label="Phone" class="mb-2" />
                    <CustomInput v-model="customer.postcode" label="Postcode" class="mb-2" />
                    <CustomInput type="select" v-model="customer.status" label="Status" :options="statusOptions" class="mb-2" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput type="textarea" v-model="customer.address" label="Address" class="mb-2 col-span-2" />
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
import { useCustomerStore } from '../../store/customers';
import Spinner from '../../components/core/Spinner.vue';
import CustomInput from '../../components/core/CustomInput.vue';
import router from '../../router';
import { useToast } from "vue-toastification"

const toast = useToast();
const customerStore = useCustomerStore();
const route = useRoute();

const loading = ref(false);
const DEFAULT_CUSTOMER = {
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

const customer = ref({ ...DEFAULT_CUSTOMER });

const statusOptions = ref([
    { value: 1, label: 'Active' },
    { value: 0, label: 'Inactive' }
]);

onMounted(() => {
    if (route.params.id) {
        getCustomer(route.params.id);
    }
});

const getCustomer = (id) => {
    loading.value = true;
    customerStore.getCustomer(id).then(({ data }) => {
        customer.value = data;
        loading.value = false;
    }).catch(error => {
        loading.value = false;
        toast.error("Failed to load customer data");
        router.push({ name: 'app.customer_list' });
    });
};

const onSubmit = () => {
    loading.value = true;
    if (customer.value.id) {
        customerStore.updateCustomer(customer.value).then(({data}) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.customer_list' }); 
        }).catch(({response}) => {
            loading.value = false;
            toast.error(response.data.message);
        });
    } else {
        customerStore.createCustomer(customer.value).then(({data}) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.customer_list' }); 
        }).catch(({response}) => {
            loading.value = false;
            toast.error(response.data.message);
        });
    }
};

const goBack = () => {
    router.push({ name: 'app.customer_list' }); 
};
</script>