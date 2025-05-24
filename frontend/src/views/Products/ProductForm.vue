<template>
    <div>
        <Spinner v-if="loading" class="mx-auto" />
    <div v-else class="bg-white p-4 rounded-lg shadow ">
        <h2 class="text-xl font-semibold mb-4">
            {{ product.id ? `Edit Product: "${product.name}"` : "Create New Product" }}
        </h2>


        <form @submit.prevent="onSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput v-model="product.code" label="Product Code" class="mb-2" :required="true" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput v-model="product.name" label="Product Name" class="mb-2" :required="true" />
                <CustomInput type="select" :addModal="true" :searchable="true" v-model="product.brand_id" label="Brand" :options="brandOptions"
                    />                
                <CustomInput type="select" :searchable="true" v-model="product.category_id" label="Category" :options="categoryOptions" :addModal="true" :required="true"
                    />                
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput type="number" v-model="product.per_carton" label="Per Carton" class="mb-2" :required="true" />
                <CustomInput type="number" v-model="product.minimum_qty" label="Minimum Qty" class="mb-2" />
                <CustomInput v-model="product.barcode" label="Barcode" class="mb-2" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput type="date" v-model="product.expire_date" label="Expire Date" class="mb-2" />
                <CustomInput type="file" v-model="product.image" label="Product Image" class="mb-2"
                @change="file => product.image = file" info="Max Width/Height: 1000px * 1000px & Size: 1MB"
                 />
                <CustomInput type="textarea" v-model="product.description" label="Product Description" class="mb-2" />  
            </div>

            <hr class="text-gray-200 my-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput type="number" v-model="product.purchase_price" label="Purchase Price" class="mb-2" :required="true" />
                <CustomInput type="number" symbol="%"  v-model="product.profit_margin" label="Profit Margin" class="mb-2" />
                <CustomInput  v-model="product.sales_price" label="Sale Price" class="mb-2" :required="true" />
            </div>

            <hr class="text-gray-200 my-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput type="select" v-model="product.discount_type" label="Discount Type" :options="discountTypeOptions"/>
                <CustomInput v-model="product.discount" label="Discount" class="mb-2" />
                <CustomInput :disabled="true" v-model="product.final_price" label="Final Price" class="mb-2"/>
            </div>

            <hr class="text-gray-200 my-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
                <CustomInput type="number" v-model="product.current_opening_stock" label="Current Opening Stock" class="mb-2" />
                <CustomInput type="number" v-model="product.adjust_stock" label="Adjust Stock" class="mb-2" />
                <CustomInput type="textarea" v-model="product.adjustment_note" label="Adjustment Note" class="mb-2" />
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
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useStore } from '../../store';
import { useProductStore } from '../../store/product';
import { useCategoryStore } from '../../store/category';
import { useBrandStore } from '../../store/brand';
import Spinner from '../../components/core/Spinner.vue';
import CustomInput from '../../components/core/CustomInput.vue';
import router from '../../router';
import { useToast } from "vue-toastification"

const toast = useToast();
const productStore = useProductStore();
const categoryStore = useCategoryStore();
const brandStore = useBrandStore();
const store = useStore();
const route = useRoute();

const loading = ref(false);
const DEFAULT_PRODUCT = {
    id: '',
    code: '',
    name: '',
    brand_id: '',
    category_id: '',
    per_carton: '',
    minimum_qty: 0,
    barcode: '',
    expire_date: null,
    image: '',
    description: '',
    purchase_price: null,
    sales_price: null,
    final_price: null,
    profit_margin: 0,
    discount_type: '',
    discount: 0,
    current_opening_stock: 0,
    adjust_stock: 0,
    adjustment_note: '',
};

const product = ref({ ...DEFAULT_PRODUCT });

onMounted(() => {
    if (route.params.id) {
        getProduct(route.params.id);
    }
    brandStore.getBrands();
    categoryStore.getCategorys();
});

const getProduct = (id) => {
  loading.value = true;
  productStore.getProduct(id).then(({ data }) => {
    product.value = data;
    loading.value = false;
  });
};

const brandOptions = computed(() => {
    
    return brandStore.brands.data.map(item => ({
        value: item.id,
        label: item.name 
    }));
});

const categoryOptions = computed(() => {
    return categoryStore.categories.data.map(item => ({
        value: item.id,
        label: item.name 
    }));
});

const discountTypeOptions = ref([
        {
            value: 'percentage',
            label: 'Percentage'
        },
        {
            value: 'fixed',
            label: 'Fixed'
        }

]);


const onSubmit = () => {
    loading.value = true;


    if (product.value.id) {
        productStore.updateProduct(product.value).then((
            {data}
        ) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.product_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    } else {
        productStore.createProduct(product.value).then(({data}) => {
            loading.value = false;
            toast.success(data.message);
            router.push({ name: 'app.product_list' }); 
        }).catch(({response})=>{
            loading.value = false;
            toast.error(response.data.message);
        });
    }
};

const goBack = () => {
    router.push({ name: 'app.product_list' }); 
};



watch(() => product.value.purchase_price, (newPrice) => {
  if (newPrice) {
    product.value.sales_price = parseFloat(newPrice); 
    product.value.final_price = parseFloat(newPrice); 
    updatePrices();
  }
});
watch(() => product.value.profit_margin, ()=>updatePrices());
watch(() => product.value.discount,()=> applyDiscount());
watch(() => product.value.discount_type, ()=>applyDiscount());

const updatePrices = () => {
  const purchasePrice = parseFloat(product.value.purchase_price) || 0;
  const margin = parseFloat(product.value.profit_margin) || 0;

  const profitAmount = (purchasePrice * margin) / 100;
  product.value.sales_price = purchasePrice + profitAmount;

  applyDiscount();
};

const applyDiscount = () => {
  const discountValue = parseFloat(product.value.discount) || 0;
  const originalPrice = parseFloat(product.value.sales_price) || 0;

  if (product.value.discount_type === 'fixed') {
    product.value.final_price = Math.max(0, originalPrice - discountValue);
  } else if (product.value.discount_type === 'percentage') {
    const discountAmount = (originalPrice * discountValue) / 100;
    product.value.final_price = Math.max(0, originalPrice - discountAmount);
  }
};

</script>