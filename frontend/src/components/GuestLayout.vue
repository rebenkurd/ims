
<template>

  <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-20 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
      <img class="mx-auto h-28 w-auto" src="../assets/images/logo.png" alt="Your Company" />
      <h2 class="mt-2 text-center text-3xl/9 font-bold tracking-tight text-gray-900">
        {{ title }}
      </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">

      <slot></slot>

    </div>
  </div>
</template>

<script setup>

const {title}=defineProps({
    title: {
      type: String,
    }
})

</script>
