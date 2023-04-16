<template>
  <header class="border-b shadow-sm border-b-1">
    <nav class="p-4 container mx-auto">
      <RouterLink to="/" class="text-lg font-medium"> docsportal </RouterLink>
    </nav>
  </header>
  <div class="p-4 container mx-auto">
    <slot></slot>
  </div>
</template>

<script setup lang="ts"></script>

<style scoped></style>
