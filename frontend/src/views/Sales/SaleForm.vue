<template>
  <div>
    <Spinner v-if="loading" class="mx-auto" />
    <div v-else class="bg-white p-4 rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">
        {{ sale.id ? `Edit Sale` : "Create New Sale" }}
      </h2>

      <form @submit.prevent="onSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-2.5">
          <CustomInput type="select" :addModal="true" :searchable="true" v-model="sale.customer_id"
            label="Customer Name" :options="customersOptions" />
          <CustomInput type="date" v-model="sale.created_at" label="Sale Date" class="mb-2" :required="true" />
          <CustomInput type="select" v-model="sale.sale_status" label="Sale Status"
            :options="saleStatusOptions" class="mb-2" />
          <CustomInput v-model="sale.reference_no" label="Reference No." class="mb-2" />
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
                  <th class="border-b-2 p-2">Sale Price</th>
                  <th class="border-b-2 p-2">Discount</th>
                  <th class="border-b-2 p-2">Unit Price</th>
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
                    <input v-model="item.salePrice"
                      class="w-24 mx-1 rounded-md bg-white pl-8 pr-3 py-2 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-gray-400 focus:border-gray-400 focus:z-10 sm:text-sm"
                      @change="updateItemTotals(item)" />
                  </td>
                  <td class="border-b-2 p-2">
                    <input type="number" v-model="item.discount"
                      class="w-20 mx-1 rounded-md bg-white pl-8 pr-3 py-2 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-gray-400 focus:border-gray-400 focus:z-10 sm:text-sm"
                      @change="updateItemTotals(item)" />
                  </td>
                  <td class="border-b-2 p-2">{{ (item.unitPrice || 0).toFixed(2) }}</td>
                  <td class="border-b-2 p-2">{{ (item.totalAmount || 0).toFixed(2) }}</td>
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
                <CustomInput type="number" v-model="cartStore.discountAll" class="ml-2" />
                <CustomInput type="select" v-model="cartStore.discountType" class="ml-2"
                  :options="discountTypeOptions" />
              </div>

              <div class="flex mb-4">
                <CustomInput type="textarea" v-model="cartStore.note" label="Note" class="mb-2 w-full" />
              </div>
            </div>

            <!-- Right side - Totals -->
            <div class="w-1/2 pl-8">
              <div class="flex justify-between mb-2">
                <div class="font-semibold">Subtotal</div>
                <div>$ {{ (cartStore.subtotal || 0).toFixed(2) }}</div>
              </div>

              <div class="flex justify-between mb-2">
                <div class="font-semibold">Discount on All</div>
              <div>
                {{ sale.discount_type !== 'fixed' ? '%': '$' }} 
                {{ 
                  (cartStore.discountType === 'Per%' ? 
                  (cartStore.subtotal || 0) * (Number(cartStore.discountAll)) / 100 : Number(cartStore.discountAll || 0)).toFixed(2) }}</div>
              </div>

              <div class="flex justify-between font-bold text-lg">
                <div>Grand Total</div>
                <div>$ {{ (cartStore.grandTotal || 0).toFixed(2) }}</div>
              </div>

              <div v-if="cartStore.payments.length > 0" class="mt-4 border-t pt-2">
                <div class="flex justify-between mb-2">
                  <div class="font-semibold">Paid Amount</div>
                  <div>$ {{ cartStore.paidAmount.toFixed(2) }}</div>
                </div>
                <div class="flex justify-between font-bold text-lg">
                  <div>Balance Due</div>
                  <div>$ {{ cartStore.balanceDue.toFixed(2) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <hr class="text-gray-200 my-4">
        
        <!-- Previous Payments Section -->
        <div v-if="cartStore.payments.length > 0">
          <h1 class="text-lg m-2 font-bold">Previous Payments:</h1>
          <table class="table-auto w-full mb-4">
            <thead class="bg-gray-400">
              <tr>
                <th class="border-b-2 p-2 text-left text-white">#</th>
                <th class="border-b-2 p-2 text-left text-white">Date</th>
                <th class="border-b-2 p-2 text-left text-white">Payment Type</th>
                <th class="border-b-2 p-2 text-left text-white">Payment Note</th>
                <th class="border-b-2 p-2 text-left text-white">Amount</th>
                <th class="border-b-2 p-2 text-left text-white">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(payment, index) in cartStore.payments" :key="index">
                <td class="border-b-2 p-2">{{ index + 1 }}</td>
                <td class="border-b-2 p-2">{{ payment.date }}</td>
                <td class="border-b-2 p-2">{{ payment.payment_type }}</td>
                <td class="border-b-2 p-2">{{ payment.payment_note }}</td>
                <td class="border-b-2 p-2">$ {{ parseFloat(payment.amount).toFixed(2) }}</td>
                <td class="border-b-2 p-2">
                  <TrashIcon @click="cartStore.removePayment(index)" 
                    class="h-5 w-5 text-red-400 cursor-pointer" aria-hidden="true" />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Payment Section -->
        <div v-if="!sale.id || cartStore.balanceDue > 0" class="shadow bg-gray-200 p-4 rounded-md">
          <h1 class="text-lg font-bold mb-4">Make Payment:</h1>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2.5">
            <CustomInput type="number" v-model="cartStore.currentPayment.amount" 
              label="Amount" class="mb-2"  
              :max="cartStore.balanceDue > 0 ? cartStore.balanceDue : cartStore.grandTotal" />
            <CustomInput type="select" v-model="cartStore.currentPayment.payment_type" 
              label="Payment Type" :options="paymentTypeOptions" class="mb-2" />
            <CustomInput type="date" v-model="cartStore.currentPayment.date" 
              label="Payment Date" class="mb-2" />
            <CustomInput type="textarea" v-model="cartStore.currentPayment.payment_note" 
              label="Payment Note" class="mb-2 col-span-2" />
          </div>
          <button type="button" @click="cartStore.addPayment()" 
            class="mt-2 py-2 px-4 bg-blue-600 text-white rounded hover:bg-blue-700">
            Add Payment
          </button>
        </div>

        <hr class="text-gray-200 my-4">
        <div class="flex justify-center items-center space-x-2">
          <button type="submit" class="py-2 px-10 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ sale.id ? 'Update' : 'Save' }}
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
import { useSaleStore } from '@store/sale';
import { useCustomerStore } from '@store/customers';
import { useProductStore } from '@store/product';
import Spinner from '@components/core/Spinner.vue';
import CustomInput from '@components/core/CustomInput.vue';
import router from '@router';
import { useToast } from "vue-toastification"
import { TrashIcon } from '@heroicons/vue/24/outline';
import { useCartStore } from '@store/cart'
import { watchEffect } from 'vue';

const toast = useToast();
const saleStore = useSaleStore();
const customerStore = useCustomerStore();
const route = useRoute();
const productStore = useProductStore()
const cartStore = useCartStore()

const searchResults = ref([]);
const searchQuery = ref('')
const loading = ref(false);

const DEFAULT_SALE = {
  id: '',
  code: '',
  total_quantities: 0,
  discount_all: 0,
  discount_type: '',
  note: '',
  customer_id: '',
  created_at: new Date().toISOString().split('T')[0],
  reference_no: '',
  subtotal: 0,
  due_balance: 0,
  total: 0,
  sale_status: 'pending',
  items: [],
  payments: []
};

const sale = ref({ ...DEFAULT_SALE });

onMounted(() => {
  sale.value.items = [];
  
  if (route.params.id) {
    getSale(route.params.id);
  } else{
    resetForm();
    cartStore.resetInvoice();
  }
  customerStore.getCustomersForSelect();
});

const resetForm = () => {
    sale.value = { ...DEFAULT_SALE };
    cartStore.resetInvoice();
};

const searchProducts = () => {
  cartStore.searchProducts(searchQuery.value);
};

watch(() => cartStore.searchResults, (results) => {
  searchResults.value = results;
});

const selectProduct = (product) => {
  cartStore.addItem(product);
  updateSaleItems();
  searchQuery.value = '';
  searchResults.value = [];
};

const updateSaleItems = () => {
  sale.value.items = cartStore.items.map(item => {
    return {
      product_id: item.id,
      product_name: item.name,
      quantity: parseInt(item.quantity),
      sale_price: parseFloat(item.salePrice),
      discount: parseFloat(item.discount || 0),
      unit_price: parseFloat(item.unitPrice),
      total_amount: parseFloat(item.totalAmount)
    };
  });
};

const removeItem = (itemId) => {
  cartStore.removeItem(itemId);
  updateSaleItems();
};

const incrementQuantity = (itemId) => {
  const item = cartStore.items.find(item => item.id === itemId);
  if (item) {
    cartStore.updateQuantity(itemId, item.quantity + 1);
    updateSaleItems();
  }
};

const decrementQuantity = (itemId) => {
  const item = cartStore.items.find(item => item.id === itemId);
  if (item && item.quantity > 1) {
    cartStore.updateQuantity(itemId, item.quantity - 1);
    updateSaleItems();
  }
};

const updateQuantity = (itemId, quantity) => {
  cartStore.updateQuantity(itemId, quantity);
  updateSaleItems();
};

const updateItemTotals = (item) => {
  cartStore.updateItemTotals(item);
  updateSaleItems();
};

const getSale = (id) => {
  loading.value = true;
  saleStore.getSale(id).then(({ data }) => {
    sale.value = data;
    sale.value.items = data.sale_items;
    sale.value.discount_all = parseFloat(data.discount);
    sale.value.created_at = data.created_at.split('T')[0];

    // payment
    sale.value.payments = data.payments.map(payment => ({
      amount: payment.amount,
      payment_type: payment.payment_method,
      payment_note: payment.note,
      date: payment.payment_date
    }))

    cartStore.syncWithSale(data);
    loading.value = false;
  }).catch(error => {
    console.error("Error loading sale:", error);
    loading.value = false;
    toast.error("Error loading sale data");
  });
};

const saleStatusOptions = ref([
  { value: 'completed', label: 'Completed' },
  { value: 'pending', label: 'Pending' },
  // { value: 'returned', label: 'Returned' }
]);

const paymentTypeOptions = ref([
  { value: 'cash', label: 'Cash' },
]);

const customersOptions = computed(() => {
  return customerStore.customers.data.map(item => ({
    value: item.id,
    label: item.name
  }));
});

const discountTypeOptions = ref([
  { value: 'per', label: 'Percentage(%)' },
  { value: 'fixed', label: 'Fixed' },
]);

const prepareSaleData = () => {
  const saleData = {
    ...sale.value,
    total_quantities: cartStore.totalQuantities,
    discount_all: parseFloat(cartStore.discountAll || 0),
    discount_type: cartStore.discountType,
    note: cartStore.note,
    total: cartStore.grandTotal,
    payments: cartStore.payments.map(payment => ({
      amount: payment.amount,
      payment_type: payment.payment_type,
      payment_note: payment.payment_note,
      date: payment.date
    }))
  };
  
  return saleData;
};

const onSubmit = async () => {
  try {
    if (cartStore.items.length === 0) {
      throw new Error("Please add at least one item to the sale");
    }
    
    if (!sale.value.customer_id) {
      throw new Error("Please select a customer");
    }
    
    loading.value = true;
    const saleData = prepareSaleData();
    
    if (sale.value.id) {
      // Update existing sale
      const { data } = await saleStore.updateSale(saleData);
      toast.success(data.message);
      cartStore.resetInvoice();
      router.push({ name: 'app.sale_list' });
    } else {
      // Create new sale
      const { data } = await saleStore.createSale(saleData);
      toast.success(data.message);
      
      // Verify we have invoice data before redirecting
      if (data.invoice?.id) {
        router.push({
          name: 'app.sale_invoice',
          params: {
            saleId: data.data.id,
            invoiceId: data.invoice.id
          }
        });
      } else {
        throw new Error("Invoice was not created");
      }
    }
  } catch (error) {
    toast.error(error.response?.data?.message || error.message);
    // Don't reset cart if we want to allow user to try again
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  resetForm();
  cartStore.resetInvoice();
  router.push({ name: 'app.sale_list' });
};

watch(
  [
    () => cartStore.totalQuantities,
    () => cartStore.discountAll,
    () => cartStore.discountType,
    () => cartStore.note,
    () => cartStore.grandTotal,
    () => cartStore.subtotal,
    () => cartStore.balanceDue
  ],
  ([newTotalQuantities, newDiscountAll, newDiscountType, newNote, newGrandTotal, newSubtotal, newDueBalance]) => {
    sale.value.total_quantities = newTotalQuantities;
    sale.value.discount_all = newDiscountAll;
    sale.value.discount_type = newDiscountType;
    sale.value.note = newNote;
    sale.value.total = newGrandTotal;
    sale.value.subtotal = newSubtotal;
    sale.value.due_balance = newDueBalance;
  }
);


watchEffect(() => {
  sale.value.items = cartStore.items.map(item => {
    return {
      product_id: item.id,
      quantity: parseInt(item.quantity),
      sale_price: parseFloat(item.salePrice),
      discount: parseFloat(item.discount || 0),
      unit_price: parseFloat(item.unitPrice),
      total_amount: parseFloat(item.totalAmount),
    };
  });
});

watch(() => route.params.id, (newId) => {
    if (newId) {
        getSale(newId);
    } else {
        resetForm();
        cartStore.resetInvoice();
    }
});

</script>