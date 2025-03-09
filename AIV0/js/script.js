document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('active');
        });
    }

    // Product Slider
    const sliderContainer = document.querySelector('.slider-container');
    const sliderPrev = document.querySelector('.slider-prev');
    const sliderNext = document.querySelector('.slider-next');
    
    if (sliderContainer && sliderPrev && sliderNext) {
        const productCards = document.querySelectorAll('.product-card');
        let currentIndex = 0;
        const cardWidth = 280; // Approximate width of a card including gap
        const visibleCards = Math.floor(sliderContainer.offsetWidth / cardWidth);
        const maxIndex = Math.max(0, productCards.length - visibleCards);

        sliderPrev.addEventListener('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                updateSliderPosition();
            }
        });

        sliderNext.addEventListener('click', function() {
            if (currentIndex < maxIndex) {
                currentIndex++;
                updateSliderPosition();
            }
        });

        function updateSliderPosition() {
            const scrollAmount = currentIndex * cardWidth;
            sliderContainer.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
            
            // Update button states
            sliderPrev.style.opacity = currentIndex === 0 ? '0.5' : '1';
            sliderNext.style.opacity = currentIndex === maxIndex ? '0.5' : '1';
        }

        // Initialize slider button states
        updateSliderPosition();

        // Handle window resize for slider
        window.addEventListener('resize', function() {
            const newVisibleCards = Math.floor(sliderContainer.offsetWidth / cardWidth);
            const newMaxIndex = Math.max(0, productCards.length - newVisibleCards);
            
            // Adjust current index if needed
            if (currentIndex > newMaxIndex) {
                currentIndex = newMaxIndex;
                updateSliderPosition();
            }
        });
    }

    // Testimonial Slider
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    const dots = document.querySelectorAll('.dot');
    
    if (testimonialCards.length > 0 && dots.length > 0) {
        let currentTestimonial = 0;
        let testimonialInterval;

        function showTestimonial(index) {
            // Hide all testimonials
            testimonialCards.forEach(card => {
                card.classList.remove('active');
            });
            
            // Deactivate all dots
            dots.forEach(dot => {
                dot.classList.remove('active');
            });
            
            // Show selected testimonial and activate dot
            testimonialCards[index].classList.add('active');
            dots[index].classList.add('active');
            
            // Update current index
            currentTestimonial = index;
        }

        // Set up dot click handlers
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                showTestimonial(index);
                resetTestimonialInterval();
            });
        });

        // Auto-rotate testimonials
        function startTestimonialInterval() {
            testimonialInterval = setInterval(function() {
                let nextIndex = (currentTestimonial + 1) % testimonialCards.length;
                showTestimonial(nextIndex);
            }, 5000);
        }

        function resetTestimonialInterval() {
            clearInterval(testimonialInterval);
            startTestimonialInterval();
        }

        // Initialize testimonial slider
        startTestimonialInterval();
    }

    // Newsletter Form
    const newsletterForm = document.getElementById('newsletter-form');
    const formMessage = document.querySelector('.form-message');

    if (newsletterForm && formMessage) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            // Simple validation
            if (!isValidEmail(email)) {
                formMessage.textContent = 'Please enter a valid email address.';
                formMessage.style.color = '#e53e3e';
                return;
            }
            
            // Simulate form submission
            formMessage.textContent = 'Thank you for subscribing!';
            formMessage.style.color = '#fff';
            emailInput.value = '';
            
            // In a real application, you would send this data to your server
            console.log('Newsletter subscription:', email);
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.btn-add-cart');
    const cartCount = document.querySelector('.cart-count');
    
    if (addToCartButtons.length > 0 && cartCount) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get product info for cart
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.productId;
                
                // Submit form to add to cart
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'cart.php';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'add';
                
                const productIdInput = document.createElement('input');
                productIdInput.type = 'hidden';
                productIdInput.name = 'product_id';
                productIdInput.value = productId;
                
                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = 'quantity';
                quantityInput.value = '1';
                
                form.appendChild(actionInput);
                form.appendChild(productIdInput);
                form.appendChild(quantityInput);
                
                document.body.appendChild(form);
                form.submit();
            });
        });
    }

    // Wishlist button functionality
    const wishlistButtons = document.querySelectorAll('.wishlist-btn');
    
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.classList.toggle('active');
            
            if (this.classList.contains('active')) {
                this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>`;
                this.style.color = '#e53e3e';
            } else {
                this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>`;
                this.style.color = '';
            }
            
            // Get product info (in a real app, you'd store this)
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            
            console.log(this.classList.contains('active') ? 'Added to wishlist:' : 'Removed from wishlist:', productName);
        });
    });

    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    if (togglePasswordButtons) {
        togglePasswordButtons.forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Change the eye icon
                if (type === 'text') {
                    this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
                } else {
                    this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
                }
            });
        });
    }

    // Password strength meter
    const passwordInput = document.getElementById('password');
    const strengthSegments = document.querySelectorAll('.strength-segment');
    const strengthText = document.querySelector('.strength-text');
    
    if (passwordInput && strengthSegments.length) {
        passwordInput.addEventListener('input', function() {
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
                segment.className = 'strength-segment';
                if (index < strength) {
                    if (strength <= 2) {
                        segment.classList.add('weak');
                    } else if (strength === 3) {
                        segment.classList.add('medium');
                    } else {
                        segment.classList.add('strong');
                    }
                }
            });
            
            // Update strength text
            if (password.length === 0) {
                strengthText.textContent = 'Password strength';
            } else if (strength <= 2) {
                strengthText.textContent = 'Weak';
            } else if (strength === 3) {
                strengthText.textContent = 'Medium';
            } else {
                strengthText.textContent = 'Strong';
            }
        });
    }

    // Set current year in footer
    const currentYearElement = document.getElementById('current-year');
    if (currentYearElement) {
        currentYearElement.textContent = new Date().getFullYear();
    }

    // Add CSS animation class
    const style = document.createElement('style');
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .pulse {
            animation: pulse 0.3s ease-in-out;
        }
    `;
    document.head.appendChild(style);

    // Price Range Slider
    const priceSlider = document.getElementById("price-range");
    const minPrice = document.getElementById("min-price");
    const maxPrice = document.getElementById("max-price");
    
    if (priceSlider && minPrice && maxPrice) {
        priceSlider.addEventListener("input", function() {
            const value = this.value;
            maxPrice.textContent = `$${value}`;
            
            // Submit form to filter products
            const form = document.getElementById('filter-form');
            if (form) {
                form.submit();
            }
        });
    }
});

const priceSlider = document.getElementById("price-range")
const minPrice = document.getElementById("min-price")
const maxPrice = document.getElementById("max-price")
const productCards = document.querySelectorAll(".product-card")

if (priceSlider && minPrice && maxPrice) {
  // Set initial max price based on highest priced product
  let highestPrice = 0
  productCards.forEach((card) => {
    const priceText = card.querySelector(".product-price").textContent
    const price = Number.parseFloat(priceText.replace("$", ""))
    if (price > highestPrice) {
      highestPrice = price
    }
  })

  // Round up to nearest 10
  const roundedMax = Math.ceil(highestPrice / 10) * 10
  priceSlider.max = roundedMax
  priceSlider.value = roundedMax
  maxPrice.textContent = `$${roundedMax}`

  priceSlider.addEventListener("input", function () {
    const value = this.value
    maxPrice.textContent = `$${value}`

    // Filter products based on price
    productCards.forEach((card) => {
      const priceText = card.querySelector(".product-price").textContent
      const price = Number.parseFloat(priceText.replace("$", ""))

      if (price <= value) {
        card.style.display = "block"
      } else {
        card.style.display = "none"
      }
    })
  })
}

// cart
document.addEventListener("DOMContentLoaded", () => {
    // Cart data structure
    let cart = {
      items: [],
      subtotal: 0,
      shipping: 5.99,
      tax: 0,
      total: 0,
    }
  
    // Sample products (in a real app, this would come from a database)
    const products = [
      {
        id: 1,
        name: "Monstera Deliciosa",
        price: 39.99,
        image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=Monstera",
      },
      { id: 2, name: "Snake Plant", price: 24.99, image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=Snake+Plant" },
      { id: 3, name: "Peace Lily", price: 29.99, image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=Peace+Lily" },
      {
        id: 4,
        name: "Fiddle Leaf Fig",
        price: 49.99,
        image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=Fiddle+Leaf",
      },
      { id: 5, name: "Pothos", price: 19.99, image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=Pothos" },
      { id: 6, name: "ZZ Plant", price: 34.99, image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=ZZ+Plant" },
      { id: 7, name: "Aloe Vera", price: 22.99, image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=Aloe+Vera" },
      {
        id: 8,
        name: "Rubber Plant",
        price: 32.99,
        image: "https://placehold.co/300x300/e2f5e2/1a4d1a?text=Rubber+Plant",
      },
    ]
  
    // DOM elements
    const cartContainer = document.getElementById("cart-container")
    const emptyCart = document.getElementById("empty-cart")
    const cartContent = document.getElementById("cart-content")
    const cartItems = document.getElementById("cart-items")
    const cartSubtotal = document.getElementById("cart-subtotal")
    const cartShipping = document.getElementById("cart-shipping")
    const cartTax = document.getElementById("cart-tax")
    const cartTotal = document.getElementById("cart-total")
    const checkoutBtn = document.getElementById("checkout-btn")
    const updateCartBtn = document.getElementById("update-cart")
    const applyCouponBtn = document.getElementById("apply-coupon")
  
    // Checkout elements
    const checkoutSection = document.getElementById("checkout-section")
    const checkoutItems = document.getElementById("checkout-items")
    const checkoutSubtotal = document.getElementById("checkout-subtotal")
    const checkoutShipping = document.getElementById("checkout-shipping")
    const checkoutTax = document.getElementById("checkout-tax")
    const checkoutTotal = document.getElementById("checkout-total")
    const backToCartBtn = document.getElementById("back-to-cart")
    const checkoutForm = document.getElementById("checkout-form")
  
    // Confirmation elements
    const confirmationSection = document.getElementById("confirmation-section")
    const confirmationEmail = document.getElementById("confirmation-email")
    const orderNumber = document.getElementById("order-number")
    const orderDate = document.getElementById("order-date")
    const orderTotal = document.getElementById("order-total")
  
    // Initialize cart from localStorage
    function initCart() {
      const savedCart = localStorage.getItem("cart")
      if (savedCart) {
        cart = JSON.parse(savedCart)
        updateCartDisplay()
      } else {
        // For demo purposes, add some items to the cart
        addToCart(1, 1)
        addToCart(3, 2)
      }
    }
  
    // Add item to cart
    function addToCart(productId, quantity) {
      const product = products.find((p) => p.id === productId)
      if (!product) return
  
      const existingItem = cart.items.find((item) => item.id === productId)
      if (existingItem) {
        existingItem.quantity += quantity
      } else {
        cart.items.push({
          id: product.id,
          name: product.name,
          price: product.price,
          image: product.image,
          quantity: quantity,
        })
      }
  
      updateCartTotals()
      saveCart()
      updateCartDisplay()
    }
  
    // Remove item from cart
    function removeFromCart(productId) {
      cart.items = cart.items.filter((item) => item.id !== productId)
      updateCartTotals()
      saveCart()
      updateCartDisplay()
    }
  
    // Update item quantity
    function updateQuantity(productId, quantity) {
      const item = cart.items.find((item) => item.id === productId)
      if (item) {
        item.quantity = Math.max(1, quantity)
        updateCartTotals()
        saveCart()
      }
    }
  
    // Calculate cart totals
    function updateCartTotals() {
      cart.subtotal = cart.items.reduce((total, item) => total + item.price * item.quantity, 0)
      cart.tax = cart.subtotal * 0.07 // 7% tax
      cart.total = cart.subtotal + cart.shipping + cart.tax
    }
  
    // Save cart to localStorage
    function saveCart() {
      localStorage.setItem("cart", JSON.stringify(cart))
      // Update cart count in header
      document.querySelector(".cart-count").textContent = cart.items.reduce((count, item) => count + item.quantity, 0)
    }
  
    // Update cart display
    function updateCartDisplay() {
      if (cart.items.length === 0) {
        emptyCart.style.display = "block"
        cartContent.style.display = "none"
      } else {
        emptyCart.style.display = "none"
        cartContent.style.display = "block"
  
        // Clear existing items
        cartItems.innerHTML = ""
  
        // Add each item to the cart
        cart.items.forEach((item) => {
          const itemElement = document.createElement("div")
          itemElement.className = "cart-item"
          itemElement.innerHTML = `
                      <div class="cart-product-info">
                          <div class="cart-product-image">
                              <img src="${item.image}" alt="${item.name}">
                          </div>
                          <div class="cart-product-details">
                              <h3>${item.name}</h3>
                          </div>
                      </div>
                      <div class="price-col" data-label="Price">$${item.price.toFixed(2)}</div>
                      <div class="quantity-col" data-label="Quantity">
                          <div class="quantity-selector">
                              <button class="quantity-btn decrease" data-id="${item.id}">-</button>
                              <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-id="${item.id}">
                              <button class="quantity-btn increase" data-id="${item.id}">+</button>
                          </div>
                      </div>
                      <div class="total-col" data-label="Total">$${(item.price * item.quantity).toFixed(2)}</div>
                      <button class="remove-btn" data-id="${item.id}">
                          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                      </button>
                  `
          cartItems.appendChild(itemElement)
        })
  
        // Update totals
        cartSubtotal.textContent = `$${cart.subtotal.toFixed(2)}`
        cartShipping.textContent = `$${cart.shipping.toFixed(2)}`
        cartTax.textContent = `$${cart.tax.toFixed(2)}`
        cartTotal.textContent = `$${cart.total.toFixed(2)}`
  
        // Add event listeners to quantity buttons and remove buttons
        document.querySelectorAll(".quantity-btn.decrease").forEach((btn) => {
          btn.addEventListener("click", function () {
            const id = Number.parseInt(this.dataset.id)
            const input = this.parentElement.querySelector(".quantity-input")
            const newValue = Number.parseInt(input.value) - 1
            if (newValue >= 1) {
              input.value = newValue
              updateQuantity(id, newValue)
              updateItemTotal(this.closest(".cart-item"), id)
            }
          })
        })
  
        document.querySelectorAll(".quantity-btn.increase").forEach((btn) => {
          btn.addEventListener("click", function () {
            const id = Number.parseInt(this.dataset.id)
            const input = this.parentElement.querySelector(".quantity-input")
            const newValue = Number.parseInt(input.value) + 1
            input.value = newValue
            updateQuantity(id, newValue)
            updateItemTotal(this.closest(".cart-item"), id)
          })
        })
  
        document.querySelectorAll(".quantity-input").forEach((input) => {
          input.addEventListener("change", function () {
            const id = Number.parseInt(this.dataset.id)
            const newValue = Number.parseInt(this.value)
            if (newValue >= 1) {
              updateQuantity(id, newValue)
              updateItemTotal(this.closest(".cart-item"), id)
            } else {
              this.value = 1
              updateQuantity(id, 1)
              updateItemTotal(this.closest(".cart-item"), id)
            }
          })
        })
  
        document.querySelectorAll(".remove-btn").forEach((btn) => {
          btn.addEventListener("click", function () {
            const id = Number.parseInt(this.dataset.id)
            removeFromCart(id)
          })
        })
      }
    }
  
    // Update a single item's total
    function updateItemTotal(itemElement, productId) {
      const item = cart.items.find((item) => item.id === productId)
      if (item) {
        const totalElement = itemElement.querySelector(".total-col")
        totalElement.textContent = `$${(item.price * item.quantity).toFixed(2)}`
  
        // Update cart totals
        updateCartTotals()
        cartSubtotal.textContent = `$${cart.subtotal.toFixed(2)}`
        cartTax.textContent = `$${cart.tax.toFixed(2)}`
        cartTotal.textContent = `$${cart.total.toFixed(2)}`
        saveCart()
      }
    }
  
    // Update checkout display
    function updateCheckoutDisplay() {
      // Clear existing items
      checkoutItems.innerHTML = ""
  
      // Add each item to the checkout summary
      cart.items.forEach((item) => {
        const itemElement = document.createElement("div")
        itemElement.className = "checkout-item"
        itemElement.innerHTML = `
                  <div class="checkout-item-name">
                      ${item.name}
                      <span class="checkout-item-quantity">x${item.quantity}</span>
                  </div>
                  <div class="checkout-item-price">$${(item.price * item.quantity).toFixed(2)}</div>
              `
        checkoutItems.appendChild(itemElement)
      })
  
      // Update totals
      checkoutSubtotal.textContent = `$${cart.subtotal.toFixed(2)}`
      checkoutShipping.textContent = `$${cart.shipping.toFixed(2)}`
      checkoutTax.textContent = `$${cart.tax.toFixed(2)}`
      checkoutTotal.textContent = `$${cart.total.toFixed(2)}`
    }
  
    // Event listeners
    if (updateCartBtn) {
      updateCartBtn.addEventListener("click", () => {
        // This would typically sync with the server
        alert("Cart updated successfully!")
      })
    }
  
    if (applyCouponBtn) {
      applyCouponBtn.addEventListener("click", () => {
        const couponCode = document.getElementById("coupon-code").value.trim()
        if (couponCode) {
          // In a real app, you would validate the coupon code with the server
          alert("Coupon applied successfully!")
          document.getElementById("coupon-code").value = ""
        } else {
          alert("Please enter a coupon code")
        }
      })
    }
  
    if (checkoutBtn) {
      checkoutBtn.addEventListener("click", () => {
        // Show checkout section, hide cart section
        document.querySelector(".cart-section").style.display = "none"
        checkoutSection.style.display = "block"
        updateCheckoutDisplay()
        window.scrollTo(0, 0)
      })
    }
  
    if (backToCartBtn) {
      backToCartBtn.addEventListener("click", () => {
        // Show cart section, hide checkout section
        document.querySelector(".cart-section").style.display = "block"
        checkoutSection.style.display = "none"
        window.scrollTo(0, 0)
      })
    }
  
    if (checkoutForm) {
      checkoutForm.addEventListener("submit", (e) => {
        e.preventDefault()
  
        // In a real app, you would send the form data to the server
        // For demo purposes, we'll just show the confirmation page
  
        // Get email for confirmation
        const email = document.getElementById("email").value
        confirmationEmail.textContent = email
  
        // Generate random order number
        const orderNum = "ORD-" + Math.floor(100000 + Math.random() * 900000)
        orderNumber.textContent = orderNum
  
        // Set order date
        const date = new Date()
        orderDate.textContent = date.toLocaleDateString()
  
        // Set order total
        orderTotal.textContent = `$${cart.total.toFixed(2)}`
  
        // Show confirmation section, hide checkout section
        checkoutSection.style.display = "none"
        confirmationSection.style.display = "block"
        window.scrollTo(0, 0)
  
        // Clear cart
        cart.items = []
        updateCartTotals()
        saveCart()
      })
    }
  
    // Toggle payment method fields
    const paymentMethods = document.querySelectorAll('input[name="payment-method"]')
    const creditCardFields = document.getElementById("credit-card-fields")
  
    if (paymentMethods && creditCardFields) {
      paymentMethods.forEach((method) => {
        method.addEventListener("change", function () {
          if (this.value === "credit") {
            creditCardFields.style.display = "block"
          } else {
            creditCardFields.style.display = "none"
          }
        })
      })
    }
  
    // Initialize the cart
    initCart()
  })