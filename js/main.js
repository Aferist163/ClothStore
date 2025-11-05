// js/main.js

document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
});

// Асинхронна функція для отримання товарів
async function fetchProducts() {
    try {
        const response = await fetch('http://localhost/ClothStore/api/products.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const products = await response.json();
        const productsContainer = document.querySelector('.products');
        productsContainer.innerHTML = '';

        products.forEach(product => {
            // ОНОВЛЕНО: Ми видалили <p:last-child> і замінили його на .product-actions
            const productCard = `
                <div class="product-card">
                    <h3>${product.name} (${product.category_name})</h3>
                    <img src="${product.image_url}" alt="${product.name}">
                    <p>${product.description}</p>
                    <p><strong>Price:</strong>${product.price}€</p>
                    
                    <div class="product-actions">
                        <div class="quantity-selector">
                            <label for="quantity-${product.id}">Qty:</label>
                            <input type="number" id="quantity-${product.id}" class="quantity-input" value="1" min="1">
                        </div>
                        <button class="add-to-cart-btn" data-product-id="${product.id}">
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;
            
            // Ми більше не можемо використовувати innerHTML +=
            // тому що нам потрібно прикріпити обробники подій
            const cardElement = document.createElement('div');
            cardElement.innerHTML = productCard;
            productsContainer.appendChild(cardElement.firstElementChild); // Додаємо саму картку
        });
        
        // 2. ДОДАНО: "Вішаємо" обробники на всі нові кнопки
        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', handleAddToCart);
        });

    } catch (error) {
        console.error("Failed to fetch products:", error);
        const productsContainer = document.querySelector('.products');
        productsContainer.innerHTML = '<p class="error">Failed to load products. Please try again later.</p>';
    }
}

/**
 * 3. ДОДАНО: Функція для обробки натискання кнопки "Add to Cart"
 */
async function handleAddToCart(event) {
    const button = event.target;
    const productId = button.dataset.productId;
    
    // Знаходимо поле кількості, яке відповідає цій кнопці
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const quantity = parseInt(quantityInput.value, 10);

    if (quantity <= 0) {
        alert('Please enter a valid quantity.');
        return;
    }

    try {
        // Робимо запит до нашого API (Крок 8.1)
        const response = await fetch('http://localhost/ClothStore/api/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        });

        const result = await response.json();

        if (response.status === 401) {
            // Не залогінений
            alert('You must be logged in to add items to your cart. Redirecting to login page...');
            window.location.href = 'login.php';
            return;
        }

        if (!response.ok) {
            throw new Error(result.error || 'Failed to add item to cart');
        }

        // Успіх!
        alert(`Item added to cart! (ID: ${productId}, Qty: ${quantity})`);
        // Можна також додати анімацію або оновити лічильник кошика в хедері

    } catch (error) {
        console.error('Add to cart failed:', error);
        alert(error.message);
    }
}