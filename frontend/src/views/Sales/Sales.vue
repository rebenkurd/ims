<template>
  <div>
    <div class="flex justify-between items-center mb-3">
      <h1 class="text-xl font-semibold">
        Sales
      </h1>
      <button 
       type="button"
       class="primary-btn"
       @click="goToSaleForm"
       >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New Sale</span>
      </button>
    </div>
    <SalesTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import SalesTable from '@views/Sales/SalesTable.vue';
import { useRouter } from 'vue-router';

const router= useRouter()
const goToSaleForm = () => {
  router.push({ name: 'app.sale_form' });
}

const clickEdit = (sale) => {
  router.push({
    name: 'app.sale_form',
    params: { id: sale.id }
  });
}
</script>

<style></style>