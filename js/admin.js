// js/admin.js

// Глобальні змінні для форми
const API_URL = './api/admin.php';
const productForm = document.getElementById('product-form');
const formTitle = document.getElementById('form-title');
const productIdInput = document.getElementById('product-id');
const cancelEditBtn = document.getElementById('cancel-edit-btn');

document.addEventListener('DOMContentLoaded', () => {
    adminGuard(); // 1. Перевіряємо, чи адмін
    loadProducts(); // 2. Завантажуємо товари
    // loadCategories(); // 3. Завантажуємо категорії для форми (поки не реалізовано API)

    // Встановимо тимчасові категорії, доки у нас немає API для них
    setupTempCategories();

    // 4. Вішаємо обробник на форму
    productForm.addEventListener('submit', handleSubmitProduct);

    // 5. Обробник для кнопки "Скасувати редагування"
    cancelEditBtn.addEventListener('click', resetForm);
});

/**
 * 1. ОХОРОНЕЦЬ АДМІНКИ
 * Перевіряє, чи залогінений користувач є адміном
 */
async function adminGuard() {
    try {
        const response = await fetch('./api/auth_check.php');

        if (!response.ok) {
            // Не залогінений (401) або інша помилка
            window.location.href = 'login.php';
            return;
        }

        const authData = await response.json();

        if (!authData.isLoggedIn || authData.user.role !== 'admin') {
            // Залогінений, але не адмін (403 Forbidden з точки зору логіки)
            alert('Access denied. Admin rights required.');
            window.location.href = 'index.php';
        }
        // Якщо все добре, скрипт продовжує роботу
    } catch (error) {
        console.error('Auth check failed:', error);
        window.location.href = 'login.php';
    }
}

/**
 * 2. ЗАВАНТАЖЕННЯ ТОВАРІВ (READ)
 * Отримує товари з API і рендерить їх у таблицю
 */
async function loadProducts() {
    try {
        const response = await fetch(API_URL, { method: 'GET' });
        if (!response.ok) throw new Error('Failed to fetch products');

        const products = await response.json();
        const tbody = document.getElementById('products-tbody');
        tbody.innerHTML = ''; // Очищуємо таблицю

        products.forEach(product => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-id', product.id);
            tr.setAttribute('data-description', product.description); // <-- додали
            tr.setAttribute('data-image-url', product.image_url || ''); // <-- додали

            tr.innerHTML = `
        <td><img src="${product.image_url || 'img/placeholder.webp'}" alt="${product.name}"></td>
        <td>${product.name}</td>
        <td>${product.price}€</td>
        <td>${product.category_name}</td>
        <td class="action-buttons">
            <button class="edit-btn">Edit</button>
            <button class="delete-btn">Delete</button>
        </td>
    `;
            tbody.appendChild(tr);
        });

        // Додаємо обробники для нових кнопок
        tbody.querySelectorAll('.edit-btn').forEach(btn => btn.addEventListener('click', handleEditClick));
        tbody.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', handleDeleteClick));

    } catch (error) {
        console.error(error.message);
    }
}

/**
 * 3. ОБРОБКА ФОРМИ (CREATE / UPDATE)
 */
async function handleSubmitProduct(event) {
    event.preventDefault();

    const productId = productIdInput.value; // ID продукту (якщо редагування)
    const isUpdating = !!productId;

    // 1. Завантажуємо нову картинку, якщо вибрана
    const cloudinaryUrl = await uploadImageIfNeeded();

    // 2. Якщо нова картинка є — підставляємо її
    // Якщо ні — залишаємо старий URL (для редагування)
    const imageInput = document.getElementById("image_url");
    if (cloudinaryUrl) {
        imageInput.value = cloudinaryUrl;
    } else if (!isUpdating) {
        // Для нового товару без картинки ставимо порожнє значення
        imageInput.value = '';
    }
    // Для редагування без нової картинки старий URL залишиться в input.value

    // 3. Збираємо всі дані форми
    const formData = new FormData(productForm);
    const data = Object.fromEntries(formData.entries());

    const url = isUpdating ? `${API_URL}?id=${productId}` : API_URL;
    const method = isUpdating ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Failed to save product');
        }

        alert(`Product ${isUpdating ? 'updated' : 'created'} successfully!`);
        resetForm();
        loadProducts();

    } catch (error) {
        alert(error.message);
    }
}


/**
 * 4. ОБРОБКА ВИДАЛЕННЯ (DELETE)
 */
async function handleDeleteClick(event) {
    const row = event.target.closest('tr');
    const productId = row.dataset.id;

    if (!confirm(`Are you sure you want to delete product ID ${productId}?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_URL}?id=${productId}`, {
            method: 'DELETE'
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || 'Failed to delete product');
        }

        alert('Product deleted successfully!');
        loadProducts(); // Оновлюємо список

    } catch (error) {
        alert(error.message);
    }
}

/**
 * 5. ОБРОБКА РЕДАГУВАННЯ (Populate Form)
 * Заповнює форму даними товару, на який клікнули "Edit"
 */
function handleEditClick(event) {
    const row = event.target.closest('tr');
    const productId = row.dataset.id;

    const name = row.cells[1].textContent;
    const price = parseFloat(row.cells[2].textContent);
    const categoryId = row.dataset.categoryId || '';
    const description = row.dataset.description || '';

    // Беремо картинку з таблиці
    const imgUrl = row.dataset.imageUrl || '';

    formTitle.textContent = 'Edit Product';
    productIdInput.value = productId;
    productForm.querySelector('#name').value = name;
    productForm.querySelector('#price').value = price;
    productForm.querySelector('#description').value = description;
    document.getElementById('category_id').value = categoryId;
    document.getElementById('image_url').value = imgUrl; // стара картинка

    cancelEditBtn.style.display = 'block';
    window.scrollTo(0, 0);
}



/**
 * Скидає форму в початковий стан (для "Add New Product")
 */
function resetForm() {
    formTitle.textContent = 'Add New Product';
    productForm.reset();
    productIdInput.value = '';
    cancelEditBtn.style.display = 'none';
}

/**
 * Тимчасова функція для заповнення категорій
 * (Бо у нас ще немає API, щоб їх отримати)
 */
function setupTempCategories() {
    const categorySelect = document.getElementById('category_id');
    categorySelect.innerHTML = ''; // Очищуємо "Loading..."

    // Дані з вашого .sql файлу
    const categories = [
        { id: 1, name: 't-shirt' },
        { id: 2, name: 'eyewear' },
        { id: 3, name: 'jeans' },
        { id: 4, name: 'hoodie' },
        { id: 5, name: 'zip hoodie' },
        { id: 6, name: 'sweatshirt' },
        { id: 7, name: 'sweatpants' },
        { id: 8, name: 'puffer jacket' },
        { id: 9, name: 'hat' },
        { id: 10, name: 'shoes' }
        // Додайте більше, якщо вони у вас є
    ];

    categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id;
        option.textContent = cat.name;
        categorySelect.appendChild(option);
    });
}









async function uploadImageIfNeeded() {
    const fileInput = document.getElementById("image_file");

    // Якщо файл не вибраний → повертаємо null
    if (!fileInput || fileInput.files.length === 0) {
        return null;
    }

    const formData = new FormData();
    formData.append("image_file", fileInput.files[0]);

    try {
        const response = await fetch("./api/upload.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            return result.url; // Cloudinary URL
        } else {
            alert("Image upload failed: " + (result.error || "Unknown error"));
            return null;
        }

    } catch (err) {
        console.error("Upload error:", err);
        alert("Failed to upload image.");
        return null;
    }
}
