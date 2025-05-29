

import { defineStore } from "pinia";
import { getBrands, createBrand, deleteBrand,updateBrand,getBrand,getBrandds } from "@/store/brand/actions.js";
import state from "@/store/brand/state.js";

 export const useBrandStore = defineStore('BrandStore',{
    state: state,
    getters: {},
    actions: {
        getBrands(url,search,perPage,sortField,sortDirection){
          return getBrands(this,url,search,perPage,sortField,sortDirection);
        },
        createBrand(brand){
          return createBrand(brand);
        },
        updateBrand(brand){
          return updateBrand(brand);
        },
        deleteBrand(id){
          return deleteBrand(id);
        },
        getBrand(id){
          return getBrand(id);
        },
        getBrandds(){
          return getBrandds(this);
        },
      }
      }

);
