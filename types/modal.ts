/* eslint-disable @typescript-eslint/no-explicit-any */
// types/modal.ts

/**
 * Available positions for the modal component
 */
export type ModalPosition =
  | "center"
  | "top"
  | "right"
  | "bottom"
  | "left"
  | "top-right"
  | "top-left"
  | "bottom-right"
  | "bottom-left";

/**
 * Props interface for the Modal component
 */
export interface ModalProps {
  /**
   * Controls if the modal is visible
   * @default false
   */
  modelValue: boolean;

  /**
   * Title for the modal (can be overridden with header slot)
   * @default ''
   */
  title?: string;

  /**
   * Width of the modal (px, rem, %, etc.)
   * @default '500px'
   */
  width?: string;

  /**
   * Maximum width of the modal (px, rem, %, etc.)
   * @default '90vw'
   */
  maxWidth?: string;

  /**
   * Height of the modal (px, rem, %, etc.)
   * @default 'auto'
   */
  height?: string;

  /**
   * Maximum height of the modal (px, rem, %, etc.)
   * @default '90vh'
   */
  maxHeight?: string;

  /**
   * Whether to close modal on Escape key press
   * @default true
   */
  closeOnEsc?: boolean;

  /**
   * Whether to close modal when clicking outside
   * @default true
   */
  closeOnClickOutside?: boolean;

  /**
   * Whether to show close button in header
   * @default true
   */
  showCloseButton?: boolean;

  /**
   * Position of the modal
   * @default 'center'
   */
  position?: ModalPosition;

  /**
   * Additional CSS classes for the modal container
   * @default ''
   */
  modalClass?: string;

  /**
   * Additional CSS classes for the modal content
   * @default ''
   */
  contentClass?: string;

  /**
   * Transition duration in ms
   * @default 300
   */
  transitionDuration?: number;

  /**
   * Whether to scroll content or body
   * @default true
   */
  scrollable?: boolean;

  /**
   * Whether the modal has a footer
   * @default false
   */
  hasFooter?: boolean;

  /**
   * Whether to apply backdrop blur effect
   * @default true
   */
  backdropBlur?: boolean;

  /**
   * z-index value for the modal
   * @default 1000
   */
  zIndex?: number;

  /**
   * Whether to disable animation on initial mount
   * @default false
   */
  disableMountAnimation?: boolean;

  /**
   * Whether the modal is fullscreen
   * @default false
   */
  fullscreen?: boolean;
}

/**
 * Events emitted by the Modal component
 */
export interface ModalEvents {
  /**
   * Emitted when the modal's open state changes
   */
  "update:modelValue": (value: boolean) => void;

  /**
   * Emitted right before the modal starts to open
   */
  beforeOpen: () => void;

  /**
   * Emitted right before the modal starts to close
   */
  beforeClose: () => void;

  /**
   * Emitted when the modal has fully opened
   */
  open: () => void;

  /**
   * Emitted when the modal has fully closed
   */
  close: () => void;
}

/**
 * Slots available in the Modal component
 */
export interface ModalSlots {
  /**
   * Content for the modal header (replaces default title)
   */
  header: () => any;

  /**
   * Content to place in the top-right of the header (before close button)
   */
  "header-actions": () => any;

  /**
   * Default slot for the main content area
   */
  default: () => any;

  /**
   * Content for the modal footer
   * Only rendered if hasFooter is true or slot is provided
   */
  footer: () => any;
}
