

import { defineStore } from "pinia";
import { getSuppliers, createSupplier, deleteSupplier,updateSupplier,getSupplier,getSuppliersForSelect } from "@store/suppliers/actions.js";
import state from "@store/suppliers/state.js";

 export const useSupplierStore = defineStore('SupplierStore',{
    state: state,
    getters: {},
    actions: {
        getSuppliers(url,search,perPage,sortField,sortDirection){
          return getSuppliers(this,url,search,perPage,sortField,sortDirection);
        },
        createSupplier(supplier){
          return createSupplier(supplier);
        },
        updateSupplier(supplier){
          return updateSupplier(supplier);
        },
        deleteSupplier(id){
          return deleteSupplier(id);
        },
        getSupplier(id){
          return getSupplier(id);
        },
        getSuppliersForSelect(){
          return getSuppliersForSelect(this);
        },
      }
      }

);
