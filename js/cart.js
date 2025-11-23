// js/cart.js

// Чекаємо, поки вся HTML-сторінка завантажиться
document.addEventListener('DOMContentLoaded', () => {
    loadCart();

    // Знаходимо кнопку "Оформити замовлення" і вішаємо обробник події
    const checkoutButton = document.getElementById('checkout-button');
    checkoutButton.addEventListener('click', handleCheckout);
});

// Функція завантаження кошика
async function loadCart() {
    const itemsContainer = document.getElementById('cart-items-container');
    const emptyMsg = document.getElementById('cart-empty-msg');
    const totalElement = document.getElementById('cart-total');
    const checkoutButton = document.getElementById('checkout-button');


    try {
        const response = await fetch('./api/get_cart.php');

        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = 'login.php';
                return;
            }
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const cartData = await response.json();

        // Спершу очищаємо тільки товари, але не emptyMsg
        const existingItems = itemsContainer.querySelectorAll('.cart-item');
        existingItems.forEach(item => item.remove());

        if (!cartData.items || cartData.items.length === 0) {
            // Кошик порожній
            emptyMsg.style.display = 'block';
            checkoutButton.disabled = true;
        } else {
            // Кошик містить товари
            emptyMsg.style.display = 'none';
            checkoutButton.disabled = false;

            cartData.items.forEach(item => {
                const itemTotalPrice = (item.price * item.quantity).toFixed(2);
                const cartItemHTML = `
                <div class="cart-item">
                    <img src="${item.image_url || './img/placeholder.webp'}" alt="${item.name}">
                    <div class="cart-item-info">
                        <h4>${item.name}</h4>
                        <p>Price: ${item.price}€</p>
                    </div>
                    <div class="cart-item-quantity">x${item.quantity}</div>
                    <div class="cart-item-price">${itemTotalPrice}€</div>
                </div>
            `;
                itemsContainer.insertAdjacentHTML('beforeend', cartItemHTML);
            });
        }

        totalElement.textContent = `Total: ${cartData.totalPrice.toFixed(2)}€`;

    } catch (error) {
        console.error("Failed to load cart:", error);
        itemsContainer.innerHTML = '<p class="error">Failed to load your cart. Please try again later.</p>';
        emptyMsg.style.display = 'none';
        checkoutButton.disabled = true;
    }

}


// Функція оформлення замовлення
async function handleCheckout() {
    const checkoutButton = document.getElementById('checkout-button');
    checkoutButton.disabled = true;
    checkoutButton.textContent = 'Processing...';

    try {
        // 1. Робимо запит до нашого API (Крок 8.3)
        const response = await fetch('./api/checkout.php', {
            method: 'POST'
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        // 2. Якщо все успішно
        if (result.success) {
            alert(`Order placed successfully! Your Order ID is: ${result.order_id}`);
            // Оновлюємо кошик (він тепер має бути порожній)
            loadCart();
        }

    } catch (error) {
        console.error("Failed to checkout:", error);
        alert('Failed to place order. Please try again.');
    } finally {
        // Повертаємо кнопку в нормальний стан
        checkoutButton.disabled = false;
        checkoutButton.textContent = 'Proceed to Checkout';
    }
}
