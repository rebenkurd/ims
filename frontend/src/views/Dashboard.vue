<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold">Dashboard</h1>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center p-8">
      <Spinner class="my-6" />
    </div>

    <!-- Dashboard Content -->
    <div v-else>
      <!-- Financial Summary Section -->
      <FinancialSummary :financial-data="financialSummary" />

      <!-- Original Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <SummaryCard 
          title="TOTAL SALES" 
          :value="summaryData.total_sales" 
          icon="currency-dollar"
        />
        <SummaryCard 
          title="TOTAL PURCHASES" 
          :value="summaryData.total_purchases" 
          icon="trending-up"
        />
        <SummaryCard 
          title="TOTAL CUSTOMERS" 
          :value="summaryData.total_customers" 
          icon="users"
        />
        <SummaryCard 
          title="TOTAL SUPPLIERS" 
          :value="summaryData.total_suppliers" 
          icon="truck"
        />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
        <!-- Sales Chart -->
        <div class="bg-white p-4 rounded-lg shadow">
          <SectionTitle title="MONTHLY SALES" />
          <SalesChart :data="salesChartData" />
        </div>

        <!-- Recently Added Products -->
        <div class="bg-white p-4 rounded-lg shadow">
          <SectionTitle title="RECENTLY ADDED PRODUCTS" />
          <div class="overflow-x-auto">
            <table class="table-auto w-full">
              <thead>
                <tr>
                  <th class="border-b-2 p-2 text-left">Code</th>
                  <th class="border-b-2 p-2 text-left">Name</th>
                  <th class="border-b-2 p-2 text-left">Category</th>
                  <th class="border-b-2 p-2 text-left">Price</th>
                  <th class="border-b-2 p-2 text-left">Stock</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in recentProducts" :key="product.id" class="animate-fade-in-down">
                  <td class="border-b-2 p-2">{{ product.code }}</td>
                  <td class="border-b-2 p-2">{{ product.name }}</td>
                  <td class="border-b-2 p-2">{{ product.category?.name || 'N/A' }}</td>
                  <td class="border-b-2 p-2">{{ formatCurrency(product.sales_price) }}</td>
                  <td class="border-b-2 p-2">{{ product.current_opening_stock }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
        <!-- Expired Products -->
        <div class="bg-white p-4 rounded-lg shadow" v-if="expiredProducts.length > 0">
          <SectionTitle title="EXPIRED PRODUCTS" />
          <div class="overflow-x-auto">
            <table class="table-auto w-full">
              <thead>
                <tr>
                  <th class="border-b-2 p-2 text-left">Code</th>
                  <th class="border-b-2 p-2 text-left">Name</th>
                  <th class="border-b-2 p-2 text-left">Expiry Date</th>
                  <th class="border-b-2 p-2 text-left">Stock</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in expiredProducts" :key="product.id" class="animate-fade-in-down">
                  <td class="border-b-2 p-2">{{ product.code }}</td>
                  <td class="border-b-2 p-2">{{ product.name }}</td>
                  <td class="border-b-2 p-2">{{ formatDate(product.expire_date) }}</td>
                  <td class="border-b-2 p-2">{{ product.current_opening_stock }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white p-4 rounded-lg shadow" v-if="lowStockProducts.length > 0">
          <SectionTitle title="LOW STOCK ALERTS" />
          <div class="overflow-x-auto">
            <table class="table-auto w-full">
              <thead>
                <tr>
                  <th class="border-b-2 p-2 text-left">Code</th>
                  <th class="border-b-2 p-2 text-left">Name</th>
                  <th class="border-b-2 p-2 text-left">Category</th>
                  <th class="border-b-2 p-2 text-left">Current Stock</th>
                  <th class="border-b-2 p-2 text-left">Minimum Qty</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="product in lowStockProducts" :key="product.id" class="animate-fade-in-down">
                  <td class="border-b-2 p-2">{{ product.code }}</td>
                  <td class="border-b-2 p-2">{{ product.name }}</td>
                  <td class="border-b-2 p-2">{{ product.category?.name || 'N/A' }}</td>
                  <td class="border-b-2 p-2">{{ product.current_opening_stock }}</td>
                  <td class="border-b-2 p-2">{{ product.minimum_qty }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useDashboardStore } from '../store/dashboard';
import FinancialSummary from '../components/dashboard/FinancialSummary.vue';
import SummaryCard from '../components/dashboard/SummaryCard.vue';
import SectionTitle from '../components/dashboard/SectionTitle.vue';
import SalesChart from '../components/dashboard/SalesChart.vue';
import Spinner from '../components/core/Spinner.vue';

const dashboardStore = useDashboardStore();
const loading = ref(true);
const summaryData = ref({});
const financialSummary = ref({});
const salesChartData = ref({});
const recentProducts = ref([]);
const expiredProducts = ref([]);
const lowStockProducts = ref([]);

onMounted(async () => {
  try {
    await dashboardStore.fetchDashboardData();
    summaryData.value = dashboardStore.summaryData;
    financialSummary.value = dashboardStore.financialSummary;
    salesChartData.value = formatChartData(dashboardStore.salesData);
    recentProducts.value = dashboardStore.recentProducts;
    expiredProducts.value = dashboardStore.expiredProducts;
    lowStockProducts.value = dashboardStore.lowStockProducts;
  } catch (error) {
    console.error('Failed to load dashboard data:', error);
  } finally {
    loading.value = false;
  }
});

const formatChartData = (salesData) => {
  return {
    labels: salesData.months,
    datasets: [
      {
        label: 'Sales',
        data: salesData.amounts,
        backgroundColor: '#3B82F6',
      }
    ]
  };
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-GB');
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
};
</script>