

import { defineStore } from "pinia";
import { getCategories, createCategory, deleteCategory,updateCategory,getCategory,getCategorys } from "./actions.js";
import state from "./state.js";

 export const useCategoryStore = defineStore('CategoryStore',{
    state: state,
    getters: {},
    actions: {
        getCategories(url,search,perPage,sortField,sortDirection){
          return getCategories(this,url,search,perPage,sortField,sortDirection);
        },
        createCategory(category){
          return createCategory(category);
        },
        updateCategory(category){
          return updateCategory(category);
        },
        deleteCategory(id){
          return deleteCategory(id);
        },
        getCategory(id){
          return getCategory(id);
        },
        getCategorys(){
          return getCategorys(this);
        },
      }
      }

);
