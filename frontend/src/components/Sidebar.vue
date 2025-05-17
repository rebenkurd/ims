<template>
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="../assets/images/logo.png" alt="Logo" class="sidebar-logo-img" />
            <span class="sidebar-logo-text">
                Admin Panel
            </span>
        </div>

        <nav class="flex-1 overflow-y-auto">
            <router-link :to="{ name: 'app.dashboard' }"
                class="sidebar-item">
                <HomeIcon class="w-6 h-6 mr-3 text-gray-400" />
                <span class="sidebar-item-text">Dashboard</span>
            </router-link>


            <!-- Product -->
            <div class="relative">
                <div @click="toggleProductDropdown"
                    class="flex items-center justify-between transition-colors sidebar-item"
                    :class="{ 'bg-gray-700': isProductDropdownOpen }">
                    <div class="flex items-center">
                        <ListBulletIcon class="w-6 h-6 mr-3 text-gray-400" />
                        <span class="sidebar-item-text">Products</span>
                    </div>
                    <ChevronDownIcon class="w-5 h-5 text-gray-50 transition-transform duration-300"
                        :class="{ 'rotate-180': isProductDropdownOpen }" />
                </div>

                <transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="isProductDropdownOpen" class="bg-gray-700 rounded-md shadow-sm mx-1 mb-2">
                        <router-link :to="{name:'app.product_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Product List
                        </router-link>
                        <router-link :to="{name:'app.product_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New Product
                        </router-link>
                        <router-link :to="{name:'app.category_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Category List
                        </router-link>
                        <router-link :to="{name:'app.category_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New Category
                        </router-link>
                        <router-link :to="{name:'app.brand_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Brand List
                        </router-link>
                        <router-link :to="{name:'app.brand_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New Brand
                        </router-link>
                    </div>
                </transition>
            </div>
            <!-- !!! Product -->
            <!-- Purchase -->
            <div class="relative">
                <div @click="togglePurchaseDropdown"
                    class="flex items-center justify-between transition-colors sidebar-item"
                    :class="{ 'bg-gray-700': isPurchaseDropdownOpen }">
                    <div class="flex items-center">
                        <UsersIcon class="w-6 h-6 mr-3 text-gray-400" />
                        <span class="sidebar-item-text">Purchases</span>
                    </div>
                    <ChevronDownIcon class="w-5 h-5 text-gray-50 transition-transform duration-300"
                        :class="{ 'rotate-180': isPurchaseDropdownOpen }" />
                </div>

                <transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="isPurchaseDropdownOpen" class="bg-gray-700 rounded-md shadow-sm mx-1 mb-2">
                        <router-link :to="{name:'app.purchase_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Purchase List
                        </router-link>
                        <router-link :to="{name:'app.purchase_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New Purchase
                        </router-link>
                    </div>
                </transition>
            </div>
            <!-- !!! Purchase -->
            <!-- Sale -->
            <div class="relative">
                <div @click="toggleSaleDropdown"
                    class="flex items-center justify-between transition-colors sidebar-item"
                    :class="{ 'bg-gray-700': isSaleDropdownOpen }">
                    <div class="flex items-center">
                        <UsersIcon class="w-6 h-6 mr-3 text-gray-400" />
                        <span class="sidebar-item-text">Sales</span>
                    </div>
                    <ChevronDownIcon class="w-5 h-5 text-gray-50 transition-transform duration-300"
                        :class="{ 'rotate-180': isSaleDropdownOpen }" />
                </div>

                <transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="isSaleDropdownOpen" class="bg-gray-700 rounded-md shadow-sm mx-1 mb-2">
                        <router-link :to="{name:'app.sale_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Sale List
                        </router-link>
                        <router-link :to="{name:'app.sale_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New Sale
                        </router-link>
                    </div>
                </transition>
            </div>
            <!-- !!! Sale -->
            <!-- User -->
            <div class="relative">
                <div @click="toggleUserDropdown"
                    class="flex items-center justify-between transition-colors sidebar-item"
                    :class="{ 'bg-gray-700': isUserDropdownOpen }">
                    <div class="flex items-center">
                        <UsersIcon class="w-6 h-6 mr-3 text-gray-400" />
                        <span class="sidebar-item-text">Users</span>
                    </div>
                    <ChevronDownIcon class="w-5 h-5 text-gray-50 transition-transform duration-300"
                        :class="{ 'rotate-180': isUserDropdownOpen }" />
                </div>

                <transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="isUserDropdownOpen" class="bg-gray-700 rounded-md shadow-sm mx-1 mb-2">
                        <router-link :to="{name:'app.user_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            User List
                        </router-link>
                        <router-link :to="{name:'app.user_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New User
                        </router-link>
                    </div>
                </transition>
            </div>
            <!-- !!! User -->
            <!-- Supplier -->
            <div class="relative">
                <div @click="toggleSupplierDropdown"
                    class="flex items-center justify-between transition-colors sidebar-item"
                    :class="{ 'bg-gray-700': isSupplierDropdownOpen }">
                    <div class="flex items-center">
                        <UsersIcon class="w-6 h-6 mr-3 text-gray-400" />
                        <span class="sidebar-item-text">Suppliers</span>
                    </div>
                    <ChevronDownIcon class="w-5 h-5 text-gray-50 transition-transform duration-300"
                        :class="{ 'rotate-180': isSupplierDropdownOpen }" />
                </div>

                <transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="isSupplierDropdownOpen" class="bg-gray-700 rounded-md shadow-sm mx-1 mb-2">
                        <router-link :to="{name:'app.supplier_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Supplier List
                        </router-link>
                        <router-link :to="{name:'app.supplier_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New Supplier
                        </router-link>
                    </div>
                </transition>
            </div>
            <!-- !!! Supplier -->
            <!-- Customer -->
            <div class="relative">
                <div @click="toggleCustomerDropdown"
                    class="flex items-center justify-between transition-colors sidebar-item"
                    :class="{ 'bg-gray-700': isCustomerDropdownOpen }">
                    <div class="flex items-center">
                        <UsersIcon class="w-6 h-6 mr-3 text-gray-400" />
                        <span class="sidebar-item-text">Customers</span>
                    </div>
                    <ChevronDownIcon class="w-5 h-5 text-gray-50 transition-transform duration-300"
                        :class="{ 'rotate-180': isCustomerDropdownOpen }" />
                </div>

                <transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="isCustomerDropdownOpen" class="bg-gray-700 rounded-md shadow-sm mx-1 mb-2">
                        <router-link :to="{name:'app.customer_list'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Customer List
                        </router-link>
                        <router-link :to="{name:'app.customer_form'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Add New Customer
                        </router-link>
                    </div>
                </transition>
            </div>
            <!-- !!! Customer -->
            <!-- Setting -->
            <div class="relative">
                <div @click="toggleSettingDropdown"
                    class="flex items-center justify-between transition-colors sidebar-item"
                    :class="{ 'bg-gray-700': isSettingDropdownOpen }">
                    <div class="flex items-center">
                        <UsersIcon class="w-6 h-6 mr-3 text-gray-400" />
                        <span class="sidebar-item-text">Settings</span>
                    </div>
                    <ChevronDownIcon class="w-5 h-5 text-gray-50 transition-transform duration-300"
                        :class="{ 'rotate-180': isSettingDropdownOpen }" />
                </div>

                <transition enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                    <div v-if="isSettingDropdownOpen" class="bg-gray-700 rounded-md shadow-sm mx-1 mb-2">
                        <router-link :to="{name:'app.company_profile'}"
                            class="block px-4 py-2 text-sm text-white hover:bg-gray-800 transition-colors">
                            Company Profile
                        </router-link>
                    </div>
                </transition>
            </div>
            <!-- !!! Setting -->
        </nav>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import {
    HomeIcon,
    UsersIcon,
    ChartBarIcon,
    ListBulletIcon,
    ChevronDownIcon
} from '@heroicons/vue/24/outline'

const isProductDropdownOpen = ref(false)
const isUserDropdownOpen = ref(false)
const isPurchaseDropdownOpen = ref(false)
const isSaleDropdownOpen = ref(false)
const isSupplierDropdownOpen = ref(false)
const isCustomerDropdownOpen = ref(false)
const isSettingDropdownOpen = ref(false)

const toggleProductDropdown = () => isProductDropdownOpen.value = !isProductDropdownOpen.value;
const toggleUserDropdown = () => isUserDropdownOpen.value = !isUserDropdownOpen.value;
const togglePurchaseDropdown = () => isPurchaseDropdownOpen.value = !isPurchaseDropdownOpen.value;
const toggleSaleDropdown = () => isSaleDropdownOpen.value = !isSaleDropdownOpen.value;
const toggleSupplierDropdown = () => isSupplierDropdownOpen.value = !isSupplierDropdownOpen.value;
const toggleCustomerDropdown = () => isCustomerDropdownOpen.value = !isCustomerDropdownOpen.value;
const toggleSettingDropdown = () => isSettingDropdownOpen.value = !isSettingDropdownOpen.value;


</script>