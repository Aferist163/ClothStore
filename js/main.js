// Глобальна змінна для всіх товарів
let productsData = [];

// Чекаємо завантаження DOM
document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();

    // Додаємо обробники для фільтрів і сортування
    document.querySelectorAll('.filter').forEach(f => f.addEventListener('change', applyFilterSort));
    const sortSelect = document.getElementById('sort-select');
    if (sortSelect) sortSelect.addEventListener('change', applyFilterSort);
});

// 1️⃣ Завантаження товарів з API
async function fetchProducts() {
    try {
        const response = await fetch('./api/products.php');
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        productsData = await response.json(); // Зберігаємо всі товари
        renderProducts(productsData); // Показуємо їх спочатку
    } catch (error) {
        console.error("Failed to fetch products:", error);
        const container = document.querySelector('.products');
        if (container) container.innerHTML = '<p class="error">Failed to load products. Please try again later.</p>';
    }
}

// 2️⃣ Функція рендерингу товарів
function renderProducts(products) {
    const productsContainer = document.querySelector('.products-list');
    productsContainer.innerHTML = '';

    // --- Генерація продуктів ---
    products.forEach(product => {
        const productCard = `
        <div class="product-card glass">
            <h3>${product.name} (${product.category_name})</h3>
            <img src="${product.image_url || './img/placeholder.webp'}" alt="${product.name}">
            <p>${product.description}</p>
            <p><strong>Price:</strong> ${product.price}€</p>
            <div class="product-actions">
                <div class="quantity-selector">
                    <label for="quantity-${product.id}">Qty:</label>
                    <input type="number" id="quantity-${product.id}" class="quantity-input" value="1" min="1">
                </div>
                <button class="add-to-cart-btn" data-product-id="${product.id}">Add to Cart</button>
            </div>
        </div>
    `;
        const cardElement = document.createElement('div');
        cardElement.innerHTML = productCard;
        productsContainer.appendChild(cardElement.firstElementChild);
    });

    // ⬇️ звертаємося до елементів тільки ТУТ, після того як вони зʼявилися
    const gBtn = document.getElementById("glass-toggle");
    const gIcon = document.getElementById("glass-icon");
    const gmain = document.querySelector(".sidebar");

    let ggmain = document.querySelectorAll(".product-card");

    let interfaceEnabledGlass = true;

    gBtn.addEventListener("click", () => {
    interfaceEnabledGlass = !interfaceEnabledGlass;

    // --- Sidebar ---
    if (interfaceEnabledGlass) {
        gmain.classList.add("glass");
        gmain.classList.remove("static");
    } else {
        gmain.classList.remove("glass");
        gmain.classList.add("static");
    }

    // --- Product cards ---
    ggmain.forEach(card => {
        if (interfaceEnabledGlass) {
            card.classList.add("glass");
            card.classList.remove("static");
        } else {
            card.classList.remove("glass");
            card.classList.add("static");
        }
    });

    gIcon.src = interfaceEnabledGlass
        ? "./img/noBlur.png"
        : "./img/blur.png";
});



    // Додаємо обробники на кнопки "Add to Cart"
    document.querySelectorAll('.add-to-cart-btn').forEach(button => button.addEventListener('click', handleAddToCart));
}

// 3️⃣ Функція додавання товару в кошик
async function handleAddToCart(event) {
    const button = event.target;
    const productId = button.dataset.productId;
    const quantityInput = document.getElementById(`quantity-${productId}`);
    const quantity = parseInt(quantityInput.value, 10);

    if (quantity <= 0) {
        alert('Please enter a valid quantity.');
        return;
    }

    try {
        const response = await fetch('./api/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        });

        const result = await response.json();

        if (response.status === 401) {
            alert('You must be logged in to add items to your cart. Redirecting to login page...');
            window.location.href = 'login.php';
            return;
        }

        if (!response.ok) throw new Error(result.error || 'Failed to add item to cart');

        alert(`Item added to cart! (ID: ${productId}, Qty: ${quantity})`);
    } catch (error) {
        console.error('Add to cart failed:', error);
        alert(error.message);
    }
}

// 4️⃣ Фільтрація та сортування
function applyFilterSort() {
    const selectedCategories = Array.from(document.querySelectorAll('.filter'))
        .filter(f => f.checked)
        .map(f => f.value.toLowerCase()); // приводимо до нижнього регістру

    let filteredProducts = productsData;

    // Фільтр по категорії (також приводимо category_name до нижнього регістру)
    if (selectedCategories.length > 0) {
        filteredProducts = filteredProducts.filter(p => selectedCategories.includes(p.category_name.toLowerCase()));
    }

    // Сортування
    const sortValue = document.getElementById('sort-select')?.value || 'default';
    if (sortValue === 'price-asc') filteredProducts.sort((a, b) => a.price - b.price);
    else if (sortValue === 'price-desc') filteredProducts.sort((a, b) => b.price - a.price);
    else if (sortValue === 'name-asc') filteredProducts.sort((a, b) => a.name.localeCompare(b.name));
    else if (sortValue === 'name-desc') filteredProducts.sort((a, b) => b.name.localeCompare(a.name));

    renderProducts(filteredProducts);
}

let currentSlide = 0;

function updateSlider() {
    const slides = document.querySelector('.slides');
    slides.style.transform = `translateX(-${currentSlide * 100}%)`;
}

document.addEventListener("DOMContentLoaded", () => {
    const slidesCount = document.querySelectorAll('.slides img').length;

    document.querySelector('.next').addEventListener('click', () => {
        currentSlide = (currentSlide + 1) % slidesCount;
        updateSlider();
    });

    document.querySelector('.prev').addEventListener('click', () => {
        currentSlide = (currentSlide - 1 + slidesCount) % slidesCount;
        updateSlider();
    });
});





document.addEventListener("DOMContentLoaded", () => {

    // --- Кнопка відео ---
    const btn = document.getElementById("bg-toggle");
    const icon = document.getElementById("bg-icon");
    const video = document.getElementById("bg-video");
    let isVideoPlaying = true;

    btn.addEventListener("click", () => {
        isVideoPlaying = !isVideoPlaying;

        if (isVideoPlaying) {
            video.play();
            icon.src = "./img/noVideo.svg";
        } else {
            video.pause();
            icon.src = "./img/video.svg";
        }
    });



    // --- Кнопка інтерфейсу ---
    const uiBtn = document.getElementById("ui-toggle");
    const uiIcon = document.getElementById("ui-icon");
    const main = document.querySelector(".main-container");

    let interfaceEnabled = true;

    uiBtn.addEventListener("click", () => {
        interfaceEnabled = !interfaceEnabled;

        if (interfaceEnabled) {
            main.classList.remove("no-interface");
            uiIcon.src = "./img/noInterface.svg";     // інтерфейс включений
        } else {
            main.classList.add("no-interface");
            uiIcon.src = "./img/interface.svg";   // інтерфейс вимкнений
        }
    });
});
