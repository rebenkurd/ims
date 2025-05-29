

import { defineStore } from "pinia";
import { login, logout, getUser } from "@store/actions";
import state from "@store/state";

 export const useStore = defineStore('store',{
    state: state,
    getters: {},
    actions: {
        login(data) {
          return login(this,data);
        },
        logout() {
          return logout(this.user);
        },
        getUser() {
          return getUser(this);
        },

      }
      }

);
