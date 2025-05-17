
<template>
     <GuestLayout title="Request New Password">
        <form class="space-y-6" action="#" method="POST">
          <p class="bg-blue-100 border-2 border-blue-300 rounded-md p-2 text-sm text-blue-400">
            Enter your registered email address to request a new password. We'll send you an email with a link to reset your password.
            
          </p>
          
          <div>
            <label for="email" class="form-label">Email</label>
            <div class="mt-2">
              <input type="email" name="email" id="email"  placeholder="Enter Your Email" autocomplete="email" required="" class="form-input" />
            </div>
          </div>
  
  
          <div>
            <button type="submit" class="form-button">Submit</button>
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
