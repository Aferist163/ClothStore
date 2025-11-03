// js/main.js

// Чекаємо, поки вся HTML-сторінка завантажиться
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
});

// Асинхронна функція для отримання товарів
async function fetchProducts() {
    try {
        // 1. Робимо запит до нашого API, яке ми створили на Кроці 7
        const response = await fetch('http://localhost/ClothStore/api/products.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // 2. Отримуємо дані у форматі JSON
        const products = await response.json();

        // 3. Знаходимо наш порожній контейнер
        const productsContainer = document.querySelector('.products');

        // 4. Очищуємо контейнер (про всяк випадок, якщо там щось було)
        productsContainer.innerHTML = '';

        // 5. Перебираємо кожен товар і створюємо для нього HTML-картку
        products.forEach(product => {
            // Ми використовуємо ту саму HTML-структуру, що й у вашого товариша,
            // щоб CSS (index.css) ідеально "ліг" на неї.
            const productCard = `
                <div class="product-card">
                    <h3>${product.name} (${product.category_name})</h3>
                    <img src="${product.image_url}" alt="${product.name}">
                    <p>${product.description}</p>
                    <p><strong>Price:</strong>${product.price}€</p>
                </div>
            `;
            
            // 6. Додаємо створену картку в контейнер
            productsContainer.innerHTML += productCard;
        });

    } catch (error) {
        console.error("Failed to fetch products:", error);
        const productsContainer = document.querySelector('.products');
        productsContainer.innerHTML = '<p class="error">Failed to load products. Please try again later.</p>';
    }
}