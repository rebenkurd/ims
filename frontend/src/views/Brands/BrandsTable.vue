<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <!-- Controls section -->
    <div class="flex flex-col md:flex-row md:justify-between gap-3 border-b-2 pb-3">
      <!-- Per Page selector -->
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select 
          @change="getBrands(null)" 
          v-model="perPage"
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>

      <!-- Search input -->
      <div class="w-full md:w-48">
        <input 
          v-model="search" 
          @keyup="getBrands(null)"
          class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          placeholder="Type to search..."
        >
      </div>
    </div>

    <!-- Table section with overflow handling -->
    <div class="overflow-x-hidden">
      <table class="table-auto w-full min-w-full">
        <thead>
          <tr>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="id" :sort-field="sortField" :sort-direction="sortDirection">ID</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="name" :sort-field="sortField" :sort-direction="sortDirection">Name</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Actions</TableHeaderCell>
          </tr>
        </thead>
        <tbody v-if="brands.loading">
          <tr>
            <td colspan="11" class="text-center">
              <Spinner class="my-6" />
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr v-for="brand of brands.data" :key="brand.id" class="animate-fade-in-down">
            <td class="border-b-2 p-2">{{ brand.id }}</td>
            <td class="border-b-2 p-2">{{ brand.name }}</td>
            <td class="border-b-2 p-2">
              <Menu as="div" class="relative inline-block text-left">
                <div>
                  <MenuButton
                    class="inline-flex w-full justify-center rounded-md px-4 py-2 cursor-pointer text-gray-800 hover:text-gray-700"
                  > 
                    <EllipsisVerticalIcon
                      class="h-8 w-8"
                      aria-hidden="true"
                    />
                  </MenuButton>
                </div>
                <transition
                  enter-active-class="transition duration-100 ease-out"
                  enter-from-class="transform scale-95 opacity-0"
                  enter-to-class="transform scale-100 opacity-100"
                  leave-active-class="transition duration-75 ease-in"
                  leave-from-class="transform scale-100 opacity-100"
                  leave-to-class="transform scale-95 opacity-0"
                >
                  <MenuItems
                    class="absolute right-0 md:right-auto left-auto md:left-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 focus:outline-none z-10"
                  >
                    <div class="px-1 py-1">
                      <MenuItem v-slot="{ active }">
                        <button
                          :class="[
                            active ? 'bg-blue-500 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointer',
                          ]"
                          @click="editBrand(brand)"
                        >
                          <PencilIcon
                            :active="active"
                            class="mr-2 h-5 w-5 text-blue-400"
                            aria-hidden="true"
                          />
                          Edit
                        </button>
                      </MenuItem>
                    </div>
                    <div class="px-1 py-1">
                      <MenuItem v-slot="{ active }">
                        <button
                          :class="[
                            active ? 'bg-blue-500 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointer',
                          ]"
                          @click="deleteBrand(brand)"
                        >
                          <TrashIcon
                            :active="active"
                            class="mr-2 h-5 w-5 text-blue-400"
                            aria-hidden="true"
                          />
                          Delete
                        </button>
                      </MenuItem>
                    </div>
                  </MenuItems>
                </transition>
              </Menu>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination section -->
    <div v-if="!brands.loading" class="flex text-red-500 flex-col md:flex-row md:justify-between items-center mt-5 gap-3">
      <span class="text-sm">
        Showing from {{ brands.from }} to {{ brands.to }}
      </span>

      <nav 
        v-if="brands.total > brands.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px overflow-x-auto"
        aria-label="Pagination"
      >
        <a
          v-for="(link, i) of brands.links"
          :key="i"
          :disabled="!link.url"
          href="#"
          @click="getForPage($event, link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
            link.active
              ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
            i === 0 ? 'rounded-l-md' : '',
            i === brands.links.length - 1 ? 'rounded-r-md' : '',
            !link.url ? ' bg-gray-100 text-gray-700': ''
          ]"
          v-html="link.label"
        >
        </a>
      </nav>
    </div>
  </div>
</template>


<script setup>
import { Bars3Icon, EllipsisVerticalIcon, PencilIcon, PlusIcon, TrashIcon } from '@heroicons/vue/24/outline';
import Spinner from '@components/core/Spinner.vue';
import { computed, onMounted, ref } from 'vue';
import { useBrandStore } from '@store/brand';
import { PER_PAGE } from '@views/constant';
import TableHeaderCell from '@components/core/table/TableHeaderCell.vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import router from '@router';
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2'

const toast = useToast()
const brandStore = useBrandStore();
const perPage = ref(PER_PAGE);
const search = ref('');
const brands = computed(() => brandStore.brands)
const sortField = ref('updated_at');
const sortDirection = ref('desc');

const emit = defineEmits(['clickEdit'])
onMounted(() => {
  getBrands()
})

const getBrands = (url = null) => {
  brandStore.getBrands(
    url,
    search.value,
    perPage.value,
    sortField.value,
    sortDirection.value
  )
}

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  getBrands(link.url)
}


const sortData=(field)=>{

  if(sortField.value == field){
    if(sortDirection.value == 'asc'){
      sortDirection.value = 'desc';
    }else{
      sortDirection.value = 'asc';
    }
  }else{
      sortField.value = field;
      sortDirection.value = 'asc';
  }
  getBrands()
}

const editBrand = (brand)=>{
  router.push({
    name:'app.brand_form',
    params: { id: brand.id }
  })
}


const deleteBrand = (brand)=>{

  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#fb2c36',
    cancelButtonColor: '#6a7282',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire(
        'Deleted!',
        'brand has been deleted.',
       'success'
      )
      brandStore.deleteBrand(brand.id)
        .then(()=>{
          brandStore.getBrands()
        })
    }
  })


}



</script>

<style></style>