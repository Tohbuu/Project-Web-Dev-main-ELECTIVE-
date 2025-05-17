const wrapper = document.querySelector(".sliderWrapper");
const menuItem = document.querySelectorAll(".menuItem");
const currentProductImg = document.querySelector(".productImg");
const currentProductTitle = document.querySelector(".productTitle");
const currentProductPrice = document.querySelector(".productPrice");
const formItem = document.getElementById("formItem");
const formPrice = document.getElementById("formPrice");
const formImage = document.getElementById("formImage");

// Your products array
const products = [
    {
        title: "Pepporoni Pizza",
        price: "150",
        colors: [
            {
                img: "peperonipizza.jpg"
            }
        ]
    },
    {
        title: "Cheese Pizza",
        price: "150",
        colors: [
            {
                img: "cheesepizza.jpg"
            }
        ]
    },
    {
        title: "Hawaian Pizza",
        price: "150",
        colors: [
            {
                img: "hawaiian pizza.jpg"
            }
        ]
    },
    {
        title: "Meat Pizza",
        price: "150",
        colors: [
            {
                img: "meatpizza.jpg"
            }
        ]
    },
    {
        title: "Cheezey Pizza",
        price: "150",
        colors: [
            {
                img: "cheezypizza.jpg"
            }
        ]
    }
];

let choosenProduct = products[0];

// Initialize form with first product
updateFormData(choosenProduct);

menuItem.forEach((item, index) => {
    item.addEventListener("click", () => {
        // Reset all menu items
        menuItems.forEach(menuItem => {
            menuItem.classList.remove('active');
            menuItem.setAttribute('aria-selected', 'false');
            menuItem.style.color = 'var(--text-light)';
        });
        
        // Set active menu item
        item.classList.add('active');
        item.setAttribute('aria-selected', 'true');
        item.style.color = 'var(--white)';
        
        // Change the current slide with smooth transition
        wrapper.style.transition = 'transform 0.8s cubic-bezier(0.77, 0, 0.175, 1)';
        wrapper.style.transform = `translateX(${-100 * index}vw)`;
        
        // Change the chosen product
        choosenProduct = products[index];
        
        // Update visible elements
        currentProductTitle.textContent = choosenProduct.title;
        currentProductPrice.textContent = "₱" + choosenProduct.price;
        currentProductImg.src = choosenProduct.colors[0].img;
        
        // Update hidden form fields
        updateFormData(choosenProduct);
    });
});

function updateFormData(product) {
    formItem.value = product.title;
    formPrice.value = product.price;
    formImage.value = product.colors[0].img;
}

// Function to scroll to the order form
function scrollToOrderForm() {
    document.getElementById('product').scrollIntoView({ behavior: 'smooth' });
}

// Function to update the order form with selected pizza details
function updateOrderForm(index) {
    // Get the pizza data from the global variable
    const pizza = window.pizzaData[index];
    
    if (!pizza) {
        console.error('Pizza data not found for index:', index);
        return;
    }
    
    // Update form display elements
    document.getElementById('productTitleDisplay').textContent = pizza.name;
    document.getElementById('productPriceDisplay').textContent = pizza.price;
    document.getElementById('productDescDisplay').textContent = pizza.desc || '';
    
    // Update the image with proper asset path
    const imgSrc = window.assetPath + pizza.img;
    document.getElementById('formProductImg').src = imgSrc;
    
    // Update hidden form inputs
    document.getElementById('formItem').value = pizza.name;
    document.getElementById('formPrice').value = pizza.price.replace('₱', '');
    document.getElementById('formImage').value = pizza.img;
    
    // Scroll to the form
    scrollToOrderForm();
    
    // Update menu item highlighting
    const menuItems = document.querySelectorAll('.menuItem');
    menuItems.forEach((item, i) => {
        if (i === parseInt(index)) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
    
    // Update slider position
    const sliderWrapper = document.querySelector('.sliderWrapper');
    if (sliderWrapper) {
        sliderWrapper.style.transition = 'transform 0.5s ease';
        sliderWrapper.style.transform = `translateX(${-100 * index}vw)`;
    }
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Store pizza data and asset path in global variables
    window.pizzaData = JSON.parse(document.getElementById('pizza-data')?.textContent || '[]');
    window.assetPath = document.getElementById('asset-path')?.textContent || '';
    
    // Initialize elements
    const sliderWrapper = document.querySelector('.sliderWrapper');
    const menuItems = document.querySelectorAll('.menuItem');
    const buyButtons = document.querySelectorAll('.buyButton');
    
    // Function to change the active pizza in the slider
    function changeActivePizza(index) {
        // Update slider position with smooth transition
        if (sliderWrapper) {
            sliderWrapper.style.transition = 'transform 0.5s ease';
            sliderWrapper.style.transform = `translateX(${-100 * index}vw)`;
        }
        
        // Update menu item styling
        menuItems.forEach((item, i) => {
            if (i === index) {
                item.setAttribute('aria-selected', 'true');
                item.classList.add('active');
            } else {
                item.setAttribute('aria-selected', 'false');
                item.classList.remove('active');
            }
        });
    }
    
    // Add click event listeners to all "BUY NOW" buttons
    buyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const index = parseInt(this.getAttribute('data-index'));
            changeActivePizza(index);
            updateOrderForm(index);
        });
    });
    
    // Add click event listeners to menu items
    menuItems.forEach((item, index) => {
        item.addEventListener('click', function() {
            changeActivePizza(index);
            updateOrderForm(index);
        });
    });
    
    // Set the first menu item as active by default
    if (menuItems.length > 0) {
        changeActivePizza(0);
    }
    
    // Form validation
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            const phoneNumber = document.getElementById('phoneNumber').value;
            if (!/^\d{11}$/.test(phoneNumber)) {
                e.preventDefault();
                alert('Please enter a valid 11-digit phone number');
            }
        });
    }
    
    // Make the updateOrderForm function globally available
    window.updateOrderForm = function(index) {
        changeActivePizza(index);
        updateOrderForm(index);
    };
    
    // Add click event listeners to buy buttons
    buyButtons.forEach((button, index) => {
        button.addEventListener("click", () => {
            // Scroll to product section
            document.getElementById('product').scrollIntoView({ behavior: 'smooth' });
            
            // Set the active menu item
            menuItems.forEach((menuItem, i) => {
                if (i === index) {
                    menuItem.click(); // Trigger the click event on the menu item
                }
            });
        });
    });
    
    // Set first menu item as active by default
    if (menuItems.length > 0) {
        menuItems[0].classList.add('active');
        menuItems[0].setAttribute('aria-selected', 'true');
        menuItems[0].style.color = 'var(--white)';
    }
});
