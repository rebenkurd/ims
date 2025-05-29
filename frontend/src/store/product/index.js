

import { defineStore } from "pinia";
import { getProducts, createProduct, deleteProduct, getProduct,updateProduct,updateProductStatus ,fetchItems,searchItems,clearSearch} from "@store/product/actions.js";
import state from "@store/product/state";

 export const useProductStore = defineStore('ProductStore',{
    state: state,
    getters: {
      getItemById: (state) => (id) => {
        return state.items.find(item => item.id === id)
      }
    },
    actions: {
        getProducts(url,search,perPage,sortField,sortDirection){
          return getProducts(this,url,search,perPage,sortField,sortDirection);
        },
        createProduct(product){
          return createProduct(product);
        },
        updateProduct(product){
          return updateProduct(product);
        },
        deleteProduct(id){
          return deleteProduct(id);
        },
        getProduct(id){
          return getProduct(id);
        },
        updateProductStatus(product){
          return updateProductStatus(product);
        },
        fetchItems(){
         return fetchItems(this);
        },
        searchItems(query){
         return searchItems(this,query);
        },
        clearSearch(){
         return clearSearch(this);
      }
    }
  }

);
