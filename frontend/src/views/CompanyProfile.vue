<template>
    <div>
        <Spinner v-if="loading" class="mx-auto" />
        <div v-else class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">
                Company Profile
            </h2>

            <form @submit.prevent="onSubmit">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="company.company_name" label="Company Name" class="mb-2" :required="true" />
                    <CustomInput v-model="company.mobile" label="Mobile" class="mb-2" :required="true" />
                    <CustomInput type="email" v-model="company.email" label="Email" class="mb-2" :required="true" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="company.phone" label="Phone" class="mb-2" />
                    <CustomInput v-model="company.website" label="Website" class="mb-2" />
                    <CustomInput 
                        type="file" 
                        label="Company Logo" 
                        class="mb-2"
                        @change="handleFileChange"
                        info="Max Width/Height: 1000px * 1000px & Size: 1MB"
                    />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="company.country" label="Country" class="mb-2" />
                    <CustomInput v-model="company.state" label="State" class="mb-2" />
                    <CustomInput v-model="company.city" label="City" class="mb-2" :required="true" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                    <CustomInput v-model="company.postcode" label="Postcode" class="mb-2" />
                    <CustomInput type="textarea" v-model="company.address" label="Address" class="mb-2 col-span-2" :required="true" />
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
import { useRouter } from 'vue-router';
import { useCompanyProfileStore } from '@store/company_profile';
import Spinner from '@components/core/Spinner.vue';
import CustomInput from '@components/core/CustomInput.vue';
import { useToast } from "vue-toastification"

const toast = useToast();
const companyProfileStore = useCompanyProfileStore();
const router = useRouter();

const loading = ref(false);
const DEFAULT_COMPANY = {
    company_name: '',
    mobile: '',
    email: '',
    phone: '',
    website: '',
    country: '',
    state: '',
    city: '',
    postcode: '',
    address: '',
    logo: null,
};

const company = ref({ ...DEFAULT_COMPANY });

onMounted(() => {
    getCompanyProfile();   
});

const getCompanyProfile = async () => {
    try {
        loading.value = true;
        const companyData = await companyProfileStore.getCompanyProfile();
        company.value = companyData ? { ...companyData } : { ...DEFAULT_COMPANY };        
    } catch (error) {
        toast.error("Failed to load company profile");
    } finally {
        loading.value = false;
    }
};

const handleFileChange = (event) => {
    company.value.logo = event.target.files[0];
};

const onSubmit = () => {
    loading.value = true;
    
    const formData = new FormData();
    
    // Append all fields including the logo if it's a File
    Object.keys(company.value).forEach(key => {
        if (company.value[key] !== null && company.value[key] !== undefined) {
            if (key === 'logo' && company.value[key] instanceof File) {
                formData.append(key, company.value[key]);
            } else {
                formData.append(key, company.value[key]);
            }
        }
    });

    companyProfileStore.updateCompanyProfile(formData)
        .then((response) => {
            loading.value = false;
            toast.success(response.message);
            // Update the company data but don't reset the logo
            company.value = {
                ...company.value,
                ...response.data
            };
        })
        .catch((error) => {
            loading.value = false;
            toast.error(error.response?.data?.message || "Failed to update company profile");
        });
};

</script>