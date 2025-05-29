import { defineStore } from "pinia";
import { getSales, createSale, deleteSale, getSale, updateSale, getSaleInvoice } from "@store/sale/actions.js";
import state from "@store/sale/state";

export const useSaleStore = defineStore('SaleStore', {
  state: state,
  getters: {},
  actions: {
    getSales(url, search, perPage, sortField, sortDirection) {
      return getSales(this, url, search, perPage, sortField, sortDirection);
    },
    createSale(sale) {
      return createSale(sale);
    },
    updateSale(sale) {
      return updateSale(sale);
    },
    deleteSale(id) {
      return deleteSale(id);
    },
    getSale(id) {
      return getSale(id);
    },
    getSaleInvoice(saleId, invoiceId) {
      return getSaleInvoice(saleId, invoiceId);
    }
  }
});