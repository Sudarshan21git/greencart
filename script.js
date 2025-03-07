document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    mobileMenuToggle.addEventListener('click', function() {
        mobileMenu.classList.toggle('active');
    });

    // Product Slider
    const sliderContainer = document.querySelector('.slider-container');
    const sliderPrev = document.querySelector('.slider-prev');
    const sliderNext = document.querySelector('.slider-next');
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

    // Testimonial Slider
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    const dots = document.querySelectorAll('.dot');
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

    // Newsletter Form
    const newsletterForm = document.getElementById('newsletter-form');
    const formMessage = document.querySelector('.form-message');

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

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('.btn-add-cart');
    const cartCount = document.querySelector('.cart-count');
    let cartItems = 0;

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            cartItems++;
            cartCount.textContent = cartItems;
            
            // Add animation effect
            cartCount.classList.add('pulse');
            setTimeout(() => {
                cartCount.classList.remove('pulse');
            }, 300);
            
            // Get product info for cart (in a real app, you'd store this)
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('h3').textContent;
            const productPrice = productCard.querySelector('.product-price').textContent;
            
            // Show confirmation message
            const originalText = this.textContent;
            this.textContent = 'Added!';
            this.style.backgroundColor = '#15803d';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.backgroundColor = '';
            }, 1500);
            
            console.log('Added to cart:', productName, productPrice);
        });
    });

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

    // Set current year in footer
    document.getElementById('current-year').textContent = new Date().getFullYear();

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
});

// Add this to your existing script.js file

document.addEventListener('DOMContentLoaded', function() {
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

    // Form validation
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simple validation
            if (!email || !password) {
                alert('Please fill in all fields');
                return;
            }
                        
            // Simulate successful login
            alert('Login successful!');
            window.location.href = 'index.html';
        });
    }
    
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const firstName = document.getElementById('first-name').value;
            const lastName = document.getElementById('last-name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            // const terms = document.getElementById('terms').checked;
            
            // Simple validation
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                alert('Please fill in all fields');
                return;
            }
            
            if (password !== confirmPassword) {
                alert('Passwords do not match');
                return;
            }
            
            // Simulate successful signup
            alert('Account created successfully!');
            window.location.href = 'login.html';
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


