

import { defineStore } from "pinia";
import { getUsers, createUser, deleteUser,updateUser,getUser } from "@store/user/actions.js";
import state from "@store/user/state";

 export const useUserStore = defineStore('UserStore',{
    state: state,
    getters: {},
    actions: {
        getUsers(url,search,perPage,sortField,sortDirection){
          return getUsers(this,url,search,perPage,sortField,sortDirection);
        },
        createUser(user){
          return createUser(user);
        },
        updateUser(user){
          return updateUser(user);
        },
        deleteUser(id){
          return deleteUser(id);
        },
        getUser(id){
          return getUser(id);
        },
      }
      }

);
