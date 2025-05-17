

import { defineStore } from "pinia";
import { getSuppliers, createSupplier, deleteSupplier,updateSupplier,getSupplier,getSuppliersForSelect } from "./actions.js";
import state from "./state.js";

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
