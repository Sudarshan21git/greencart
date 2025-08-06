// Testimonial Slider
const testimonialCards = document.querySelectorAll(".testimonial-card");
const dots = document.querySelectorAll(".dot");
let currentTestimonial = 0;
let testimonialInterval;

function showTestimonial(index) {
  // Hide all testimonials
  testimonialCards.forEach((card) => {
    card.classList.remove("active");
  });

  // Deactivate all dots
  dots.forEach((dot) => {
    dot.classList.remove("active");
  });

  // Show selected testimonial and activate dot
  testimonialCards[index].classList.add("active");
  dots[index].classList.add("active");

  // Update current index
  currentTestimonial = index;
}

// Set up dot click handlers
dots.forEach((dot, index) => {
  dot.addEventListener("click", () => {
    showTestimonial(index);
    resetTestimonialInterval();
  });
});

// Auto-rotate testimonials
function startTestimonialInterval() {
  testimonialInterval = setInterval(() => {
    const nextIndex = (currentTestimonial + 1) % testimonialCards.length;
    showTestimonial(nextIndex);
  }, 5000);
}

function resetTestimonialInterval() {
  clearInterval(testimonialInterval);
  startTestimonialInterval();
}

// Initialize testimonial slider
if (testimonialCards.length > 0 && dots.length > 0) {
  startTestimonialInterval();
}

// Unified Add to Cart functionality
document.addEventListener("DOMContentLoaded", () => {
  // Function to show notifications
  function showNotification(message, type) {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll(".notification");
    existingNotifications.forEach((notification) => {
      notification.remove();
    });

    // Create notification element
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.textContent = message;

    // Add to document
    const container =
      document.getElementById("notification-container") || document.body;
    container.appendChild(notification);

    // Show notification
    setTimeout(() => {
      notification.classList.add("show");
    }, 10);

    // Hide and remove after 5 seconds
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => {
        notification.remove();
      }, 300);
    }, 5000);
  }

  // Function to update cart count in header
  function updateCartCount(quantity) {
    const cartCountElement = document.querySelector(".cart-count");
    if (cartCountElement) {
      const currentCount = Number.parseInt(cartCountElement.textContent) || 0;
      cartCountElement.textContent = currentCount + quantity;
    }
  }

  // Add to cart functionality
  const addToCartButtons = document.querySelectorAll(".btn-add-cart");

  addToCartButtons.forEach((button) => {
    if (button.disabled) return; // Skip disabled buttons

    button.addEventListener("click", function () {
      const productId = this.getAttribute("data-productid");
      let quantity = 1; // Default to 1 for quick add

      // Check if we're on a product details page with quantity input
      const quantityInput = document.querySelector(".quantity-input");
      if (quantityInput) {
        quantity = Number.parseInt(quantityInput.value);
        const maxStock = Number.parseInt(
          quantityInput.getAttribute("max") || "0"
        );

        // Validate quantity
        if (isNaN(quantity) || quantity < 1) {
          showNotification("Please select a valid quantity", "error");
          return;
        }

        if (quantity > maxStock) {
          showNotification(`Sorry, only ${maxStock} items available`, "error");
          return;
        }
      }

      console.log("Adding to cart:", productId, quantity);

      // Send AJAX request to add item to cart
      const formData = new FormData();
      formData.append("product_id", productId);
      formData.append("quantity", quantity);

      // Show loading state
      this.disabled = true;
      const originalText = this.textContent;
      this.textContent = "Adding...";

      fetch("../functions/addToCart.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          console.log("Response:", data);
          if (data.success) {
            showNotification(data.message || "Item added to cart!", "success");
            // Update cart count in header if needed
            updateCartCount(quantity);
          } else {
            showNotification(
              data.message || "Failed to add item to cart",
              "error"
            );
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          showNotification("An error occurred. Please try again.", "error");
        })
        .finally(() => {
          // Reset button state
          this.disabled = false;
          this.textContent = originalText;
        });
    });
  });

  // Handle quantity controls on product details page
  const decreaseBtn = document.querySelector(".quantity-btn.decrease");
  const increaseBtn = document.querySelector(".quantity-btn.increase");
  const quantityInput = document.querySelector(".quantity-input");
  const stockWarning = document.getElementById("stock-warning");

  if (decreaseBtn && increaseBtn && quantityInput) {
    const maxStock = Number.parseInt(quantityInput.getAttribute("max") || "0");

    decreaseBtn.addEventListener("click", () => {
      const value = Number.parseInt(quantityInput.value);
      if (value > 1) {
        quantityInput.value = value - 1;
        if (stockWarning) stockWarning.style.display = "none";
      }
    });

    increaseBtn.addEventListener("click", () => {
      const value = Number.parseInt(quantityInput.value);
      if (value < maxStock) {
        quantityInput.value = value + 1;
        if (value + 1 >= maxStock && stockWarning) {
          stockWarning.style.display = "block";
        }
      } else if (stockWarning) {
        stockWarning.style.display = "block";
      }
    });

    quantityInput.addEventListener("change", function () {
      const value = Number.parseInt(this.value);
      if (isNaN(value) || value < 1) {
        this.value = 1;
      } else if (value > maxStock) {
        this.value = maxStock;
        if (stockWarning) stockWarning.style.display = "block";
      } else if (stockWarning) {
        stockWarning.style.display = "none";
      }
    });
  }
});

// filte-group price-range
const priceSlider = document.getElementById("price-range");
const minPrice = document.getElementById("min-price");
const maxPrice = document.getElementById("max-price");
const productCards = document.querySelectorAll(".product-card");

if (priceSlider && minPrice && maxPrice) {
  // Set initial max price based on highest priced product
  let highestPrice = 0;
  productCards.forEach((card) => {
    const priceText = card.querySelector(".product-price").textContent;
    const price = Number.parseFloat(priceText.replace(/[^\d]/g, ""));
    if (price > highestPrice) {
      highestPrice = price;
    }
  });

  priceSlider.max = highestPrice;
  priceSlider.value = highestPrice;
  maxPrice.textContent = "Rs." + highestPrice;

  priceSlider.addEventListener("input", function () {
    const value = this.value;
    maxPrice.textContent = "Rs." + value;

    // Filter products based on price
    productCards.forEach((card) => {
      const priceText = card.querySelector(".product-price").textContent;
      const price = Number.parseFloat(priceText.replace(/[^\d]/g, ""));

      if (price <= value) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  });
}

// Toggle password visibility
const togglePasswordButtons = document.querySelectorAll(".toggle-password");

if (togglePasswordButtons) {
  togglePasswordButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const passwordInput = this.previousElementSibling;
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      // Change the eye icon
      if (type === "text") {
        this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
      } else {
        this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
      }
    });
  });
}

// Password strength meter
const passwordInput = document.getElementById("new-password");
const strengthSegments = document.querySelectorAll(".strength-segment");
const strengthText = document.querySelector(".strength-text");

if (passwordInput && strengthSegments.length) {
  passwordInput.addEventListener("input", function () {
    const password = this.value;
    let strength = 0;

    // Check password length
    if (password.length >= 8) {
      strength += 1;
    }

    // Check for mixed case
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
      strength += 1;
    }

    // Check for numbers
    if (password.match(/\d/)) {
      strength += 1;
    }

    // Check for special characters
    if (password.match(/[^a-zA-Z\d]/)) {
      strength += 1;
    }

    // Update strength meter
    strengthSegments.forEach((segment, index) => {
      segment.className = "strength-segment";
      if (index < strength) {
        if (strength <= 2) {
          segment.classList.add("weak");
        } else if (strength === 3) {
          segment.classList.add("medium");
        } else {
          segment.classList.add("strong");
        }
      }
    });

    // Update strength text
    if (password.length === 0) {
      strengthText.textContent = "Password strength";
    } else if (strength <= 2) {
      strengthText.textContent = "Weak";
    } else if (strength === 3) {
      strengthText.textContent = "Medium";
    } else {
      strengthText.textContent = "Strong";
    }
  });
}
