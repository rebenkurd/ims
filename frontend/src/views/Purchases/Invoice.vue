<template>
  <div id="invoice-print">
  <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl mx-auto">
    <!-- Invoice Header -->
    <div class="flex justify-between items-start mb-8">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">PURCHASE INVOICE</h1>
        <p class="text-gray-600 text-sm">Invoice <span class="font-bold ">#{{ invoice.invoice_number }}</span></p>
      </div>
      <div class="text-right">
        <div class="flex items-center justify-end mb-2">
          <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
            {{ invoice.payment_status }}
          </span>
        </div>
        <p class="text-gray-600">Date: {{ formatDate(purchase.created_at) }}</p>
        <!-- <p class="text-gray-600">Due Date: {{ formatDate(invoice.due_date) }}</p> -->
      </div>
    </div>

    <!-- Supplier and Company Info -->
    <div class="grid grid-cols-2 gap-8 mb-8">
      <div class="">
        <h2 class="text-lg font-semibold mb-2">Supplier:</h2>
        <p class="font-medium">{{ purchase.supplier.name }}</p>
        <p class="text-gray-600">{{ purchase.supplier.phone }}</p>
        <p class="text-gray-600">{{ purchase.supplier.address }}</p>
      </div>
      <div class="text-right">
        <h2 class="text-lg font-semibold mb-2">Your Company:</h2>
        <p class="font-medium">{{ company.company_name }}</p>
        <p class="text-gray-600">{{ company.address }}</p>
        <p class="text-gray-600">{{ company.city }} , {{ company.state }} {{ company.postcode }} </p>
        <p class="text-gray-600">Phone: {{ company.phone }} </p>
      </div>
    </div>

    <!-- Purchase Details -->
    <div class="mb-6">
      <h2 class="text-lg font-semibold mb-2">Purchase Details:</h2>
      <p><span class="font-medium">Reference:</span> {{ purchase.reference_no }}</p>
      <p><span class="font-medium">Purchase Code:</span> {{ purchase.purchase_code }}</p>
      <p><span class="font-medium">Status:</span> {{ purchase.purchase_status }}</p>
    </div>

    <!-- Items Table -->
    <div class="mb-8 overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Discount</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(item, index) in purchase.items" :key="item.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ item.product.name }}</div>
              <div class="text-sm text-gray-500">{{ item.product.barcode }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.quantity }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(item.unit_price) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(item.discount) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(item.total_price) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Totals -->
    <div class="grid grid-cols-2 gap-8">
      <div>
        <h3 class="text-lg font-semibold mb-2">Notes:</h3>
        <p class="text-gray-600">{{ purchase.note || 'No notes available' }}</p>
      </div>
      <div>
        <div class="flex justify-between mb-2">
          <span class="font-medium">Subtotal:</span>
          <span>{{ formatCurrency(purchase.subtotal) }}</span>
        </div>
        <div class="flex justify-between mb-2">
          <span class="font-medium">Discount:</span>
          <span>{{ purchase.discount_type=="fixed"?formatCurrency(invoice.discount):"%"+invoice.discount }}</span>
        </div>
        <!-- <div class="flex justify-between mb-2">
          <span class="font-medium">Tax:</span>
          <span>{{ formatCurrency(invoice.tax) }}</span>
        </div> -->
        <div class="flex justify-between text-lg font-bold border-t pt-2 mt-2">
          <span>Total:</span>
          <span>{{ formatCurrency(invoice.final_amount) }}</span>
        </div>
      </div>
    </div>

    <!-- Payments -->
    <div class="mt-8" v-if="purchase.payments && purchase.payments.length > 0">
      <h3 class="text-lg font-semibold mb-4">Payment History:</h3>
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Method</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Amount</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="payment in purchase.payments" :key="payment.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(payment.payment_date) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ payment.payment_method }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ payment.note || '-' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatCurrency(payment.amount) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Invoice Footer -->
    <div class="mt-12 pt-6 border-t border-gray-200 flex justify-between">
      <div>
        <p class="text-sm text-gray-500">Thank you for your business!</p>
      </div>
      <div class="flex space-x-4">

        <router-link :to="{name:'app.purchase_list'}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Go Back</router-link>
        <!-- <button @click="downloadPDF" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
          Download PDF
        </button> -->
        <button @click="printInvoice" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Print Invoice
        </button>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { usePurchaseStore } from '@store/purchase';
import { useToast } from "vue-toastification";
import { useCompanyProfileStore } from '@store/company_profile';

const toast = useToast();
const router = useRouter();
const route = useRoute();
const purchaseStore = usePurchaseStore();
const companyProfileStore = useCompanyProfileStore();

const purchase = ref({
  supplier: {},
  items: [],
  payments: []
});

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



const invoice = ref({});
const loading = ref(false);

onMounted(() => {
  fetchInvoiceData();
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
const fetchInvoiceData = async () => {
  try {
    loading.value = true;
    const response = await purchaseStore.getPurchaseInvoice(
      route.params.purchaseId,
      route.params.invoiceId
    );

    if (!response.invoice) {
      toast.error("Invoice not found for this purchase");
      router.push({ name: 'app.purchase_list' });
      return;
    }

    purchase.value = response.purchase;
    purchase.value.items = response.purchase.purchase_items;
    purchase.value.supplier = response.purchase.supplier;
    purchase.value.payments = response.purchase.payments;
    invoice.value = response.invoice;
  } catch (error) {
    console.error('Invoice Error Details:', error);
    
    let errorMessage = "Failed to load invoice data";
    if (error.response) {
      if (error.response.status === 404) {
        errorMessage = "Invoice not found for this purchase";
      } else if (error.response.data?.message) {
        errorMessage = error.response.data.message;
      }
    }
    
    toast.error(errorMessage);
    router.push({ name: 'app.purchase_list' });
  } finally {
    loading.value = false;
  }
};

console.log(purchase);

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
};

const printInvoice = () => {
  if (loading.value) {
    toast.info("Please wait while the invoice loads");
    return;
  }
  window.print();
};

const downloadPDF = () => {
  // This would be implemented with a PDF generation library or API call
  toast.info("PDF download functionality will be implemented");
};
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  
  #invoice-print,
  #invoice-print * {
    visibility: visible;
  }
  
  #invoice-print {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    margin: 0;
    padding: 0;
    background: white;
  }
  
  /* Optional: Improve print layout */
  @page {
    size: auto;
    margin: 0mm;
  }
  
  body {
    padding: 0;
    margin: 0;
    background: white;
  }
  
  /* Hide print button when printing */
  button {
    display: none !important;
  }
}
</style>