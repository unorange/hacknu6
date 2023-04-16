<script setup lang="ts">
import { NMessageProvider } from 'naive-ui'
</script>

<template>
  <n-message-provider>
    <header class="border-b shadow-sm border-b-1">
      <nav class="p-4 container mx-auto">
        <RouterLink to="/" class="text-lg font-medium"> docsportal </RouterLink>
      </nav>
    </header>
    <div class="p-4 container mx-auto">
      <RouterView />
    </div>
  </n-message-provider>
</template>
