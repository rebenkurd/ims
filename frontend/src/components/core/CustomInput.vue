<template>
  <div>
    <label class="text-sm text-gray-800">{{ label }}<span v-if="symbol">({{ symbol }})</span> <span v-if="required" class="text-red-400">*</span> </label>
    <div class="mt-1 flex rounded-md shadow-sm">
      <span v-if="prepend"
        class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
        {{ prepend }}
      </span>
      <template v-if="type === 'textarea'">
        <textarea :name="name" :required="required" :value="modelValue"
          @input="$emit('update:modelValue', $event.target.value)" :class="inputClasses"
          :placeholder="label"></textarea>
      </template>

      <template v-else-if="type === 'file'">
        <div> 
          <input :type="type" :name="name" :required="required" @change="handleFileChange"
            :class="inputClasses" :placeholder="label"  />
          <div v-if="imageUrl" class="mt-2 p-2">
            <img :src="imageUrl" alt="Selected Image" class="w-32 h-32 object-cover rounded-md border" />
          </div>
          <div v-if="!imageUrl" class="mt-2 p-2">
            <img :src="image" :alt="name" class="w-32 h-32 object-cover rounded-md border" />
          </div>
        </div>
      </template>


      <template v-else-if="type === 'select'">
        <div class="w-full relative">
          <div @click="toggleDropdown" class="relative">
            <div class="block bg-white w-full px-3 py-2 border text-sm border-gray-200 text-gray-900 cursor-pointer"
              :class="addModal ? 'rounded-l-md':'rounded-md'"
            >
              {{ selectedLabel || `Select ${label}` }}
            </div>
          </div>
          
          <div v-if="showDropdown" class="absolute w-full mt-1 border border-gray-300 bg-white rounded-md shadow-lg z-10">
            <input v-if="searchable" v-model="searchQuery" type="text"
              class="w-full p-2 border-b border-gray-300 outline-none text-sm" placeholder="Search...">
            <ul class="max-h-48 overflow-y-auto">
              <li v-for="option in filteredOptions" :key="option.value" @click="selectOption(option)"
                class="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm">
                {{ option.label }}
              </li>
            </ul>
          </div>
        </div>
      </template>


      <template v-else>
        <input :type="type" :name="name" :required="required" :value="modelValue" :disabled="disabled?true:false" :class="inputClasses"
          @input="$emit('update:modelValue', $event.target.value)"  :placeholder="label"
          :step="type === 'number' ? '0.01' : undefined" />
      </template>


      <span v-if="append"
        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
        {{ append }}
      </span>
      <span v-else-if="addModal"
        class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm cursor-pointer hover:bg-blue-500 hover:text-white duration-300 transition-all" @click="toggleAddModal">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>

      </span>
    </div>
        <span class="text-xs text-red-400 p-2" v-if="info">{{ info }}</span>
  </div>
</template>

<script setup>
import { computed, readonly, ref } from 'vue';

const props = defineProps({
  modelValue: [String, Number, File],
  label: String,
  type: {
    type: String,
    default: 'text',
  },
  name: String,
  image: String,
  required:{
    type: Boolean,
    default: false,
  },
  info:{
    type: String,
    default: '',
  },
  readonly: Boolean,
  prepend: {
    type: String,
    default: '',
  },
  append: {
    type: String,
    default: '',
  },
  addModal: {
    type: Boolean,
    default: false,
  },
    options: {
    type: Array,
    default: () => [],
  },

  searchable: {
    type: Boolean,
    default: false,
  },
  disabled:{
    type: Boolean,
    default: false,
  },
  symbol:{
    type: String,
    default: '',
  }
});

const emit = defineEmits(['update:modelValue', 'change']);

const imageUrl = ref(null);
const searchQuery = ref('');
const showDropdown = ref(false);

const selectedLabel = computed(() => {
  const selectedOption = props.options.find(option => option.value === props.modelValue);
  return selectedOption ? selectedOption.label : '';
});

const filteredOptions = computed(() => {
  if (!searchQuery.value) return props.options;
  return props.options.filter(option =>
    option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
};

const selectOption = (option) => {
  emit('update:modelValue', option.value);
  showDropdown.value = false;
  searchQuery.value = '';
};

const handleFileChange = (event) => {

  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      imageUrl.value = e.target.result;
    };
    reader.readAsDataURL(file);
    emit('update:modelValue', file);
  }  
};

const inputClasses = computed(() => {
  const cls = [
    'block w-full bg-white px-3 py-2 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-400 focus:border-blue-400 focus:z-10 sm:text-sm',
  ];

  if (props.append && !props.prepend) {
    cls.push('rounded-l-md');
  } else if (props.prepend && !props.append) {
    cls.push('rounded-r-md');
  } else if (!props.prepend && !props.append) {
    cls.push('rounded-md');
  }

  return cls.join(' ');
});
</script>

<style scoped>
textarea {
  resize: vertical;
}
</style>