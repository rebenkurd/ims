<template>
  <div>
    <Spinner v-if="loading" class="mx-auto" />
    <div v-else class="bg-white p-4 rounded-lg shadow ">
      <h2 class="text-xl font-semibold mb-4">
        {{ purchase.id ? `Edit Purchase: "${purchase.name}"` : "Create New Purchase" }}
      </h2>


      <form @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2.5">
          <CustomInput type="select" :addModal="true" :searchable="true" v-model="purchase.supplier_id"
            label="Supplier Name" :options="suppliersOptions" />
          <CustomInput type="date" v-model="purchase.date" label="Purchase Date" class="mb-2" :required="true" />
          <CustomInput type="select" v-model="purchase.purchase_status" label="Purchase Status"
            :options="purchaseStatusOptions" class="mb-2" />
          <CustomInput v-model="purchase.reference_no" label="Reference No." class="mb-2" />
        </div>
        <hr class="text-gray-200 my-4">

        <div class="bg-white p-4">
          <div class="w-full relative">
            <div class="relative">
              <input type="text" v-model="searchQuery" @input="searchProducts" placeholder="Item name/Barcode/Itemcode"
                class="block w-full rounded-md bg-white pl-8 pr-3 py-2 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-gray-400 focus:border-gray-400 focus:z-10 sm:text-sm" />
              <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd"
                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                    clip-rule="evenodd"></path>
                </svg>
              </span>
            </div>

            <div v-if="searchResults.length > 0"
              class="absolute w-full mt-1 rounded-md max-h-80 overflow-y-scroll z-10 bg-white border border-gray-300">
              <div v-for="product in searchResults" :key="product.id" @click="selectProduct(product)"
                class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200">
                {{ product.barcode }} -- {{ product.name }}
              </div>
            </div>
          </div>

          <div class="mt-4">
            <table class="w-full text-left">
              <thead>
                <tr class="bg-gray-800 text-white">
                  <th class="border-b-2 p-2">Item Name</th>
                  <th class="border-b-2 p-2">Quantity</th>
                  <th class="border-b-2 p-2">Purchase Price</th>
                  <th class="border-b-2 p-2">Discount</th>
                  <th class="border-b-2 p-2">Unit Cost</th>
                  <th class="border-b-2 p-2">Total Amount</th>
                  <th class="border-b-2 p-2">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in cartStore.items" :key="item.id" class="border-b">
                  <td class="border-b-2 p-2">{{ item.name }}</td>
                  <td class="border-b-2 p-2">
                    <div class="flex items-center">
                      <button type="button" @click="decrementQuantity(item.id)"
                        class="bg-gray-200 px-2 py-1 rounded-md text-bold cursor-pointer">-</button>
                      <input type="number" v-model="item.quantity" min="1"
                        class="w-20 text-center mx-1 rounded-md bg-white pl-8 pr-3 py-2 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-gray-400 focus:border-gray-400 focus:z-10 sm:text-sm"
                        @change="updateQuantity(item.id, item.quantity)" />
                      <button type="button" @click="incrementQuantity(item.id)"
                        class="bg-gray-200 px-2 py-1 rounded-md text-bold cursor-pointer">+</button>
                    </div>
                  </td>
                  <td class="border-b-2 p-2">
                    <input type="number" v-model="item.purchasePrice"
                      class="w-24 mx-1 rounded-md bg-white pl-8 pr-3 py-2 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-gray-400 focus:border-gray-400 focus:z-10 sm:text-sm"
                      @change="updateItemTotals(item)" />
                  </td>
                  <td class="border-b-2 p-2">
                    <input type="number" v-model="item.discount"
                      class="w-20 mx-1 rounded-md bg-white pl-8 pr-3 py-2 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-gray-400 focus:border-gray-400 focus:z-10 sm:text-sm"
                      @change="updateItemTotals(item)" />
                  </td>
                  <td class="border-b-2 p-2">{{ item.unitCost.toFixed(2) }}</td>
                  <td class="border-b-2 p-2">{{ item.totalAmount.toFixed(2) }}</td>
                  <td class="border-b-2 p-2">
                    <TrashIcon @click="removeItem(item.id)" class="mr-2 h-5 w-5 text-red-400 cursor-pointer"
                      aria-hidden="true" />
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex mt-10">
            <!-- Left side -->
            <div class="w-1/2">
              <div class="flex mb-4">
                <div class="w-32 font-semibold">Total Quantities</div>
                <div class="text-green-700 font-bold text-lg">{{ cartStore.totalQuantities }}</div>
              </div>


              <div class="flex mb-4">
                <div class="w-32 font-semibold">Discount on All</div>
                <CustomInput type="number" v-model="purchase.discount" class="ml-2" />
                <CustomInput type="select" v-model="purchase.discountType" class="ml-2"
                  :options="discountTypeOptions" />

              </div>

              <div class="flex mb-4">
                <CustomInput type="textarea" v-model="purchase.note" label="Note" class="mb-2 w-full" />
              </div>
            </div>

            <!-- Right side - Totals -->
            <div class="w-1/2 pl-8">
              <div class="flex justify-between mb-2">
                <div class="font-semibold">Subtotal</div>
                <div>$ {{ cartStore.subtotal.toFixed(2) }}</div>
              </div>


              <div class="flex justify-between mb-2">
                <div class="font-semibold">Discount on All</div>
                <div>$ {{ (cartStore.discountType === 'Per%' ? cartStore.subtotal * (cartStore.discountAll / 100) :
                  cartStore.discountAll).toFixed(2) }}</div>
              </div>

              <div class="flex justify-between font-bold text-lg">
                <div>Grand Total</div>
                <div>$ {{ cartStore.grandTotal.toFixed(2) }}</div>
                <input type="hidden" v-model="cartStore.grandTotal">
              </div>

            </div>
          </div>
        </div>


        <hr class="text-gray-200 my-4">
        <h1 class="text-lg m-2 font-bold">Previous Payments Information :</h1>
        <table class="table-auto w-full mb-10">
          <thead class="bg-gray-400">
            <tr>
              <th class="border-b-2 p-2 text-left text-white">#</th>
              <th class="border-b-2 p-2 text-left text-white">Date</th>
              <th class="border-b-2 p-2 text-left text-white">Payment Type</th>
              <th class="border-b-2 p-2 text-left text-white">Payment Note</th>
              <th class="border-b-2 p-2 text-left text-white">Payment</th>
              <th class="border-b-2 p-2 text-left text-white">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border-b-2 p-2">Product 1</td>
              <td class="border-b-2 p-2">2</td>
              <td class="border-b-2 p-2">100</td>
              <td class="border-b-2 p-2">20%</td>
              <td class="border-b-2 p-2">80</td>
              <td class="border-b-2 p-2">
                <TrashIcon class="mr-2 h-5 w-5 text-red-400 cursor-pointer" aria-hidden="true" />
              </td>
            </tr>
          </tbody>
        </table>
        <hr class="text-gray-200 my-4">
        <div class="shadow bg-gray-200 p-4 rounded-md">
          <h1>Make Payment:</h1>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2.5">
            <CustomInput type="number" v-model="purchase.amount" label="Amount" class="mb-2" />
            <CustomInput type="select" v-model="purchase.purchase_status" label="Payment Type"
              :options="paymentTypeOptions" class="mb-2" />
            <CustomInput type="textarea" v-model="purchase.note" label="Payment Note" class="mb-2 col-span-2" />
          </div>
        </div>

        <hr class="text-gray-200 my-4">
        <div class="flex justify-center items-center space-x-2">
          <button type="submit" class="py-2 px-10 bg-blue-600 text-white rounded hover:bg-blue-700">
            Save
          </button>
          <button type="button" @click="goBack" class="py-2 px-10 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
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
import { usePurchaseStore } from '../../store/purchase';
import { useSupplierStore } from '../../store/suppliers';
import { useProductStore } from '../../store/product';
import Spinner from '../../components/core/Spinner.vue';
import CustomInput from '../../components/core/CustomInput.vue';
import router from '../../router';
import { useToast } from "vue-toastification"
import { TrashIcon } from '@heroicons/vue/24/outline';
import { useCartStore } from '../../store/cart'
const toast = useToast();
const purchaseStore = usePurchaseStore();
const supplierStore = useSupplierStore();
const route = useRoute();

const productStore = useProductStore()
const cartStore = useCartStore()

const searchResults = ref([]);
const searchQuery = ref('')


const loading = ref(false);
const DEFAULT_PURCHASE = {
  id: '',
  code: '',

};

const purchase = ref({ ...DEFAULT_PURCHASE });

onMounted(() => {
  if (route.params.id) {
    getPurchase(route.params.id);
  }
  supplierStore.getSuppliersForSelect();
});



const searchProducts = () => {
  cartStore.searchProducts(searchQuery.value);
};

watch(() => cartStore.searchResults, (results) => {
  searchResults.value = results;
});

const selectProduct = (product) => {
  cartStore.addItem(product);
  searchQuery.value = '';
  searchResults.value = [];
};

const incrementQuantity = (itemId) => {
  const item = cartStore.items.find(item => item.id === itemId);
  if (item) {
    cartStore.updateQuantity(itemId, item.quantity + 1);
  }
};

const decrementQuantity = (itemId) => {
  const item = cartStore.items.find(item => item.id === itemId);
  if (item && item.quantity > 1) {
    cartStore.updateQuantity(itemId, item.quantity - 1);
  }
};

const updateQuantity = (itemId, quantity) => {
  cartStore.updateQuantity(itemId, quantity);
};

const updateItemTotals = (item) => {
  cartStore.updateItemTotals(item);
};

const removeItem = (itemId) => {
  cartStore.removeItem(itemId);
};

const createPurchase = async () => {
  await purchaseStore.createPurchase();
};




const getPurchase = (id) => {
  loading.value = true;
  purchaseStore.getPurchase(id).then(({ data }) => {
    purchase.value = data;
    loading.value = false;
  });
};


const purchaseStatusOptions = ref([
  {
    value: 'receive',
    label: 'Receive'
  },
  {
    value: 'pending',
    label: 'Pending'
  },
  {
    value: 'ordered',
    label: 'Ordered'
  }

]);
const paymentTypeOptions = ref([
  {
    value: 'cash',
    label: 'Cash'
  },
  {
    value: 'debt',
    label: 'Debt'
  },

]);

const suppliersOptions = computed(() => {
  return supplierStore.suppliers.data.map(item => ({
    value: item.id,
    label: item.name

  }));
});

const discountTypeOptions = ref([
  {
    value: 'fixed',
    label: 'Fixed'
  },
  {
    value: 'percentage',
    label: 'Percentage(%)'
  },

]);


const onSubmit = () => {
  loading.value = true;
  if (purchase.value.id) {
    purchaseStore.updatePurchase(purchase.value).then((
      { data }
    ) => {
      loading.value = false;
      toast.success(data.message);
      router.push({ name: 'app.purchase_list' });
    }).catch(({ response }) => {
      loading.value = false;
      toast.error(response.data.message);
    });
  } else {
    purchaseStore.createPurchase(purchase.value).then(({ data }) => {
      loading.value = false;
      toast.success(data.message);
      router.push({ name: 'app.purchase_list' });
    }).catch(({ response }) => {
      loading.value = false;
      toast.error(response.data.message);
    });
  }
};

const goBack = () => {
  router.push({ name: 'app.purchase_list' });
};












</script>
