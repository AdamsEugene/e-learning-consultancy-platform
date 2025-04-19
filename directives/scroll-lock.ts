import type { DirectiveBinding, ObjectDirective } from "vue";

// Keep track of locked elements to handle multiple elements using the directive
let lockedElements = 0;
let originalBodyStyle: string | null = null;

const scrollLock: ObjectDirective = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== false) {
      // Save original style only on first lock
      if (lockedElements === 0) {
        originalBodyStyle = document.body.style.overflow;
      }
      lockedElements++;
      document.body.style.overflow = "hidden";

      // Prevent touch scrolling as well
      document.addEventListener("touchmove", preventScroll, { passive: false });
    }
  },
  updated(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== binding.oldValue) {
      if (binding.value !== false) {
        // Apply lock
        if (lockedElements === 0) {
          originalBodyStyle = document.body.style.overflow;
        }
        lockedElements++;
        document.body.style.overflow = "hidden";

        // Prevent touch scrolling
        document.addEventListener("touchmove", preventScroll, {
          passive: false,
        });
      } else {
        // Remove lock
        lockedElements--;
        if (lockedElements <= 0) {
          document.body.style.overflow = originalBodyStyle || "";
          lockedElements = 0;

          // Re-enable touch scrolling
          document.removeEventListener("touchmove", preventScroll);
        }
      }
    }
  },
  unmounted(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== false) {
      // Remove lock
      lockedElements--;
      if (lockedElements <= 0) {
        document.body.style.overflow = originalBodyStyle || "";
        lockedElements = 0;

        // Re-enable touch scrolling
        document.removeEventListener("touchmove", preventScroll);
      }
    }
  },
};

// Helper to prevent scrolling on mobile devices
function preventScroll(e: TouchEvent) {
  e.preventDefault();
}

export default scrollLock;
