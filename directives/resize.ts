import type { DirectiveBinding, ObjectDirective } from "vue";

interface ResizeElement extends HTMLElement {
  _resizeHandler?: (event: MouseEvent) => void;
  _stopResizeHandler?: () => void;
  _startX?: number;
  _startY?: number;
  _startWidth?: number;
  _startHeight?: number;
  _isResizing?: boolean;
}

const resize: ObjectDirective = {
  mounted(el: ResizeElement, binding: DirectiveBinding) {
    // Add resize handle
    const handle = document.createElement("div");
    handle.className = "v-resize-handle";
    handle.style.position = "absolute";
    handle.style.width = "10px";
    handle.style.height = "10px";
    handle.style.bottom = "0";
    handle.style.right = "0";
    handle.style.cursor = "se-resize";
    handle.style.backgroundColor = "transparent";
    el.style.position = "relative";
    el.appendChild(handle);

    // Define resize handler
    el._resizeHandler = (event: MouseEvent) => {
      if (!el._isResizing) return;

      const deltaX = event.clientX - (el._startX || 0);
      const deltaY = event.clientY - (el._startY || 0);

      const newWidth = (el._startWidth || 0) + deltaX;
      const newHeight = (el._startHeight || 0) + deltaY;

      // Apply min constraints if provided
      const minWidth = binding.value?.minWidth || 100;
      const minHeight = binding.value?.minHeight || 100;

      if (newWidth >= minWidth) {
        el.style.width = `${newWidth}px`;
      }

      if (newHeight >= minHeight) {
        el.style.height = `${newHeight}px`;
      }

      // Emit resize event if callback provided
      if (binding.value?.onResize) {
        binding.value.onResize({
          width: el.offsetWidth,
          height: el.offsetHeight,
        });
      }
    };

    // Define stop resize handler
    el._stopResizeHandler = () => {
      el._isResizing = false;
      document.removeEventListener(
        "mousemove",
        el._resizeHandler as EventListener
      );
      document.removeEventListener(
        "mouseup",
        el._stopResizeHandler as EventListener
      );

      // Restore text selection
      document.body.style.userSelect = "";
    };

    // Start resize
    const startResize = (event: MouseEvent) => {
      el._isResizing = true;
      el._startX = event.clientX;
      el._startY = event.clientY;
      el._startWidth = el.offsetWidth;
      el._startHeight = el.offsetHeight;

      document.addEventListener(
        "mousemove",
        el._resizeHandler as EventListener
      );
      document.addEventListener(
        "mouseup",
        el._stopResizeHandler as EventListener
      );

      // Prevent text selection during resize
      document.body.style.userSelect = "none";
    };

    handle.addEventListener("mousedown", startResize);
  },
  unmounted(el: ResizeElement) {
    if (el._resizeHandler) {
      document.removeEventListener(
        "mousemove",
        el._resizeHandler as EventListener
      );
    }
    if (el._stopResizeHandler) {
      document.removeEventListener(
        "mouseup",
        el._stopResizeHandler as EventListener
      );
    }
  },
};

export default resize;
