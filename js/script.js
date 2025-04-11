    // Home Product Slider
    // const sliderContainer = document.querySelector('.slider-container');
    // const sliderPrev = document.querySelector('.slider-prev');
    // const sliderNext = document.querySelector('.slider-next');
    // const productCards = document.querySelectorAll('.product-card');
    
    // let currentIndex = 0;
    // const cardWidth = 280; // Approximate width of a card including gap
    // const visibleCards = Math.floor(sliderContainer.offsetWidth / cardWidth);
    // const maxIndex = Math.max(0, productCards.length - visibleCards);

    // sliderPrev.addEventListener('click', function() {
    //     if (currentIndex > 0) {
    //         currentIndex--;
    //         updateSliderPosition();
    //     }
    // });

    // sliderNext.addEventListener('click', function() {
    //     if (currentIndex < maxIndex) {
    //         currentIndex++;
    //         updateSliderPosition();
    //     }
    // });

    // function updateSliderPosition() {
    //     const scrollAmount = currentIndex * cardWidth;
    //     sliderContainer.scrollTo({
    //         left: scrollAmount,
    //         behavior: 'smooth'
    //     });
        
        // Update button states
        // sliderPrev.style.opacity = currentIndex === 0 ? '0.5' : '1';
        // sliderNext.style.opacity = currentIndex === maxIndex ? '0.5' : '1';

    // Initialize slider button states
    // updateSliderPosition();

    // Handle window resize for slider
    // window.addEventListener('resize', function() {
    //     const newVisibleCards = Math.floor(sliderContainer.offsetWidth / cardWidth);
    //     const newMaxIndex = Math.max(0, productCards.length - newVisibleCards);
        
    //     // Adjust current index if needed
    //     if (currentIndex > newMaxIndex) {
    //         currentIndex = newMaxIndex;
    //         updateSliderPosition();
    //     }
    // });

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

    // Add to Cart functionality
    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', function () {
            const productID = this.getAttribute('data-productID');
    
            if (!productID) {
                alert("Invalid product ID.");
                return;
            }
    
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../functions/addToCart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
    
                    if (!response.success) {
                        alert(response.message); // Show error message
    
                        // Redirect to login page if required
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                        return;
                    }
    
                    // Update cart count dynamically
                    document.querySelector('.cart-count').textContent = response.cartCount;
    
                    // Show success animation
                    button.textContent = 'Added!';
                    button.style.backgroundColor = '#15803d';
                    setTimeout(() => {
                        button.textContent = 'Add to Cart';
                        button.style.backgroundColor = '';
                    }, 1500);
                }
            };
    
            xhr.send('product_id=' + productID);
        });
    });
    
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
    const passwordInput = document.getElementById('new-password');
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
});

// filte-group price-range
const priceSlider = document.getElementById("price-range")
const minPrice = document.getElementById("min-price")
const maxPrice = document.getElementById("max-price")
const productCards = document.querySelectorAll(".product-card")

if (priceSlider && minPrice && maxPrice) {
  // Set initial max price based on highest priced product
  let highestPrice = 0  
  productCards.forEach((card) => {
    const priceText = card.querySelector(".product-price").textContent
    const price = parseFloat(priceText.replace(/[^\d]/g, ""));
    if (price > highestPrice) {
      highestPrice = price
    }
  })

  priceSlider.max = highestPrice
  priceSlider.value = highestPrice
  maxPrice.textContent = "Rs."+highestPrice

  priceSlider.addEventListener("input", function () {
    const value = this.value
    maxPrice.textContent = "Rs."+value

    // Filter products based on price
    productCards.forEach((card) => {
      const priceText = card.querySelector(".product-price").textContent
      const price = Number.parseFloat(priceText.replace("Rs.", ""))

      if (price <= value) {
        card.style.display = "block"
      } else {
        card.style.display = "none"
      }
    })
  })
}

//  item counting
    const decreaseBtns = document.querySelectorAll('.quantity-btn.decrease');
    const increaseBtns = document.querySelectorAll('.quantity-btn.increase');

    decreaseBtns.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.quantity-selector').querySelector('.quantity-input');
            let quantity = parseInt(input.value);
            if (quantity > 1) {
                input.value = quantity - 1;
                updateCart(input); // Trigger the form submit
            }
        });
    });

    increaseBtns.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.quantity-selector').querySelector('.quantity-input');
            let quantity = parseInt(input.value);
            input.value = quantity + 1;
            updateCart(input); // Trigger the form submit
        });
    });

    // Function to submit the form with the updated quantity
    function updateCart(input) {
        const form = input.closest('form');
        form.submit(); // Submit the form to update the cart in the backend
    }
