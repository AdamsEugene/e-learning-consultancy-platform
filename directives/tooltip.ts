import type { DirectiveBinding, ObjectDirective } from "vue";

interface TooltipElement extends HTMLElement {
  _tooltip?: HTMLElement;
  _tooltipHandler?: (event: MouseEvent) => void;
}

const tooltip: ObjectDirective = {
  mounted(el: TooltipElement, binding: DirectiveBinding) {
    // Create tooltip element
    const tooltip = document.createElement("div");
    tooltip.className = "v-tooltip";
    tooltip.textContent = binding.value;
    tooltip.style.position = "absolute";
    tooltip.style.zIndex = "9999";
    tooltip.style.backgroundColor = "rgba(0, 0, 0, 0.8)";
    tooltip.style.color = "white";
    tooltip.style.padding = "5px 10px";
    tooltip.style.borderRadius = "4px";
    tooltip.style.fontSize = "14px";
    tooltip.style.pointerEvents = "none";
    tooltip.style.transition = "opacity 0.2s";
    tooltip.style.opacity = "0";
    document.body.appendChild(tooltip);

    el._tooltip = tooltip;

    // Show tooltip on mouseenter
    el._tooltipHandler = (_event: MouseEvent) => {
      const rect = el.getBoundingClientRect();
      tooltip.style.top = `${rect.top - tooltip.offsetHeight - 10}px`;
      tooltip.style.left = `${
        rect.left + (rect.width - tooltip.offsetWidth) / 2
      }px`;
      tooltip.style.opacity = "1";
    };

    el.addEventListener("mouseenter", el._tooltipHandler);

    // Hide tooltip on mouseleave
    el.addEventListener("mouseleave", () => {
      tooltip.style.opacity = "0";
    });
  },
  updated(el: TooltipElement, binding: DirectiveBinding) {
    if (el._tooltip && binding.value !== binding.oldValue) {
      el._tooltip.textContent = binding.value;
    }
  },
  unmounted(el: TooltipElement) {
    if (el._tooltipHandler) {
      el.removeEventListener("mouseenter", el._tooltipHandler);
    }
    if (el._tooltip && el._tooltip.parentNode) {
      el._tooltip.parentNode.removeChild(el._tooltip);
    }
  },
};

export default tooltip;
