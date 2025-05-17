import { defineStore } from "pinia";
import { getCustomers, createCustomer, deleteCustomer, updateCustomer, getCustomer, getCustomersForSelect } from "./actions.js";
import state from "./state.js";

export const useCustomerStore = defineStore('CustomerStore', {
    state: state,
    getters: {},
    actions: {
        getCustomers(url, search, perPage, sortField, sortDirection) {
            return getCustomers(this, url, search, perPage, sortField, sortDirection);
        },
        createCustomer(customer) {
            return createCustomer(customer);
        },
        updateCustomer(customer) {
            return updateCustomer(customer);
        },
        deleteCustomer(id) {
            return deleteCustomer(id);
        },
        getCustomer(id) {
            return getCustomer(id);
        },
        getCustomersForSelect() {
            return getCustomersForSelect(this);
        },
    }
});