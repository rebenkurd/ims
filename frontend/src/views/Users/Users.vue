
<template>
  <div>
    <div class="flex flex-col sm:flex-row justify-between items-center mb-3 gap-3">
      <h1 class="text-xl font-semibold">
        Users
      </h1>
      <button 
        type="button"
        class="primary-btn w-full sm:w-auto"
        @click="goToUserForm"
      >
        <PlusIcon class="size-6 mr-1" />
        <span>Add New User</span>
      </button>
    </div>
    <UsersTable @clickEdit="clickEdit"/>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline';
import UsersTable from './UsersTable.vue';
import { useRouter } from 'vue-router';

const router= useRouter()
const goToUserForm = () => {
  router.push({ name: 'app.user_form' });
}

const clickEdit = (user) => {
  router.push({
    name: 'app.user_form',
    params: { id: user.id }
  });
}

</script>

<style></style>