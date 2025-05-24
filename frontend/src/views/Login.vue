<script setup>
  import { ref } from 'vue';
  import GuestLayout from '../components/GuestLayout.vue'
  import {useStore} from '../store'
  import { useRouter } from 'vue-router';
  import { XMarkIcon } from '@heroicons/vue/24/outline';
  import { useToast } from "vue-toastification"

  const toast = useToast()

  const store = useStore()
  const loading = ref(false)
  const loadingDemo = ref(false) // Added for demo login
  const errorMsg = ref("")

  const router = useRouter()
  const user = {
    username: '',
    password: ''
  }

  // Demo credentials
  const demoAccounts = [
    { role: 'Admin', username: 'admin', password: '123' },
    { role: 'Saler', username: 'saler', password: '123' },
    { role: 'Purchaser', username: 'purchaser', password: '123' }
  ]

  const login = async () => {
    loading.value = true
    store.login(user)
    .then(() => {
      loading.value = false
      router.push({name: 'app.dashboard'})
    })
    .catch(({response})=>{
      loading.value = false
      toast.error(response.data.message)
      })
  }

  // Function to login with demo credentials
  const loginWithDemo = (account) => {
    loadingDemo.value = true
    store.login(account)
    .then(() => {
      loadingDemo.value = false
      router.push({name: 'app.dashboard'})
    })
    .catch(({response})=>{
      loadingDemo.value = false
      toast.error(response.data.message)
      })
  }

</script>

<template>
  <GuestLayout title="Inventory management System">
    <form class="space-y-6" @submit.prevent="login" method="POST">
      <div v-if="errorMsg" class="flex justify-between items-center bg-red-100 border-2 border-red-300 rounded-md py-2 px-3 text-sm text-red-400">
        <span>These credentials do not match our records.</span>
        <span @click='errorMsg = ""'>
          <XMarkIcon class="w-6 h-6 cursor-pointer"/>
        </span>
      </div>
      <div>
        <label for="username" class="form-label">Username</label>
        <div class="mt-2">
          <input type="text" name="username" id="username"  placeholder="Enter Your Email" required="" class="form-input"
          v-model="user.username"
           />
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label for="password"  class="form-label">Password</label>
          <div class="text-sm">
            <router-link :to="{name:'requestResetPassword'}" class="font-semibold text-blue-600 hover:text-blue-500">Forgot password?</router-link>
          </div>
        </div>
        <div class="mt-2">
          <input type="password" name="password" placeholder="Enter Your Password" id="password" autocomplete="current-password" required="" class="form-input"
          v-model="user.password"
           />
        </div>
      </div>

      <div>
        <button 
        :disabled="loading"
        type="submit"
        class="form-button"
        :class="{
          'cursor-not-allowed':loading,
          'hover:bg-blue-500':loading
        }"
        >
        
        <svg v-if="loading" class="mr-3 -ml-1 size-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>        
        Login</button>
      </div>
    </form>

    <!-- Demo Login Section -->
    <div class="mt-8 pt-8 border-t border-gray-200">
      <h3 class="text-center text-lg font-medium text-gray-900 mb-4">Click to Start Demo Session!</h3>
      
      <div class="space-y-4">
        <div v-for="account in demoAccounts" :key="account.role" class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
          <div class="flex-1">
            <span class="font-medium">{{ account.role }}</span>
            <span class="text-gray-500 text-sm ml-2">({{ account.username }}/{{ account.password }})</span>
          </div>
          <button
            @click="loginWithDemo(account)"
            :disabled="loadingDemo"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            :class="{'opacity-50 cursor-not-allowed': loadingDemo}"
          >
            <span v-if="loadingDemo">Applying...</span>
            <span v-else>Apply</span>
          </button>
        </div>
      </div>

    </div>
  </GuestLayout>
</template>