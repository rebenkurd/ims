<template>
      <Disclosure as="nav" class="bg-white shadow-md">
        <div class=" px-2 sm:px-4 lg:px-4">
          <div class="flex h-16 items-center justify-between">
            <button @click="emit('toggle-sidebar')" type="button" class="relative rounded-full  p-1 text-gray-700  cursor-pointer">
                  <span class="absolute -inset-1.5" />
                  <span class="sr-only">View notifications</span>
                  <Bars3Icon class="size-6" aria-hidden="true" />
            </button>
            <div class="hidden md:block">
              <div class="ml-4 flex items-center md:ml-6">
                <button type="button" class="relative rounded-full  p-1 text-gray-700 hover:text-gray-900 cursor-pointer">
                  <span class="absolute -inset-1.5" />
                  <span class="sr-only">View notifications</span>
                  <BellIcon class="size-6" aria-hidden="true" />
                </button>
  
                <!-- Profile dropdown -->
                <Menu as="div" class="relative ml-3">
                  <div>
                    <MenuButton class="relative flex max-w-xs items-center text-left rounded-full text-sm cursor-pointer">
                      <span class="absolute -inset-1.5" />
                      <span class="sr-only">Open user menu</span>
                      <img class="size-8 rounded-full" :src="currentUser.imageUrl" alt="" />
                      <span class="ml-3 text-sm font-medium text-gray-900">
                        {{ currentUser.name }}
                      </span>
                      
                    </MenuButton>
                  </div>
                  <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                    <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 ring-1 shadow-lg ring-black/5 ">
                      <MenuItem v-for="item in userNavigation" :key="item.name" v-slot="{ active }">
                        <a :href="item.href" :class="[active ? 'bg-gray-100 outline-hidden' : '', 'block px-4 py-2 text-sm text-gray-700']">{{ item.name }}</a>
                      </MenuItem>
                      <MenuItem v-slot="{ active }">
                        <button type="button" @click="logout" :class="[active ? 'bg-gray-100 outline-hidden' : '', 'cursor-pointer text-left block w-full px-4 py-2 text-sm text-gray-700']">Sign Out</button>
                      </MenuItem>
                    </MenuItems>
                  </transition>
                </Menu>
              </div>
            </div>
          </div>
        </div>
  
      </Disclosure>
  

</template>

<script setup>
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
  import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
import { useStore } from '../store'
import router from '../router';
import { computed } from 'vue';

  const store = useStore();


  const currentUser = computed(()=> store.user.data)
  

  const userNavigation = [
    { name: 'Your Profile', href: '#', },
    { name: 'Settings', href: '#', },
  ]


  const emit = defineEmits(['toggle-sidebar'])

  const logout = ()=>{
    store.logout()
    .then(()=>{
       router.push({name:'login'})
    })

    }
</script>

<style>

</style>


