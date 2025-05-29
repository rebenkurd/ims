<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-3">
      <h1 class="text-xl font-semibold">
        Suppliers
      </h1>
      <button 
        type="button"
        class="primary-btn w-full sm:w-auto"
        @click="goToSupplierForm"
      >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New Supplier</span>
      </button>
    </div>
    <SuppliersTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import SuppliersTable from '@views/Suppliers/SuppliersTable.vue';
import { useRouter } from 'vue-router';

const router = useRouter()
const goToSupplierForm = () => {
  router.push({ name: 'app.supplier_form' });
}

const clickEdit = (supplier) => {
  router.push({
    name: 'app.supplier_form',
    params: { id: supplier.id }
  });
}
</script>

<style></style>