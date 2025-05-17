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
        title: "Overload Cheese Pizza", // Changed from "Cheezey Pizza" to "Overload Cheese Pizza"
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
        menuItem.forEach(menuItem => {
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
  
    // If the image path doesn't already include 'images/'
    const imgPath = product.colors[0].img;
    formImage.value = imgPath.includes('images/') ? imgPath : 'images/' + imgPath;
}

// Function to scroll to the order form
function scrollToOrderForm() {
    document.getElementById('product').scrollIntoView({ behavior: 'smooth' });
}

// Function to update the order form with selected pizza details
function updateOrderForm(index) {
    // Get the pizza data from the hidden JSON element
    const pizzaDataElement = document.getElementById('pizza-data');
    const assetPathElement = document.getElementById('asset-path');
    
    if (!pizzaDataElement || !assetPathElement) {
        console.error('Required data elements not found');
        return;
    }
    
    // Parse the JSON data
    const pizzaData = JSON.parse(pizzaDataElement.textContent);
    const assetPath = assetPathElement.textContent;
    
    // Get the selected pizza
    const pizza = pizzaData[index];
    
    if (!pizza) {
        console.error('Pizza data not found for index:', index);
        return;
    }
    
    // Update form display elements
    document.getElementById('productTitleDisplay').textContent = pizza.name;
    document.getElementById('productPriceDisplay').textContent = pizza.price;
    document.getElementById('productDescDisplay').textContent = pizza.desc || '';
    
    // Update the image with proper asset path
    const imgSrc = assetPath + 'images/' + pizza.img;
    document.getElementById('formProductImg').src = imgSrc;
    
    // Update hidden form inputs
    document.getElementById('formItem').value = pizza.name;
    document.getElementById('formPrice').value = pizza.price.replace('₱', '');
    document.getElementById('formImage').value = pizza.img;
    
    // Highlight the active menu item
    const menuItems = document.querySelectorAll('.menuItem');
    menuItems.forEach((item, i) => {
        if (i === parseInt(index)) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
    
    // Update slider position if needed
    const sliderWrapper = document.querySelector('.sliderWrapper');
    if (sliderWrapper) {
        sliderWrapper.style.transition = 'transform 0.5s ease';
        sliderWrapper.style.transform = `translateX(${-100 * index}vw)`;
    }
    
    // Scroll to the order form
    document.getElementById('product').scrollIntoView({ behavior: 'smooth' });
}

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get all menu items
    const menuItems = document.querySelectorAll('.menuItem');
    // Get the slider wrapper
    const wrapper = document.querySelector('.sliderWrapper');
    // Get pizza data
    const pizzaData = JSON.parse(document.getElementById('pizza-data').textContent);
    // Asset path
    const assetPath = document.getElementById('asset-path').textContent;
    
    // Form elements to update
    const formProductImg = document.getElementById('formProductImg');
    const productTitleDisplay = document.getElementById('productTitleDisplay');
    const productPriceDisplay = document.getElementById('productPriceDisplay');
    const productDescDisplay = document.getElementById('productDescDisplay');
    const formItem = document.getElementById('formItem');
    const formPrice = document.getElementById('formPrice');
    const formImage = document.getElementById('formImage');
    
    // Add click event to each menu item - ONLY change the slider, not scroll to buy section
    menuItems.forEach((item, index) => {
        item.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent any default action
            
            // Remove active class from all menu items
            menuItems.forEach(menuItem => {
                menuItem.classList.remove('active');
                menuItem.setAttribute('aria-selected', 'false');
            });
            
            // Add active class to clicked menu item
            this.classList.add('active');
            this.setAttribute('aria-selected', 'true');
            
            // Slide to the corresponding pizza
            wrapper.style.transition = 'transform 0.8s cubic-bezier(0.77, 0, 0.175, 1)';
            wrapper.style.transform = `translateX(${-100 * index}vw)`;
            
            // Update the product form with the selected pizza data
            const selectedPizza = pizzaData[index];
            
            // Update form display elements
            productTitleDisplay.textContent = selectedPizza.name;
            productPriceDisplay.textContent = selectedPizza.price;
            productDescDisplay.textContent = selectedPizza.desc;
            
            // Update hidden form fields
            formItem.value = selectedPizza.name;
            formPrice.value = selectedPizza.price.replace('₱', '');
            formImage.value = selectedPizza.img;
            
            // Update product image
            formProductImg.src = `${assetPath}images/${selectedPizza.img}`;
            
            // DO NOT scroll to the product form - only update the slider
        });
    });
    
    // Handle "BUY NOW" button clicks - THESE should scroll to the buy section
    const buyButtons = document.querySelectorAll('.buyButton');
    buyButtons.forEach((button, index) => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active menu item
            menuItems.forEach(menuItem => {
                menuItem.classList.remove('active');
                menuItem.setAttribute('aria-selected', 'false');
            });
            menuItems[index].classList.add('active');
            menuItems[index].setAttribute('aria-selected', 'true');
            
            // Update the product form with the selected pizza data
            const selectedPizza = pizzaData[index];
            
            // Update form display elements
            productTitleDisplay.textContent = selectedPizza.name;
            productPriceDisplay.textContent = selectedPizza.price;
            productDescDisplay.textContent = selectedPizza.desc;
            
            // Update hidden form fields
            formItem.value = selectedPizza.name;
            formPrice.value = selectedPizza.price.replace('₱', '');
            formImage.value = selectedPizza.img;
            
            // Update product image
            formProductImg.src = `${assetPath}images/${selectedPizza.img}`;
            
            // Scroll to the product form - ONLY the buy button does this
            document.getElementById('product').scrollIntoView({ 
                behavior: 'smooth' 
            });
        });
    });
    
    // Initialize with the first pizza
    updateOrderForm(0);
});
