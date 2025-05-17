
<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-3">
      <h1 class="text-xl font-semibold">
        Categories
      </h1>
      <button 
        type="button"
        class="primary-btn w-full sm:w-auto"
        @click="goToCategoryForm"
      >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New Category</span>
      </button>
    </div>
    <CategoriesTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import CategoriesTable from './CategoriesTable.vue';
import { useRouter } from 'vue-router';

const router= useRouter()
const goToCategoryForm = () => {
  router.push({ name: 'app.category_form' });
}

const clickEdit = (category) => {
  router.push({
    name: 'app.category_form',
    params: { id: category.id }
  });
}

</script>

<style></style>