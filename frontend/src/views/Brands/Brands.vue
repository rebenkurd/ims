
<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-3">
      <h1 class="text-xl font-semibold">
        Brands
      </h1>
      <button 
        type="button"
        class="primary-btn w-full sm:w-auto"
        @click="goToBrandForm"
      >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New Brand</span>
      </button>
    </div>
    <BrandTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import BrandTable from '@views/Brands/BrandsTable.vue';
import { useRouter } from 'vue-router';

const router= useRouter()
const goToBrandForm = () => {
  router.push({ name: 'app.brand_form' });
}

const clickEdit = (brand) => {
  router.push({
    name: 'app.brand_form',
    params: { id: brand.id }
  });
}

</script>

<style></style>