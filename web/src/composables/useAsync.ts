import type { Ref } from "vue";
import { ref } from "vue";

interface UseAsync<T extends (...args: any[]) => unknown> {
  active: Ref<boolean>;
  run: (...args: Parameters<T>) => Promise<ReturnType<T>>;
  // data: Ref<ReturnType<T> | null>;
}

export function useAsync<T extends (...args: any[]) => unknown>(
  fn: T
): UseAsync<T> {
  const active: UseAsync<T>["active"] = ref(false);
  // const data: UseAsync<T>["data"] = ref(null);

  const run: UseAsync<T>["run"] = async (...args) => {
    active.value = true;
    try {
      const result = await fn(...args);
      // data.value = result;
      return result as ReturnType<T>;
    } finally {
      active.value = false;
    }
  };

  return { active, run };
}
