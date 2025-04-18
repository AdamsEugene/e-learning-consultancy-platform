import type { DirectiveBinding, ObjectDirective } from "vue";

const focus: ObjectDirective = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== false) {
      el.focus();
    }
  },
  updated(el: HTMLElement, binding: DirectiveBinding) {
    if (binding.value !== false) {
      el.focus();
    }
  },
};

export default focus;
