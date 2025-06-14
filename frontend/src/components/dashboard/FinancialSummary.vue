<template>
  <div class="financial-summary">
    <SectionTitle title="" />
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <!-- Total Purchase Due -->
 
      <div v-if="store.user.role_id === 1 || store.user.role_id === 3"
       class="financial-card bg-gradient-to-br from-blue-50 to-blue-100 border-l-4 border-blue-500">
        <div class="financial-icon bg-blue-100 text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <h3 class="financial-title">Total Purchase Due</h3>
          <p class="financial-amount">{{ formatCurrency(financialData.total_purchase_due) }}</p>
        </div>
        <TrendIndicator :value="financialData.purchase_due_trend" />
      </div>

      <!-- Today's Total Purchase -->
      <div 
      v-if="store.user.role_id === 1 || store.user.role_id === 3"
      class="financial-card bg-gradient-to-br from-purple-50 to-purple-100 border-l-4 border-purple-500">
        <div class="financial-icon bg-purple-100 text-purple-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <div>
          <h3 class="financial-title">Today's Purchases</h3>
          <p class="financial-amount">{{ formatCurrency(financialData.todays_purchase) }}</p>
        </div>
        <TrendIndicator :value="financialData.todays_purchase_trend" />
      </div>

      <!-- Total Sales Due -->
      <div
      v-if="store.user.role_id === 1 || store.user.role_id === 2"
       class="financial-card bg-gradient-to-br from-amber-50 to-amber-100 border-l-4 border-amber-500">
        <div class="financial-icon bg-amber-100 text-amber-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
          </svg>
        </div>
        <div>
          <h3 class="financial-title">Total Sales Due</h3>
          <p class="financial-amount">{{ formatCurrency(financialData.total_sales_due) }}</p>
        </div>
        <TrendIndicator :value="financialData.sales_due_trend" />
      </div>

      <!-- Today Payment Received -->
      <div 
      v-if="store.user.role_id === 1"
      class="financial-card bg-gradient-to-br from-green-50 to-green-100 border-l-4 border-green-500">
        <div class="financial-icon bg-green-100 text-green-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </div>
        <div>
          <h3 class="financial-title">Today's Payments</h3>
          <p class="financial-amount">{{ formatCurrency(financialData.today_payment_received) }}</p>
        </div>
        <TrendIndicator :value="financialData.payment_received_trend" />
      </div>

      <!-- Total Sales Amount -->
      <div
      v-if="store.user.role_id === 1 || store.user.role_id === 2"
       class="financial-card bg-gradient-to-br from-cyan-50 to-cyan-100 border-l-4 border-cyan-500">
        <div class="financial-icon bg-cyan-100 text-cyan-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        </div>
        <div>
          <h3 class="financial-title">Total Sales</h3>
          <p class="financial-amount">{{ formatCurrency(financialData.total_sales_amount) }}</p>
        </div>
        <TrendIndicator :value="financialData.sales_amount_trend" />
      </div>

      <!-- Today's Total Sales -->
      <div
      
      v-if="store.user.role_id === 1 || store.user.role_id === 2"
       class="financial-card bg-gradient-to-br from-indigo-50 to-indigo-100 border-l-4 border-indigo-500">
        <div class="financial-icon bg-indigo-100 text-indigo-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
        </div>
        <div>
          <h3 class="financial-title">Today's Sales</h3>
          <p class="financial-amount">{{ formatCurrency(financialData.todays_sales) }}</p>
        </div>
        <TrendIndicator :value="financialData.todays_sales_trend" />
      </div>
    </div>
  </div>
</template>
<script setup>
import { defineProps } from 'vue';
import SectionTitle from '@components/dashboard/SectionTitle.vue';
import TrendIndicator from '@components/dashboard/TrendIndicator.vue';
import { useStore } from '@store/';

const store = useStore();

const props = defineProps({
  financialData: {
    type: Object,
    required: true,
    default: () => ({
      total_purchase_due: 0,
      todays_purchase: 0,
      total_sales_due: 0,
      today_payment_received: 0,
      total_sales_amount: 0,
      todays_sales: 0,
      purchase_due_trend: 'neutral',
      todays_purchase_trend: 'neutral',
      sales_due_trend: 'neutral',
      payment_received_trend: 'neutral',
      sales_amount_trend: 'neutral',
      todays_sales_trend: 'neutral'
    })
  }
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);
};
</script>

<style scoped>

</style>