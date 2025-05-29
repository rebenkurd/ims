import { defineStore } from 'pinia';
import axiosClient from '@/axios';

export const useDashboardStore = defineStore('dashboard', {
  state: () => ({
    loading: false,
    summaryData: {},
    financialSummary: {},
    salesData: {},
    recentProducts: [],
    expiredProducts: [],
    lowStockProducts: []
  }),

  actions: {
    async fetchDashboardData() {
      this.loading = true;
      try {
        const response = await axiosClient.get('/dashboard');
        this.summaryData = response.data.summary;
        this.financialSummary = response.data.financialSummary || {
          total_purchase_due: 307144.00,
          todays_purchase: 310475.00,
          total_sales_due: 4385.00,
          today_payment_received: 345935.70,
          total_sales_amount: 338660.70,
          todays_sales: 350320.70,
          purchase_due_trend: 'up',
          todays_purchase_trend: 'up',
          sales_due_trend: 'down',
          payment_received_trend: 'up',
          sales_amount_trend: 'up',
          todays_sales_trend: 'up'
        };
        this.salesData = response.data.sales;
        this.recentProducts = response.data.recentProducts;
        this.expiredProducts = response.data.expiredProducts;
        this.lowStockProducts = response.data.lowStockProducts;
      } catch (error) {
        console.error('Failed to fetch dashboard data:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    }
  }
});