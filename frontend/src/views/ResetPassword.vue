
<template>
     <GuestLayout title="Create New Password">
        <form class="space-y-6" action="#" method="POST">
          
          <div>
            <label for="new-password" class="form-label">New Password</label>
            <div class="mt-2">
              <input type="password" name="new-password" id="new-password" placeholder="Enter New Password" required="" class="form-input" />
            </div>
          </div>
          <div>
            <label for="confirmation-password" class="form-label">Confirmation Password</label>
            <div class="mt-2">
              <input type="password" name="confirmation-password" placeholder="Enter Confirmation Password" id="confirmation-password"  required="" class="form-input" />
            </div>
          </div>
  
  
          <div>
            <button type="submit" class="form-button">Reset</button>
          </div>
          
          <div class="flex justify-center mt-4">
            <p class="text-sm/6 text-gray-600">Return To <router-link :to="{name:'login'}" class="font-medium text-blue-600 hover:text-blue-800">Login</router-link></p>
          </div>
        </form>
  
     </GuestLayout>
  </template>

<script setup>
import GuestLayout from '../components/GuestLayout.vue'

</script>
