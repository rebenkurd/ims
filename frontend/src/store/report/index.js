import { defineStore } from 'pinia';
import state from '@store/report/state';
import getters from '@store/report/getters';
import * as actions from '@store/report/actions';

export const useReportStore = defineStore('report', {
  state,
  getters,
  actions: {
    fetchPurchases(url = null) {
      return actions.fetchPurchases(this, url);
    },
    fetchSales(url = null) {
      return actions.fetchSales(this, url);
    },
    fetchExpiredProducts() {
      return actions.fetchExpiredProducts(this);
    },
    fetchWorldBankReports(url = null) {
      return actions.fetchWorldBankReports(this, url);
    },
    updatePagination(responseData) {
      return actions.updatePagination(this, responseData);
    },
    updateFilters(newFilters) {
      return actions.updateFilters(this, newFilters);
    },
    resetFilters() {
      return actions.resetFilters(this);
    },
    changePage(url) {
      return actions.changePage(this, url);
    }
  }
});