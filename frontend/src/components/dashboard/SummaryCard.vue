<template>
  <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
    <div class="flex justify-between items-start">
      <div>
        <h3 class="text-sm font-medium text-gray-500">{{ title }}</h3>
        <p class="mt-1 text-2xl font-semibold text-gray-900">
          {{ formattedValue }}
        </p>
      </div>
      <div class="p-2 rounded-full bg-blue-100 text-blue-600">
        <component :is="iconComponent" class="h-5 w-5" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import {
  CurrencyDollarIcon,
  ArrowTrendingUpIcon,
  ShoppingCartIcon,
  ChartBarIcon,
  ServerIcon,
  BanknotesIcon,
  LockClosedIcon,
  DocumentDuplicateIcon,
  UsersIcon,
  TruckIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  value: {
    type: [Number, String],
    required: true
  },
  icon: {
    type: String,
    required: true
  }
});

const formattedValue = computed(() => {
  if (typeof props.value === 'number') {
    return props.value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  return props.value;
});

const iconComponent = computed(() => {
  switch (props.icon) {
    case 'currency-dollar': return CurrencyDollarIcon;
    case 'trending-up': return ArrowTrendingUpIcon;
    case 'shopping-cart': return ShoppingCartIcon;
    case 'chart-bar': return ChartBarIcon;
    case 'server': return ServerIcon;
    case 'cash': return BanknotesIcon;
    case 'lock-closed': return LockClosedIcon;
    case 'document-duplicate': return DocumentDuplicateIcon;
    case 'users': return UsersIcon;
    case 'truck': return TruckIcon;
    default: return CurrencyDollarIcon;
  }
});
</script>