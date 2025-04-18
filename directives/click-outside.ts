import type { DirectiveBinding, ObjectDirective } from "vue";

interface ClickOutsideElement extends HTMLElement {
  _clickOutside?: (event: Event) => void;
}

const clickOutside: ObjectDirective = {
  mounted(el: ClickOutsideElement, binding: DirectiveBinding) {
    el._clickOutside = (event: Event) => {
      // Check if the click was outside the el and its children
      if (!(el === event.target || el.contains(event.target as Node))) {
        binding.value(event);
      }
    };
    document.addEventListener("click", el._clickOutside);
  },
  updated(el: ClickOutsideElement, binding: DirectiveBinding) {
    // If the binding value changes, update the handler
    if (binding.value !== binding.oldValue) {
      if (el._clickOutside) {
        document.removeEventListener("click", el._clickOutside);
      }
      el._clickOutside = (event: Event) => {
        if (!(el === event.target || el.contains(event.target as Node))) {
          binding.value(event);
        }
      };
      document.addEventListener("click", el._clickOutside);
    }
  },
  unmounted(el: ClickOutsideElement) {
    if (el._clickOutside) {
      document.removeEventListener("click", el._clickOutside);
    }
  },
};

export default clickOutside;
