// js/admin.js

const API_URL = './api/admin.php';
const productForm = document.getElementById('product-form');
const formTitle = document.getElementById('form-title');
const productIdInput = document.getElementById('product-id');
const cancelEditBtn = document.getElementById('cancel-edit-btn');

document.addEventListener('DOMContentLoaded', () => {
    adminGuard(); 
    loadProducts(); 
   // loadCategories(); 
    setupTempCategories();
    productForm.addEventListener('submit', handleSubmitProduct);
    cancelEditBtn.addEventListener('click', resetForm);
});

/*1. ОХОРОНЕЦЬ АДМІНКИ*/
async function adminGuard() {
    try {
        const response = await fetch('./api/auth_check.php');

        if (!response.ok) {
            window.location.href = 'login.php';
            return;
        }

        const authData = await response.json();

        if (!authData.isLoggedIn || authData.user.role !== 'admin') {
            alert('Access denied. Admin rights required.');
            window.location.href = 'index.php';
        }
    } catch (error) {
        console.error('Auth check failed:', error);
        window.location.href = 'login.php';
    }
}

/*2. ЗАВАНТАЖЕННЯ ТОВАРІВ (READ) */
async function loadProducts() {
    try {
        const response = await fetch(API_URL, { method: 'GET' });
        if (!response.ok) throw new Error('Failed to fetch products');

        const products = await response.json();
        const tbody = document.getElementById('products-tbody');
        tbody.innerHTML = ''; 

        products.forEach(product => {
            const tr = document.createElement('tr');
            tr.setAttribute('data-id', product.id);
            tr.setAttribute('data-category-id', product.category_id); 
            tr.setAttribute('data-description', product.description); 
            tr.setAttribute('data-image-url', product.image_url || ''); 

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

        tbody.querySelectorAll('.edit-btn').forEach(btn => btn.addEventListener('click', handleEditClick));
        tbody.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', handleDeleteClick));

    } catch (error) {
        console.error(error.message);
    }
}

/*3. ОБРОБКА ФОРМИ (CREATE / UPDATE)*/
async function handleSubmitProduct(event) {
    event.preventDefault();

    const productId = productIdInput.value; 
    const isUpdating = !!productId;

    const cloudinaryUrl = await uploadImageIfNeeded();

    const imageInput = document.getElementById("image_url");
    if (cloudinaryUrl) {
        imageInput.value = cloudinaryUrl;
    } else if (!isUpdating) {
        imageInput.value = '';
    }

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


/*4. ОБРОБКА ВИДАЛЕННЯ (DELETE)*/
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
        loadProducts(); 

    } catch (error) {
        alert(error.message);
    }
}

/* 5. ОБРОБКА РЕДАГУВАННЯ (Populate Form)*/
function handleEditClick(event) {
    const row = event.target.closest('tr');
    const productId = row.dataset.id;

    const name = row.cells[1].textContent;
    const price = parseFloat(row.cells[2].textContent);
    const categoryId = row.dataset.categoryId || '';
    const description = row.dataset.description || '';

    const imgUrl = row.dataset.imageUrl || '';

    formTitle.textContent = 'Edit Product';
    productIdInput.value = productId;
    productForm.querySelector('#name').value = name;
    productForm.querySelector('#price').value = price;
    productForm.querySelector('#description').value = description;
    document.getElementById('category_id').value = categoryId;
    document.getElementById('image_url').value = imgUrl; 

    cancelEditBtn.style.display = 'block';
    window.scrollTo(0, 0);
}

function resetForm() {
    formTitle.textContent = 'Add New Product';
    productForm.reset();
    productIdInput.value = '';
    cancelEditBtn.style.display = 'none';
}

function setupTempCategories() {
    const categorySelect = document.getElementById('category_id');
    categorySelect.innerHTML = ''; 

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
            return result.url;
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