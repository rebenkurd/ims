<template>
  <div>
    <div class="flex justify-between items-center mb-3">
      <h1 class="text-xl font-semibold">
        Products
      </h1>
      <button 
       type="button"
       class="primary-btn"
       @click="goToProductForm"
       >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New Product</span>
      </button>
    </div>
    <!-- <ProductModal v-model="showModal" :product="ProductModel" @close="onModalClose"/> -->
    <ProductsTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import ProductsTable from '@views/Products/ProductsTable.vue';
import ProductModal from '@views/Products/ProductModal.vue';
import { useRouter } from 'vue-router';

const router= useRouter()
const goToProductForm = () => {
  router.push({ name: 'app.product_form' });
}

const clickEdit = (product) => {
  router.push({
    name: 'app.product_form',
    params: { id: product.id }
  });
}

</script>

<style></style>