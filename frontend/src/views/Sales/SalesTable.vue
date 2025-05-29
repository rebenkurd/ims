<template>
  <div class="bg-white p-4 rounded-lg shadow ">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select @change="getSales(null)" v-model="perPage"
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
        <input v-model="search" @keyup="getSales(null)"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          placeholder="Type to search...">
      </div>
    </div>

      <table class="table-auto w-full">
        <thead>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="id" :sort-field="sortField" :sort-direction="sortDirection">ID</TableHeaderCell>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="code" :sort-field="sortField" :sort-direction="sortDirection">Code</TableHeaderCell>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="sale_status" :sort-field="sortField" :sort-direction="sortDirection">Sale Status</TableHeaderCell>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="discount" :sort-field="sortField" :sort-direction="sortDirection">Discount</TableHeaderCell>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="customer_id" :sort-field="sortField" :sort-direction="sortDirection">Customer</TableHeaderCell>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="total" :sort-field="sortField" :sort-direction="sortDirection">Total</TableHeaderCell>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="created_by" :sort-field="sortField" :sort-direction="sortDirection">Created By</TableHeaderCell>
          <TableHeaderCell @click="sortData" class="border-b-2 p-2 text-left" field="status" :sort-field="sortField" :sort-direction="sortDirection">Status</TableHeaderCell>
          <TableHeaderCell class="border-b-2 p-2 text-left">Actions</TableHeaderCell>
        </thead>
        <tbody v-if="sales.loading">
          <tr>
            <td colspan="11">
              <Spinner class="my-6" />
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr v-for="sale of sales.data" :key="sale.id" class="animate-fade-in-down">
            <td class="border-b-2 p-2">{{ sale.id }}</td>
            <td class="border-b-2 p-2">{{ sale.code }}</td>
            <td class="border-b-2 p-2">{{ sale.sale_status }}</td>
            <td class="border-b-2 p-2">{{ sale.discount }}</td>
            <td class="border-b-2 p-2">{{ sale.customer }}</td>
            <td class="border-b-2 p-2">{{ sale.total }}</td>
            <td class="border-b-2 p-2">{{ sale.created_by }}</td>
            <td class="border-b-2 p-2">{{ sale.status }}</td>
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
                          @click="editSale(sale)"
                        >
                          <PencilIcon
                            :active="active"
                            class="mr-2 h-5 w-5 text-blue-400"
                            aria-hidden="true"
                          />
                          Edit
                        </button>
                      </MenuItem>
                      <MenuItem v-slot="{ active }" v-if="sale.invoice">
                        <button
                          :class="[
                            active ? 'bg-blue-500 text-white' : 'text-gray-900',
                            'group flex w-full items-center rounded-md px-2 py-2 text-sm cursor-pointe',
                          ]"
                          @click="viewInvoice(sale.id, sale.invoice.id)" 
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
                          @click="deleteSale(sale)"
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
      <div v-if="!sales.loading" class="flex justify-between items-center mt-5">
        <span>
          Showing from {{ sales.from }} to {{ sales.to }}
        </span>

        <nav 
        v-if="sales.total > sales.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
        >
          <a
            v-for="(link, i) of sales.links"
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
              i === sales.links.length - 1 ? 'rounded-r-md' : '',
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
import { useSaleStore } from '@store/sale';
import { PER_PAGE } from '@/constant';
import TableHeaderCell from '@components/core/table/TableHeaderCell.vue';
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
import router from '@router';
import { useToast } from 'vue-toastification';
import Swal from 'sweetalert2'

const toast = useToast()
const saleStore = useSaleStore();
const perPage = ref(PER_PAGE);
const search = ref('');
const sales = computed(() => saleStore.sales)
const sortField = ref('updated_at');
const sortDirection = ref('desc');

const emit = defineEmits(['clickEdit'])
onMounted(() => {
getSales()
})

const getSales = (url = null) => {
saleStore.getSales(
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
getSales(link.url)
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
getSales()
}

const editSale = (sale)=>{
router.push({
  name:'app.sale_form',
  params: { id: sale.id }
})
}

const deleteSale = (sale)=>{
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
      'Sale has been deleted.',
     'success'
    )
    saleStore.deleteSale(sale.id)
    .then(()=>{
      saleStore.getSales()
    })
  }
})
}

const viewInvoice = (saleId, invoiceId) => {
if (!invoiceId) {
  toast.error("No invoice found for this sale");
  return;
}

router.push({
  name: 'app.sale_invoice',
  params: { 
    saleId: saleId,
    invoiceId: invoiceId
  }
});
};
</script>

<style></style>