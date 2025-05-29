<template>

    <div class="bg-white p-4 rounded-lg shadow ">
      <div class="flex justify-between border-b-2 pb-3">
        <div class="flex items-center">
          <span class="whitespace-nowrap mr-3">Per Page</span>
          <select @change="getPurchases(null)" v-model="perPage"
            class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>

        <!-- Searching -->
        <div>
          <input v-model="search" @keyup="getPurchases(null)"
            class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
            placeholder="Type to search...">
        </div>
      </div>

        <table class="table-auto w-full">
          <thead>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="id" :sort-field="sortField" :sort-direction="sortDirection">ID</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="code" :sort-field="sortField" :sort-direction="sortDirection">Code</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="purchase_status" :sort-field="sortField" :sort-direction="sortDirection">Purchase Status</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="discount" :sort-field="sortField" :sort-direction="sortDirection">Discount</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="supplier_id" :sort-field="sortField" :sort-direction="sortDirection">Supplier</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="total" :sort-field="sortField" :sort-direction="sortDirection">total</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="created_by" :sort-field="sortField" :sort-direction="sortDirection">Created By</TableHeaderCell>
            <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="status" :sort-field="sortField" :sort-direction="sortDirection">Status</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Actions</TableHeaderCell>
          </thead>
          <tbody v-if="purchases.loading">
            <tr>
              <td colspan="11">
      <Spinner class="my-6" />

              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr v-for="purchase of purchases.data" :key="purchase.id" class="animate-fade-in-down">
              <td class="border-b-2 p-2">{{ purchase.id }}</td>
              <td class="border-b-2 p-2">{{ purchase.code }}</td>
              <td class="border-b-2 p-2">{{ purchase.purchase_status }}</td>
              <td class="border-b-2 p-2">{{ purchase.discount }}</td>
              <td class="border-b-2 p-2">{{ purchase.supplier }}</td>
              <td class="border-b-2 p-2">{{ purchase.total }}</td>
              <td class="border-b-2 p-2">{{ purchase.created_by }}</td>
              <td class="border-b-2 p-2">{{ purchase.status }}</td>
              <!-- <td class="border-b-2 p-2">
                <span class=" py-1 px-2 rounded-md text-xs select-none cursor-pointer" :class="purchase.status == 'Active'? 'bg-green-200 text-green-700 border-green-200' : 'bg-red-200 text-red-700 border-red-200'">
                  {{ purchase.status }}
                </span>
              </td> -->
              <td class="border-b-2 p-2">
    <Menu as="div" class="relative inline-block text-left">
      <div>
        <MenuButton
          class="inline-flex w-full justify-center rounded-md  px-4 py-2 cursor-pointer text-gray-800 hover:text-gray-700 "
        > 
          
          <EllipsisVerticalIcon
            class="h-8 w-8 "
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
          class="absolute right-0 mt-2 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 focus:outline-none z-10"
        >
          <div class="px-1 py-1">
            <MenuItem v-slot="{ active }">
              <button
                :class="[
                  active ? 'bg-blue-500 text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointe',
                ]"
                @click="editPurchase(purchase)"
              >
                <PencilIcon
                  :active="active"
                  class="mr-2 h-5 w-5 text-blue-400"
                  aria-hidden="true"
                />
                Edit
              </button>
            </MenuItem>
            <MenuItem v-slot="{ active }" v-if="purchase.invoice">
  <button
    :class="[
      active ? 'bg-blue-500 text-white' : 'text-gray-900',
      'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointe',
    ]"
    @click="viewInvoice(purchase.id, purchase.invoice.id)" 
  >
    <PencilIcon
      :active="active"
      class="mr-2 h-5 w-5 text-blue-400"
      aria-hidden="true"
    />
    View Invoice
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
                @click="deletePurchase(purchase)"
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

        <!-- Pagination -->
        <div v-if="!purchases.loading" class="flex justify-between items-center mt-5">
          <span>
            Showing from {{ purchases.from }} to {{ purchases.to }}
          </span>

          <nav 
          v-if="purchases.total > purchases.limit"
          class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
          aria-label="Pagination"
          >
          <!-- Current: "z-10 bg-blue-50 border-blue-500 text-blue-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->

          <a
          v-for="(link, i) of purchases.links"
          :key="i"
          :disabled="!link.url"
          href="#"
          @click="getForPage($event, link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
              link.active
                ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
              i === 0 ? 'rounded-l-md' : '',
              i === purchases.links.length - 1 ? 'rounded-r-md' : '',
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
import { usePurchaseStore } from '@store/purchase';
import { PER_PAGE } from '@/constant';
import TableHeaderCell from '@components/core/table/TableHeaderCell.vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import router from '@router';
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2'
const toast = useToast()
const purchaseStore = usePurchaseStore();
const perPage = ref(PER_PAGE);
const search = ref('');
const purchases = computed(() => purchaseStore.purchases)
const sortField = ref('updated_at');
const sortDirection = ref('desc');

const emit = defineEmits(['clickEdit'])
onMounted(() => {
  getPurchases()
})

const getPurchases = (url = null) => {
  purchaseStore.getPurchases(
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

  getPurchases(link.url)
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
  getPurchases()
}

const editPurchase = (purchase)=>{
  router.push({
    name:'app.purchase_form',
    params: { id: purchase.id }
  })
}


const deletePurchase = (purchase)=>{
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
        'Purchase has been deleted.',
       'success'
      )
      purchaseStore.deletePurchase(purchase.id)
      .then(()=>{
        purchaseStore.getPurchases()
      })
    }
  })


}
const viewInvoice = (purchaseId, invoiceId) => {
  if (!invoiceId) {
    toast.error("No invoice found for this purchase");
    return;
  }
  
  router.push({
    name: 'app.purchase_invoice',
    params: { 
      purchaseId: purchaseId,
      invoiceId: invoiceId
    }
  });
};
</script>

<style></style>