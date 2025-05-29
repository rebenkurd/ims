import { defineStore } from 'pinia';
import axiosClient from '@/axios';

export const usePaymentStore = defineStore('payment', {
  state: () => ({
    payments: [],
    loading: false
  }),
  
  actions: {
    async fetchPayments(purchaseId) {
      this.loading = true;
      try {
        const response = await axiosClient.get(`/purchases/${purchaseId}/payments`);
        this.payments = response.data;
      } catch (error) {
        console.error('Error fetching payments:', error);
      } finally {
        this.loading = false;
      }
    },
    
    async deletePayment(paymentId) {
      this.loading = true;
      try {
        await axiosClient.delete(`/payments/${paymentId}`);
        return true;
      } catch (error) {
        console.error('Error deleting payment:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    clearPayments() {
      this.payments = [];
    }
  }
});