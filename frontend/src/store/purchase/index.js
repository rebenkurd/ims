

import { defineStore } from "pinia";
import { getPurchases, createPurchase, deletePurchase, getPurchase,updatePurchase,getPurchaseInvoice } from "@store/purchase/actions.js";
import state from "@store/purchase/state";

 export const usePurchaseStore = defineStore('PurchaseStore',{
    state: state,
    getters: {},
    actions: {
        getPurchases(url,search,perPage,sortField,sortDirection){
          return getPurchases(this,url,search,perPage,sortField,sortDirection);
        },
        createPurchase(purchase){
          return createPurchase(purchase);
        },
        updatePurchase(purchase){
          return updatePurchase(purchase);
        },
        deletePurchase(id){
          return deletePurchase(id);
        },
        getPurchase(id){
          return getPurchase(id);
        },
        getPurchaseInvoice(purchaseId, invoiceId){
          return getPurchaseInvoice(purchaseId, invoiceId);
        }
      }
      }

);
