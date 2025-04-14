<!-- components/dashboard/settings/PaymentMethodsList.vue -->
<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";

// Define props
defineProps({
  // If needed, add props here
});

// Define emits for parent communication
const emit = defineEmits(["update-message"]);

// State for payment methods
const paymentMethods = ref([
  {
    id: 1,
    type: "card",
    cardType: "visa",
    last4: "4242",
    expiryMonth: "12",
    expiryYear: "2025",
    isDefault: true,
    name: "Emily Johnson",
  },
  {
    id: 2,
    type: "paypal",
    email: "emily.johnson@example.com",
    isDefault: false,
  },
]);

// Form states
const isSubmitting = ref(false);
const isAddingNewMethod = ref(false);
const isEditingMethod = ref(false);
const currentEditId = ref<number | null>(null);

// Form data for new payment method
const newPaymentMethod = reactive({
  type: "card",
  cardNumber: "",
  cardHolder: "",
  expiryMonth: "",
  expiryYear: "",
  cvv: "",
  makeDefault: false,
  email: "",
});

// Modal state
const showDeleteModal = ref(false);
const methodToDelete = ref<number | null>(null);

// Validation states
const errors = reactive({
  cardNumber: "",
  cardHolder: "",
  expiry: "",
  cvv: "",
  email: "",
});

// Clear form data
const resetForm = () => {
  newPaymentMethod.type = "card";
  newPaymentMethod.cardNumber = "";
  newPaymentMethod.cardHolder = "";
  newPaymentMethod.expiryMonth = "";
  newPaymentMethod.expiryYear = "";
  newPaymentMethod.cvv = "";
  newPaymentMethod.makeDefault = false;
  newPaymentMethod.email = "";

  // Clear validation errors
  Object.keys(errors).forEach((key) => {
    errors[key as keyof typeof errors] = "";
  });
};

// Start add new payment method flow
const startAddNewMethod = () => {
  resetForm();
  isAddingNewMethod.value = true;
  isEditingMethod.value = false;
};

// Cancel adding new payment method
const cancelAddNewMethod = () => {
  isAddingNewMethod.value = false;
  resetForm();
};

// Start edit payment method flow
const startEditMethod = (id: number) => {
  const method = paymentMethods.value.find((m) => m.id === id);
  if (!method) return;

  resetForm();

  // Populate form with existing data
  if (method.type === "card") {
    newPaymentMethod.type = "card";
    newPaymentMethod.cardHolder = method.name;
    newPaymentMethod.expiryMonth = method.expiryMonth;
    newPaymentMethod.expiryYear = method.expiryYear;
    newPaymentMethod.makeDefault = method.isDefault;
  } else if (method.type === "paypal") {
    newPaymentMethod.type = "paypal";
    newPaymentMethod.email = method.email;
    newPaymentMethod.makeDefault = method.isDefault;
  }

  isEditingMethod.value = true;
  isAddingNewMethod.value = true;
  currentEditId.value = id;
};

// Cancel editing payment method
const cancelEditMethod = () => {
  isEditingMethod.value = false;
  isAddingNewMethod.value = false;
  currentEditId.value = null;
  resetForm();
};

// Validate form
const validateForm = (): boolean => {
  let isValid = true;

  // Reset errors
  Object.keys(errors).forEach((key) => {
    errors[key as keyof typeof errors] = "";
  });

  if (newPaymentMethod.type === "card") {
    // Validate card number (simplified validation)
    if (
      !newPaymentMethod.cardNumber ||
      newPaymentMethod.cardNumber.replace(/\s/g, "").length !== 16
    ) {
      errors.cardNumber = "Please enter a valid 16-digit card number";
      isValid = false;
    }

    // Validate card holder
    if (!newPaymentMethod.cardHolder) {
      errors.cardHolder = "Card holder name is required";
      isValid = false;
    }

    // Validate expiry date
    if (!newPaymentMethod.expiryMonth || !newPaymentMethod.expiryYear) {
      errors.expiry = "Valid expiry date is required";
      isValid = false;
    } else {
      const currentYear = new Date().getFullYear();
      const currentMonth = new Date().getMonth() + 1;
      const expiryYear = parseInt(newPaymentMethod.expiryYear);
      const expiryMonth = parseInt(newPaymentMethod.expiryMonth);

      if (
        expiryYear < currentYear ||
        (expiryYear === currentYear && expiryMonth < currentMonth)
      ) {
        errors.expiry = "Card has expired";
        isValid = false;
      }
    }

    // Validate CVV
    if (!newPaymentMethod.cvv || !/^\d{3,4}$/.test(newPaymentMethod.cvv)) {
      errors.cvv = "Valid CVV is required (3-4 digits)";
      isValid = false;
    }
  } else if (newPaymentMethod.type === "paypal") {
    // Validate email
    if (
      !newPaymentMethod.email ||
      !/\S+@\S+\.\S+/.test(newPaymentMethod.email)
    ) {
      errors.email = "Valid email address is required";
      isValid = false;
    }
  }

  return isValid;
};

// Submit new payment method
const submitPaymentMethod = async () => {
  if (!validateForm()) return;

  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    if (isEditingMethod.value && currentEditId.value !== null) {
      // Update existing payment method
      const index = paymentMethods.value.findIndex(
        (m) => m.id === currentEditId.value
      );
      if (index !== -1) {
        const updatedMethod = { ...paymentMethods.value[index] };

        if (updatedMethod.type === "card") {
          updatedMethod.name = newPaymentMethod.cardHolder;
          updatedMethod.expiryMonth = newPaymentMethod.expiryMonth;
          updatedMethod.expiryYear = newPaymentMethod.expiryYear;
        } else if (updatedMethod.type === "paypal") {
          updatedMethod.email = newPaymentMethod.email;
        }

        // Handle default payment method
        if (newPaymentMethod.makeDefault && !updatedMethod.isDefault) {
          // Unset current default
          paymentMethods.value.forEach((m) => {
            if (m.id !== currentEditId.value) {
              m.isDefault = false;
            }
          });
          updatedMethod.isDefault = true;
        }

        paymentMethods.value[index] = updatedMethod;
        emit(
          "update-message",
          "success",
          "Payment method updated successfully"
        );
      }
    } else {
      // Add new payment method
      const newId = Math.max(0, ...paymentMethods.value.map((m) => m.id)) + 1;

      if (newPaymentMethod.type === "card") {
        // Format the card number for last4
        const cardNumberClean = newPaymentMethod.cardNumber.replace(/\s/g, "");
        const last4 = cardNumberClean.substring(cardNumberClean.length - 4);

        // Determine card type (simplified)
        let cardType = "unknown";
        if (/^4/.test(cardNumberClean)) cardType = "visa";
        else if (/^5[1-5]/.test(cardNumberClean)) cardType = "mastercard";
        else if (/^3[47]/.test(cardNumberClean)) cardType = "amex";
        else if (/^6(?:011|5)/.test(cardNumberClean)) cardType = "discover";

        // If setting as default, update other cards
        if (newPaymentMethod.makeDefault) {
          paymentMethods.value.forEach((m) => {
            m.isDefault = false;
          });
        }

        // Add new card
        paymentMethods.value.push({
          id: newId,
          type: "card",
          cardType,
          last4,
          expiryMonth: newPaymentMethod.expiryMonth,
          expiryYear: newPaymentMethod.expiryYear,
          isDefault:
            newPaymentMethod.makeDefault || paymentMethods.value.length === 0,
          name: newPaymentMethod.cardHolder,
        });
      } else if (newPaymentMethod.type === "paypal") {
        // If setting as default, update other methods
        if (newPaymentMethod.makeDefault) {
          paymentMethods.value.forEach((m) => {
            m.isDefault = false;
          });
        }

        // Add new PayPal account
        paymentMethods.value.push({
          id: newId,
          type: "paypal",
          email: newPaymentMethod.email,
          isDefault:
            newPaymentMethod.makeDefault || paymentMethods.value.length === 0,
        });
      }

      emit("update-message", "success", "Payment method added successfully");
    }

    // Reset state
    isAddingNewMethod.value = false;
    isEditingMethod.value = false;
    currentEditId.value = null;
    resetForm();
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem processing your request"
    );
  } finally {
    isSubmitting.value = false;
  }
};

// Open delete confirmation modal
const confirmDelete = (id: number) => {
  methodToDelete.value = id;
  showDeleteModal.value = true;
};

// Cancel delete
const cancelDelete = () => {
  methodToDelete.value = null;
  showDeleteModal.value = false;
};

// Delete payment method
const deletePaymentMethod = async () => {
  if (methodToDelete.value === null) return;

  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    const deletedMethod = paymentMethods.value.find(
      (m) => m.id === methodToDelete.value
    );
    const wasDefault = deletedMethod?.isDefault;

    // Remove the payment method
    paymentMethods.value = paymentMethods.value.filter(
      (m) => m.id !== methodToDelete.value
    );

    // If the deleted method was the default and we have other methods, make another one default
    if (wasDefault && paymentMethods.value.length > 0) {
      paymentMethods.value[0].isDefault = true;
    }

    emit("update-message", "success", "Payment method removed successfully");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem removing the payment method"
    );
  } finally {
    isSubmitting.value = false;
    showDeleteModal.value = false;
    methodToDelete.value = null;
  }
};

// Set as default payment method
const setAsDefault = async (id: number) => {
  isSubmitting.value = true;

  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 1000));

    // Update default status
    paymentMethods.value.forEach((method) => {
      method.isDefault = method.id === id;
    });

    emit("update-message", "success", "Default payment method updated");
  } catch (error) {
    emit(
      "update-message",
      "error",
      "There was a problem updating your default payment method"
    );
  } finally {
    isSubmitting.value = false;
  }
};

// Format card number with spaces
const formatCardNumber = (event: Event) => {
  const input = event.target as HTMLInputElement;
  let value = input.value.replace(/\s/g, "").replace(/\D/g, "");

  if (value.length > 16) {
    value = value.substring(0, 16);
  }

  // Re-add spaces every 4 digits
  const parts = [];
  for (let i = 0; i < value.length; i += 4) {
    parts.push(value.substring(i, i + 4));
  }

  input.value = parts.join(" ");
  newPaymentMethod.cardNumber = input.value;
};

// Get card type icon based on card type
const getCardIcon = (type: string) => {
  switch (type) {
    case "visa":
      return '<svg class="h-8 w-8" viewBox="0 0 48 48" fill="currentColor"><path fill="#1565C0" d="M45,35c0,2.209-1.791,4-4,4H7c-2.209,0-4-1.791-4-4V13c0-2.209,1.791-4,4-4h34c2.209,0,4,1.791,4,4V35z"/><path fill="#FFF" d="M15.186 19l-2.626 7.832c0 0-.667-3.313-.733-3.729-1.495-3.411-3.701-3.221-3.701-3.221L10.726 30v-.002h3.161L18.258 19H15.186zM17.689 30L20.56 30 22.296 19 19.389 19zM38.008 19h-3.021l-4.71 11h2.852l.588-1.571h3.596L37.619 30h2.613L38.008 19zM34.513 26.328l1.563-4.157.818 4.157H34.513zM26.369 22.206c0-.606.498-1.057 1.926-1.057.928 0 1.991.177 1.991.177l.466-2.242c0 0-1.358-.515-2.691-.515-3.019 0-4.576 1.444-4.576 3.182 0 3.442 4.252 2.858 4.252 4.204 0 .654-.559.909-1.926.909-1.719 0-2.771-.384-2.771-.384l-.466 2.166c0 0 1.063.49 3.019.49 2.691 0 4.695-1.53 4.695-3.289C30.288 22.895 26.369 23.554 26.369 22.206z"/></svg>';
    case "mastercard":
      return '<svg class="h-8 w-8" viewBox="0 0 48 48" fill="currentColor"><path fill="#3F51B5" d="M45,35c0,2.209-1.791,4-4,4H7c-2.209,0-4-1.791-4-4V13c0-2.209,1.791-4,4-4h34c2.209,0,4,1.791,4,4V35z"/><path fill="#FFC107" d="M30,24c0,3.314-2.686,6-6,6s-6-2.686-6-6s2.686-6,6-6S30,20.686,30,24z"/><path fill="#FF3D00" d="M22.001,30c-1.835,0-3.462-0.83-4.571-2.128c-0.432-0.506-0.797-1.077-1.079-1.696	c-0.613-1.345-0.953-2.84-0.953-4.407c0-1.402,0.279-2.733,0.784-3.956c0.189-0.458,0.41-0.898,0.663-1.316	C18.393,14.283,20.76,13,23.423,13C25.396,13,27.199,13.658,28.6,14.762c0.527,0.413,1.010,0.886,1.433,1.413	c0.623,0.766,1.126,1.635,1.47,2.586c0.378,1.04,0.584,2.161,0.584,3.329c0,0.283-0.013,0.562-0.039,0.838	c-0.304,3.368-2.636,6.156-5.813,7.253c-0.768,0.265-1.59,0.41-2.441,0.41C23.793,30,22.001,30,22.001,30z"/><path fill="#FFFFFF" d="M30.405,26.915c-0.53,0.518-1.189,0.782-1.978,0.782h-9.851v-0.106h9.851C29.302,27.591,29.936,27.392,30.405,26.915L30.405,26.915z"/></svg>';
    case "amex":
      return '<svg class="h-8 w-8" viewBox="0 0 48 48" fill="currentColor"><path fill="#1976D2" d="M45,35c0,2.209-1.791,4-4,4H7c-2.209,0-4-1.791-4-4V13c0-2.209,1.791-4,4-4h34c2.209,0,4,1.791,4,4V35z"/><path fill="#FFFFFF" d="M22.255 20.295h3.542v-5.76h-4.158l-2.768 3.06-2.744-3.06h-4.118v10.136h4.087v-5.54l2.857 3.456 2.704-3.397v5.481h1.802v-4.375zM30.355 14.535l-4.286 10.136h2.208l.666-1.63h4.454l.705 1.63h2.324l-4.325-10.136h-1.746zm.401 3.019l1.351 3.278h-2.625l1.274-3.278zM12.522 27.415v-10.136h-4.699v10.136h4.699zM27.919 21.225h2.208v6.19h4.777v-6.19h2.284v-3.766h-9.269z"/></svg>';
    case "discover":
      return '<svg class="h-8 w-8" viewBox="0 0 48 48" fill="currentColor"><path fill="#E1E7EA" d="M45,35c0,2.2-1.8,4-4,4H7c-2.2,0-4-1.8-4-4V13c0-2.2,1.8-4,4-4h34c2.2,0,4,1.8,4,4V35z"/><path fill="#FF6D00" d="M45,35c0,2.2-1.8,4-4,4H16c0,0,20-10,24-12c2-1,4-2,4-4c0-2-1-2.9-4-4c-2-0.7-4-2-4-2c-2-1-4-2-4-4c0-2,0.1-2.9,4-4c2-0.7,4-2,4-2V13c0-2.2,1.8-4,4-4h1c2.2,0,4,1.8,4,4V35z"/><path fill="#0097A7" d="M21.3,30.2c0.5,0.2,1.2,0.3,1.9,0.3c2.2,0,3.8-1.1,3.8-3.2c0-1.4-0.8-2.5-2.6-3.2c-1.2-0.5-1.7-0.7-1.7-1.3c0-0.4,0.4-0.8,1.3-0.8c0.9,0,1.5,0.1,1.9,0.3l0.4-1.7c-0.5-0.2-1.1-0.3-1.9-0.3c-2.2,0-3.6,1.2-3.6,3.1c0,1.5,1.1,2.4,2.7,3c1.1,0.4,1.5,0.7,1.5,1.4c0,0.5-0.4,0.9-1.4,0.9c-0.8,0-1.7-0.2-2.2-0.5L21.3,30.2z"/><path fill="#0097A7" d="M26.7,27.4c0,2,1,3.2,2.7,3.2c0.7,0,1.3-0.1,1.9-0.5l-0.3-1.5c-0.3,0.2-0.6,0.3-1.1,0.3c-0.8,0-1.2-0.6-1.2-1.6c0-0.9,0.4-1.6,1.2-1.6c0.4,0,0.7,0.1,1,0.3l0.4-1.5c-0.4-0.2-0.9-0.3-1.5-0.3C27.6,24.1,26.7,25.5,26.7,27.4z"/><path fill="#0097A7" d="M32.5 30.4L34 24.3 31.8 24.3 30.3 30.4z"/><path fill="#0097A7" d="M15,24.3l-1.9,6.1h2l0.3-1h1.8l0.2,1h2l-1.5-6.1H15z M15.8,27.9l0.3-1.4c0.1-0.4,0.2-0.8,0.2-1.2c0.1,0.4,0.1,0.8,0.2,1.2l0.3,1.4H15.8z"/></svg>';
    default:
      return '<svg class="h-8 w-8" viewBox="0 0 48 48" fill="currentColor"><path fill="#CFD8DC" d="M45,35c0,2.209-1.791,4-4,4H7c-2.209,0-4-1.791-4-4V13c0-2.209,1.791-4,4-4h34c2.209,0,4,1.791,4,4V35z"/><path fill="#546E7A" d="M11 21H25V23H11zM11 25H23V27H11zM14 17H22V19H14z"/></svg>';
  }
};

onMounted(() => {
  // If needed, fetch payment methods from API
});
</script>

<template>
  <div class="payment-methods p-6">
    <!-- Section header -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-xl font-semibold text-gray-900">Payment Methods</h2>
      <button
        v-if="!isAddingNewMethod"
        @click="startAddNewMethod"
        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2"
          viewBox="0 0 20 20"
          fill="currentColor"
        >
          <path
            fill-rule="evenodd"
            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
            clip-rule="evenodd"
          />
        </svg>
        Add Payment Method
      </button>
    </div>

    <!-- Add/Edit Payment Method Form -->
    <div
      v-if="isAddingNewMethod"
      class="mb-8 bg-gray-50 rounded-lg p-6 border border-gray-200"
    >
      <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          {{
            isEditingMethod ? "Edit Payment Method" : "Add New Payment Method"
          }}
        </h3>
        <p class="text-sm text-gray-500 mt-1">
          {{
            isEditingMethod
              ? "Update your payment information below"
              : "Enter your payment information securely"
          }}
        </p>
      </div>

      <div class="space-y-6">
        <!-- Payment method selector -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2"
            >Payment Method Type</label
          >
          <div class="flex space-x-4">
            <label class="relative flex items-center">
              <input
                type="radio"
                v-model="newPaymentMethod.type"
                value="card"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                :disabled="isEditingMethod"
              />
              <span class="ml-2 block text-sm text-gray-700"
                >Credit/Debit Card</span
              >
            </label>
            <label class="relative flex items-center">
              <input
                type="radio"
                v-model="newPaymentMethod.type"
                value="paypal"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                :disabled="isEditingMethod"
              />
              <span class="ml-2 block text-sm text-gray-700">PayPal</span>
            </label>
          </div>
        </div>

        <!-- Credit/Debit Card form -->
        <div v-if="newPaymentMethod.type === 'card'" class="space-y-4">
          <!-- Card Number -->
          <div v-if="!isEditingMethod">
            <label
              for="cardNumber"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Card Number</label
            >
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                id="cardNumber"
                type="text"
                :value="newPaymentMethod.cardNumber"
                @input="formatCardNumber"
                placeholder="1234 5678 9012 3456"
                class="block w-full pr-10 focus:outline-none sm:text-sm rounded-md"
                :class="
                  errors.cardNumber
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                "
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg
                  v-if="errors.cardNumber"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-red-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
            <p v-if="errors.cardNumber" class="mt-2 text-sm text-red-600">
              {{ errors.cardNumber }}
            </p>
          </div>

          <!-- Card Holder Name -->
          <div>
            <label
              for="cardHolder"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Card Holder Name</label
            >
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                id="cardHolder"
                type="text"
                v-model="newPaymentMethod.cardHolder"
                placeholder="Name as it appears on card"
                class="block w-full pr-10 focus:outline-none sm:text-sm rounded-md"
                :class="
                  errors.cardHolder
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                "
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg
                  v-if="errors.cardHolder"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-red-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
            <p v-if="errors.cardHolder" class="mt-2 text-sm text-red-600">
              {{ errors.cardHolder }}
            </p>
          </div>

          <!-- Expiry Date and CVV -->
          <div class="grid grid-cols-2 gap-4">
            <!-- Expiry Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Expiry Date</label
              >
              <div class="mt-1 flex space-x-2">
                <div class="relative rounded-md shadow-sm w-1/2">
                  <select
                    v-model="newPaymentMethod.expiryMonth"
                    class="block w-full py-2 pl-3 pr-10 focus:outline-none sm:text-sm rounded-md"
                    :class="
                      errors.expiry
                        ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                    "
                  >
                    <option value="">MM</option>
                    <option
                      v-for="month in 12"
                      :key="month"
                      :value="month < 10 ? `0${month}` : `${month}`"
                    >
                      {{ month < 10 ? `0${month}` : month }}
                    </option>
                  </select>
                </div>
                <div class="relative rounded-md shadow-sm w-1/2">
                  <select
                    v-model="newPaymentMethod.expiryYear"
                    class="block w-full py-2 pl-3 pr-10 focus:outline-none sm:text-sm rounded-md"
                    :class="
                      errors.expiry
                        ? 'border-red-300 text-red-900 focus:ring-red-500 focus:border-red-500'
                        : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                    "
                  >
                    <option value="">YY</option>
                    <option
                      v-for="offset in 10"
                      :key="offset"
                      :value="`${new Date().getFullYear() + offset - 1}`"
                    >
                      {{ new Date().getFullYear() + offset - 1 }}
                    </option>
                  </select>
                </div>
              </div>
              <p v-if="errors.expiry" class="mt-2 text-sm text-red-600">
                {{ errors.expiry }}
              </p>
            </div>

            <!-- CVV -->
            <div v-if="!isEditingMethod">
              <label
                for="cvv"
                class="block text-sm font-medium text-gray-700 mb-1"
                >CVV</label
              >
              <div class="mt-1 relative rounded-md shadow-sm">
                <input
                  id="cvv"
                  type="text"
                  v-model="newPaymentMethod.cvv"
                  placeholder="123"
                  maxlength="4"
                  class="block w-full pr-10 focus:outline-none sm:text-sm rounded-md"
                  :class="
                    errors.cvv
                      ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                      : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                  "
                />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <svg
                    v-if="errors.cvv"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-red-500"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
              </div>
              <p v-if="errors.cvv" class="mt-2 text-sm text-red-600">
                {{ errors.cvv }}
              </p>
            </div>
          </div>
        </div>

        <!-- PayPal form -->
        <div v-if="newPaymentMethod.type === 'paypal'" class="space-y-4">
          <!-- PayPal Email -->
          <div>
            <label
              for="paypalEmail"
              class="block text-sm font-medium text-gray-700 mb-1"
              >PayPal Email</label
            >
            <div class="mt-1 relative rounded-md shadow-sm">
              <input
                id="paypalEmail"
                type="email"
                v-model="newPaymentMethod.email"
                placeholder="your-email@example.com"
                class="block w-full pr-10 focus:outline-none sm:text-sm rounded-md"
                :class="
                  errors.email
                    ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
                    : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500'
                "
              />
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg
                  v-if="errors.email"
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5 text-red-500"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
            <p v-if="errors.email" class="mt-2 text-sm text-red-600">
              {{ errors.email }}
            </p>
          </div>
        </div>

        <!-- Default payment method toggle -->
        <div class="flex items-center">
          <input
            id="makeDefault"
            type="checkbox"
            v-model="newPaymentMethod.makeDefault"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
          />
          <label for="makeDefault" class="ml-2 block text-sm text-gray-700">
            Set as default payment method
          </label>
        </div>

        <!-- Form actions -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="isEditingMethod ? cancelEditMethod() : cancelAddNewMethod()"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            :disabled="isSubmitting"
          >
            Cancel
          </button>
          <button
            type="submit"
            @click="submitPaymentMethod"
            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex items-center"
            :disabled="isSubmitting"
          >
            <svg
              v-if="isSubmitting"
              class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            {{
              isSubmitting
                ? "Saving..."
                : isEditingMethod
                ? "Update Method"
                : "Add Method"
            }}
          </button>
        </div>
      </div>
    </div>

    <!-- Payment methods list -->
    <div
      v-if="!isAddingNewMethod && paymentMethods.length > 0"
      class="space-y-4"
    >
      <div
        v-for="method in paymentMethods"
        :key="method.id"
        class="bg-white rounded-lg border border-gray-200 p-4 transition-all duration-200 hover:shadow-md"
      >
        <div class="flex items-center">
          <!-- Card/PayPal icon -->
          <div
            class="flex-shrink-0 mr-4 text-gray-400"
            v-if="method.type === 'card'"
            v-html="getCardIcon(method.cardType)"
          ></div>
          <div
            class="flex-shrink-0 mr-4 text-blue-600"
            v-else-if="method.type === 'paypal'"
          >
            <svg class="h-8 w-8" viewBox="0 0 48 48" fill="currentColor">
              <path
                d="M39.141,14.966C38.291,9.564,34.286,7,29.027,7H20.429c-0.864,0-1.601,0.629-1.736,1.482 L15.372,36.271c-0.101,0.641,0.399,1.222,1.049,1.222h5.164c0.863,0,1.6-0.629,1.736-1.482l0.887-5.611 c0.135-0.853,0.872-1.482,1.736-1.482h2.506c5.759,0,9.586-2.779,10.504-8.275c0.396-2.386,0.167-4.371-0.903-5.748 C41.294,16.041,40.36,15.38,39.141,14.966z M32.273,22.905c-0.503,3.306-3.031,3.306-5.474,3.306h-1.393l0.979-6.189 c0.059-0.372,0.38-0.64,0.756-0.64h0.637c1.667,0,3.242,0,4.056,0.95C32.364,20.849,32.497,21.675,32.273,22.905z"
                fill="currentColor"
              ></path>
              <path
                d="M17.791,24.201c-0.075-0.05-0.15-0.102-0.224-0.154l-0.014-0.01C17.639,24.087,17.715,24.139,17.791,24.201z"
                fill="currentColor"
              ></path>
              <path
                d="M18.999,24.905c-0.127-0.037-0.252-0.077-0.376-0.122C18.747,24.828,18.872,24.868,18.999,24.905z"
                fill="currentColor"
              ></path>
              <path
                d="M24.88,14.966C24.029,9.564,20.024,7,14.765,7H6.167C5.303,7,4.566,7.629,4.431,8.482L1.109,36.271 c-0.101,0.641,0.399,1.222,1.049,1.222h5.164c0.863,0,1.6-0.629,1.736-1.482l0.887-5.611c0.135-0.853,0.872-1.482,1.736-1.482 h2.506c5.759,0,9.586-2.779,10.504-8.275c0.396-2.386,0.167-4.371-0.903-5.748C27.033,16.041,26.099,15.38,24.88,14.966z M18.012,22.905c-0.503,3.306-3.031,3.306-5.474,3.306h-1.393l0.979-6.189c0.059-0.372,0.38-0.64,0.756-0.64h0.637 c1.667,0,3.242,0,4.056,0.95C18.103,20.849,18.236,21.675,18.012,22.905z"
                fill="currentColor"
              ></path>
            </svg>
          </div>

          <!-- Card/PayPal info -->
          <div class="flex-grow">
            <div class="flex items-center">
              <h3 class="font-medium text-gray-900">
                <span v-if="method.type === 'card'"
                  >•••• •••• •••• {{ method.last4 }}</span
                >
                <span v-else-if="method.type === 'paypal'"
                  >PayPal ({{ method.email }})</span
                >
              </h3>
              <span
                v-if="method.isDefault"
                class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
              >
                Default
              </span>
            </div>
            <div class="text-sm text-gray-500">
              <span v-if="method.type === 'card'">
                {{ method.name }} • Expires {{ method.expiryMonth }}/{{
                  method.expiryYear
                }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex-shrink-0 flex items-center space-x-2">
            <button
              v-if="!method.isDefault"
              @click="setAsDefault(method.id)"
              class="inline-flex items-center p-1.5 border border-transparent text-xs rounded text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              :disabled="isSubmitting"
            >
              Set Default
            </button>
            <button
              @click="startEditMethod(method.id)"
              class="inline-flex items-center p-1.5 border border-transparent text-xs rounded text-gray-600 hover:text-gray-800 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-500"
              :disabled="isSubmitting"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                />
              </svg>
            </button>
            <button
              @click="confirmDelete(method.id)"
              class="inline-flex items-center p-1.5 border border-transparent text-xs rounded text-red-600 hover:text-red-800 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
              :disabled="isSubmitting"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <div
      v-if="!isAddingNewMethod && paymentMethods.length === 0"
      class="bg-gray-50 rounded-lg p-8 text-center"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-12 w-12 mx-auto text-gray-400"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
        />
      </svg>
      <h3 class="mt-2 text-lg font-medium text-gray-900">No payment methods</h3>
      <p class="mt-1 text-sm text-gray-500">
        Add a payment method to make purchases and manage your subscriptions.
      </p>
      <div class="mt-4">
        <button
          @click="startAddNewMethod"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 mr-2"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
              clip-rule="evenodd"
            />
          </svg>
          Add Payment Method
        </button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="showDeleteModal"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
      >
        <!-- Background overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
        ></div>

        <!-- Modal panel -->
        <div
          class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
        >
          <div>
            <div
              class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6 text-red-600"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
            </div>
            <div class="mt-3 text-center sm:mt-5">
              <h3
                class="text-lg leading-6 font-medium text-gray-900"
                id="modal-title"
              >
                Remove Payment Method
              </h3>
              <div class="mt-2">
                <p class="text-sm text-gray-500">
                  Are you sure you want to remove this payment method? This
                  action cannot be undone.
                </p>
              </div>
            </div>
          </div>
          <div
            class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense"
          >
            <button
              type="button"
              @click="deletePaymentMethod"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm"
              :disabled="isSubmitting"
            >
              <svg
                v-if="isSubmitting"
                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
              >
                <circle
                  class="opacity-25"
                  cx="12"
                  cy="12"
                  r="10"
                  stroke="currentColor"
                  stroke-width="4"
                ></circle>
                <path
                  class="opacity-75"
                  fill="currentColor"
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
              {{ isSubmitting ? "Removing..." : "Remove" }}
            </button>
            <button
              type="button"
              @click="cancelDelete"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
              :disabled="isSubmitting"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Transition animations */
.payment-method-enter-active,
.payment-method-leave-active {
  transition: all 0.3s ease;
}

.payment-method-enter-from,
.payment-method-leave-to {
  opacity: 0;
  transform: translateY(30px);
}

/* Toggle transitions */
input[type="radio"],
input[type="checkbox"] {
  transition: all 0.2s ease;
}
</style>
