document.addEventListener('DOMContentLoaded', function() {
    // Get all quantity inputs
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    // Add event listeners to each quantity input
    quantityInputs.forEach(input => {
        input.addEventListener('change', updateItemPrice);
        input.addEventListener('input', updateItemPrice);
    });
    
    // Function to update item price based on quantity
    function updateItemPrice(event) {
        const quantityInput = event.target;
        const quantity = parseInt(quantityInput.value) || 1;
        
        // Find the parent row
        const row = quantityInput.closest('tr');
        if (!row) return;
        
        // Get the base price (stored in a data attribute)
        const basePrice = parseFloat(row.querySelector('.item-price').dataset.basePrice);
        
        // Calculate the new total for this item
        const itemTotal = basePrice * quantity;
        
        // Update the displayed total for this item
        const totalElement = row.querySelector('.item-total');
        if (totalElement) {
            totalElement.textContent = '₱' + itemTotal.toFixed(2);
        }
        
        // Update the hidden input for the form submission
        const hiddenPriceInput = row.querySelector('.item-total-input');
        if (hiddenPriceInput) {
            hiddenPriceInput.value = itemTotal.toFixed(2);
        }
        
        // Update the grand total
        updateGrandTotal();
    }
    
    // Function to update the grand total
    function updateGrandTotal() {
        const totalElements = document.querySelectorAll('.item-total');
        let grandTotal = 0;
        
        totalElements.forEach(element => {
            // Remove the ₱ symbol and convert to number
            const value = parseFloat(element.textContent.replace('₱', '')) || 0;
            grandTotal += value;
        });
        
        // Update the grand total display
        const grandTotalElement = document.querySelector('.grand-total');
        if (grandTotalElement) {
            grandTotalElement.textContent = '₱' + grandTotal.toFixed(2);
        }
        
        // Update the order summary
        const orderTotalElement = document.querySelector('.order-total');
        if (orderTotalElement) {
            orderTotalElement.textContent = '₱' + grandTotal.toFixed(2);
        }
    }
});
