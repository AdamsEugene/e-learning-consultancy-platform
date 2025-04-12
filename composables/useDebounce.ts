/* eslint-disable @typescript-eslint/no-explicit-any */
/**
 * Composable that creates a debounced version of a value.
 * The returned value will only update after the specified delay has passed,
 * allowing you to limit how often a computationally expensive operation occurs.
 *
 * @param value - The reactive value to debounce
 * @param delay - The delay in milliseconds (defaults to 300ms)
 * @returns A debounced version of the input value
 */
export function useDebounce<T>(value: any, delay = 300) {
  const debouncedValue = ref<T>(value.value);
  let timeout: ReturnType<typeof setTimeout> | null = null;

  watch(value, (newValue) => {
    // Clear previous timeout if it exists
    if (timeout) {
      clearTimeout(timeout);
    }

    // Set a new timeout
    timeout = setTimeout(() => {
      debouncedValue.value = newValue;
    }, delay);
  });

  return debouncedValue;
}
