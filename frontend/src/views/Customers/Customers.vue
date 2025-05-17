<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-3">
      <h1 class="text-xl font-semibold">
        Customers
      </h1>
      <button 
        type="button"
        class="primary-btn w-full sm:w-auto"
        @click="goToCustomerForm"
      >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New Customer</span>
      </button>
    </div>
    <CustomersTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import CustomersTable from './CustomersTable.vue';
import { useRouter } from 'vue-router';

const router = useRouter()
const goToCustomerForm = () => {
  router.push({ name: 'app.customer_form' });
}

const clickEdit = (customer) => {
  router.push({
    name: 'app.customer_form',
    params: { id: customer.id }
  });
}
</script>

<style></style>