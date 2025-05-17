<template>
  <div v-if="currentUser.id" class="min-h-full flex">
    <!-- sidebar -->
    <Sidebar :class="{ '-ml-[250px]': !sidebarOpened }" />

    <!-- main content area -->
    <div class="min-h-full flex-1 transition-all duration-300" :class="{ 'ml-[250px]': sidebarOpened }">
      <!-- header -->
      <Navbar @toggle-sidebar="toggleSidebar" />

      <!-- content -->
      <main>
        <div class="px-4 py-6 sm:px-6 lg:px-8">
          <router-view></router-view>
        </div>
      </main>
    </div>
  </div>
  <div v-else class="absolute top-[50%] left-[50%] transform translate-x-[-50%] translate-y-[-50%]">
    <Spinner />
  </div>


</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import Sidebar from './Sidebar.vue';
import Navbar from './Navbar.vue';
import { useStore } from '../store';
import Spinner from './core/Spinner.vue';

const store = useStore();

const sidebarOpened = ref(true);

const currentUser = computed(() => store.user.data)

const toggleSidebar = () => {
  sidebarOpened.value = !sidebarOpened.value;

}


onMounted(() => {
  store.getUser()
  handleSidebarOpened();
  window.addEventListener("resize", handleSidebarOpened);
  
});

onUnmounted(() => {
  handleSidebarOpened();
  window.addEventListener("resize", handleSidebarOpened);
});

const handleSidebarOpened = () => {
  sidebarOpened.value = window.outerWidth > 998;
}




</script>

<style></style>