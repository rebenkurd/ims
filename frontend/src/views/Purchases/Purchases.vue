<template>
  <div>
    <div class="flex justify-between items-center mb-3">
      <h1 class="text-xl font-semibold">
        Purchases
      </h1>
      <button 
       type="button"
       class="primary-btn"
       @click="goToPurchaseForm"
       >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New Purchase</span>
      </button>
    </div>
    <!-- <PurchaseModal v-model="showModal" :purchase="PurchaseModel" @close="onModalClose"/> -->
    <PurchasesTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import PurchasesTable from './PurchasesTable.vue';
import PurchaseModal from './PurchaseModal.vue';
import { useRouter } from 'vue-router';

const router= useRouter()
const goToPurchaseForm = () => {
  router.push({ name: 'app.purchase_form' });
}

const clickEdit = (purchase) => {
  router.push({
    name: 'app.purchase_form',
    params: { id: purchase.id }
  });
}

</script>

<style></style>