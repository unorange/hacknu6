import { ref } from "vue";
import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", () => {
  const token = ref("");

  const setToken = (value: string) => (token.value = value);

  const authorized = computed(() => !!token.value);

  return {
    token,
    setToken,
    authorized,
  };
});
