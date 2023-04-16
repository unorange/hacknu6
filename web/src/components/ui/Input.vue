<script setup lang="ts">
defineProps<{
  modelValue: string;
}>();

const emit = defineEmits(["update:modelValue"]);

const handleInput = (e: InputEvent | Event) => {
  const targetValue = (e.target as HTMLInputElement).value;

  emit("update:modelValue", targetValue);
};
</script>

<template>
  <label class="flex flex-col" :class="{ 'gap-y-1': $slots.default }">
    <span class="text-gray-500">
      <slot></slot>
    </span>
    <input
      :value="modelValue"
      v-bind="$attrs"
      @input="handleInput"
      class="border border-white/20 placeholder-current bg-blue-500 py-2 px-3 rounded-md"
    />
  </label>
</template>
