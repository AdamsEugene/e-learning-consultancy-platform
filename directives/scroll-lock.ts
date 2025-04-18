import type { DirectiveBinding, ObjectDirective } from "vue";

const scrollLock: ObjectDirective = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== false) {
      document.body.style.overflow = "hidden";
    }
  },
  updated(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== false) {
      document.body.style.overflow = "hidden";
    } else {
      document.body.style.overflow = "";
    }
  },
  unmounted(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== false) {
      document.body.style.overflow = "";
    }
  },
};

export default scrollLock;
