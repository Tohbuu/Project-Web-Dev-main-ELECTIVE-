// Store pizza data in JavaScript (will be populated from the blade template)
let pizzaData = [];

// Function to scroll to the order form
function scrollToOrderForm() {
    document.getElementById('product').scrollIntoView({ behavior: 'smooth' });
}

// Function to update the order form with selected pizza details
function updateOrderForm(index) {
    const pizza = window.pizzaData[index];
    
    if (!pizza) {
        console.error('Pizza data not found for index:', index);
        return;
    }
    
    // Update form display elements
    document.getElementById('productTitleDisplay').textContent = pizza.name;
    document.getElementById('productPriceDisplay').textContent = pizza.price;
    document.getElementById('productDescDisplay').textContent = pizza.desc || '';
    
    // Handle image path more robustly
    let imgSrc;
    if (pizza.img.includes('/')) {
        // If the path already includes a directory
        imgSrc = pizza.img;
    } else {
        // Otherwise, prepend the images directory
        imgSrc = (window.assetPath || '/images/') + pizza.img;
    }
    
    // Update the image
    const imgElement = document.getElementById('formProductImg');
    if (imgElement) {
        imgElement.src = imgSrc;
        
        // Add error handler to use placeholder if image fails to load
        imgElement.onerror = function() {
            this.src = '/images/pizza-placeholder.png';
        };
    }
    
    // Update hidden form inputs
    document.getElementById('formItem').value = pizza.name;
    document.getElementById('formPrice').value = pizza.price.replace('₱', '');
    document.getElementById('formImage').value = pizza.img;
    
    // Scroll to the form
    scrollToOrderForm();
}

// Add page transition effects
function setupPageTransitions() {
    // Handle all links with page transitions
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Skip if href is undefined or null
            if (!this.getAttribute('href')) return;
            
            // Skip if it's an anchor link, has target="_blank", or has data-no-transition attribute
            if (this.getAttribute('href').startsWith('#') || 
                this.getAttribute('target') === '_blank' ||
                this.hasAttribute('data-no-transition')) {
                return;
            }
            
            e.preventDefault();
            const targetUrl = this.href;
            
            // Start fade out animation
            document.body.classList.add('fade-out');
            
            // Navigate after animation completes
            setTimeout(() => {
                window.location.href = targetUrl;
            }, 300); // Reduced from 500ms for a snappier feel
        });
    });
    
    // Handle form submissions with transitions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (this.hasAttribute('data-no-transition')) return; // Skip if marked
            
            // Validate form before transition
            if (!validateForm(this)) {
                e.preventDefault();
                return;
            }
            
            e.preventDefault();
            const formElement = this;
            
            // Start fade out animation
            document.body.classList.add('fade-out');
            
            // Submit form after animation completes
            setTimeout(() => {
                formElement.submit();
            }, 300); // Reduced from 500ms for a snappier feel
        });
    });
    
    // Add fade-in class when page loads
    window.addEventListener('load', () => {
        document.body.classList.remove('fade-out');
        document.body.classList.add('fade-in');
    });
    
    // Handle browser back/forward navigation
    window.addEventListener('pageshow', (event) => {
        // If navigated to via back/forward buttons
        if (event.persisted) {
            document.body.classList.remove('fade-out');
            document.body.classList.add('fade-in');
        }
    });
}

// Basic form validation
function validateForm(form) {
    // Example: Check if phone number field exists and validate it
    const phoneField = form.querySelector('input[type="tel"]');
    if (phoneField && phoneField.required) {
        const phonePattern = /^\d{11}$/;
        if (!phonePattern.test(phoneField.value)) {
            alert('Please enter a valid 11-digit phone number');
            return false;
        }
    }
    return true;
}

// Initialize slider functionality
function initSlider() {
    const sliderWrapper = document.querySelector('.sliderWrapper');
    
    // Check if slider exists on the page
    if (!sliderWrapper) {
        console.log('Slider not found on this page');
        return;
    }
    
    let currentSlide = 0;
    const totalSlides = document.querySelectorAll('.sliderItem').length;
    
    // Function to go to a specific slide
    function goToSlide(index) {
        // Ensure index is within bounds
        if (index < 0 || index >= totalSlides) {
            console.error('Invalid slide index:', index);
            return;
        }
        
        currentSlide = index;
        
        // Add smooth transition
        sliderWrapper.style.transition = 'transform 0.5s ease';
        sliderWrapper.style.transform = `translateX(${-100 * index}vw)`;
        
        // Update active menu item with smooth transition
        document.querySelectorAll('.menuItem').forEach((item, i) => {
            item.style.transition = 'color 0.3s ease, font-weight 0.3s ease';
            
            if (i === index) {
                item.style.color = 'var(--white)'; // Changed to white for light color
                item.style.fontWeight = '600';
                item.classList.add('active');
                item.setAttribute('aria-selected', 'true');
            } else {
                item.style.color = 'var(--text-light)'; // Using CSS variable for consistency
                item.style.fontWeight = '400';
                item.classList.remove('active');
                item.setAttribute('aria-selected', 'false');
            }
        });
    }
    
    // Add click event listeners to menu items
    document.querySelectorAll('.menuItem').forEach((item, index) => {
        // Set data-index attribute if not already set
        if (!item.hasAttribute('data-index')) {
            item.setAttribute('data-index', index);
        }
        
        item.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-index'));
            goToSlide(slideIndex);
            updateOrderForm(slideIndex);
        });
    });
    
    // Add click event listeners to all "BUY NOW" buttons
    document.querySelectorAll('.buyButton').forEach((button, index) => {
        // Set data-index attribute if not already set
        if (!button.hasAttribute('data-index')) {
            button.setAttribute('data-index', index);
        }
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const slideIndex = parseInt(this.getAttribute('data-index'));
            goToSlide(slideIndex);
            updateOrderForm(slideIndex);
        });
    });
    
    // Add swipe functionality for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    sliderWrapper.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    sliderWrapper.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeThreshold = 50; // Minimum distance for a swipe
        
        if (touchStartX - touchEndX > swipeThreshold) {
            // Swipe left - go to next slide
            if (currentSlide < totalSlides - 1) {
                goToSlide(currentSlide + 1);
                updateOrderForm(currentSlide);
            }
        }
        
        if (touchEndX - touchStartX > swipeThreshold) {
            // Swipe right - go to previous slide
            if (currentSlide > 0) {
                goToSlide(currentSlide - 1);
                updateOrderForm(currentSlide);
            }
        }
    }
    
    // Set the first slide as active initially
    goToSlide(0);
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize page transitions
    setupPageTransitions();
    
    // Initialize slider if we're on a page with a slider
    if (document.querySelector('.sliderWrapper')) {
        initSlider();
    }
});

// Export function to set pizza data from the blade template
window.setPizzaData = function(data, assetPath) {
    window.pizzaData = data;
    window.assetPath = assetPath;
    
    // Initialize the first pizza
    if (data && data.length > 0) {
        updateOrderForm(0);
    }
};