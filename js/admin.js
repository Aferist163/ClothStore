const API_URL = '/api/admin.php';
const productForm = document.getElementById('product-form');
const formTitle = document.getElementById('form-title');
const productIdInput = document.getElementById('product-id');
const oldImageInput = document.getElementById('old_image_url');
const cancelEditBtn = document.getElementById('cancel-edit-btn');

document.addEventListener('DOMContentLoaded', () => {
    adminGuard();
    loadProducts();
    setupTempCategories();

    productForm.addEventListener('submit', handleSubmitProduct);
    cancelEditBtn.addEventListener('click', resetForm);
});

// --- Проверка админа
async function adminGuard() {
    try {
        const response = await fetch('/api/auth_check.php');
        if (!response.ok) return window.location.href = 'login.php';

        const authData = await response.json();
        if (!authData.isLoggedIn || authData.user.role !== 'admin') {
            alert('Access denied. Admin rights required.');
            window.location.href = 'index.php';
        }
    } catch (err) {
        console.error(err);
        window.location.href = 'login.php';
    }
}

// --- Загрузка всех товаров
async function loadProducts() {
    try {
        const res = await fetch(API_URL);
        const products = await res.json();
        const tbody = document.getElementById('products-tbody');
        tbody.innerHTML = '';

        products.forEach(p => {
            const tr = document.createElement('tr');
            tr.dataset.id = p.id;
            tr.innerHTML = `
                <td><img src="${p.image_url || 'img/placeholder.webp'}" alt="${p.name}" style="width:50px;"></td>
                <td>${p.name}</td>
                <td>${p.price}€</td>
                <td>${p.category_name}</td>
                <td class="action-buttons">
                    <button class="edit-btn">Edit</button>
                    <button class="delete-btn">Delete</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        tbody.querySelectorAll('.edit-btn').forEach(btn => btn.addEventListener('click', handleEditClick));
        tbody.querySelectorAll('.delete-btn').forEach(btn => btn.addEventListener('click', handleDeleteClick));
    } catch (err) { console.error(err); }
}

// --- Submit формы (Create / Update)
async function handleSubmitProduct(e) {
    e.preventDefault();
    const productId = productIdInput.value;
    const isUpdating = !!productId;
    const url = isUpdating ? `${API_URL}?id=${productId}` : API_URL;
    const method = isUpdating ? 'PUT' : 'POST';

    let imageUrl = "";

    // --- Загружаем фото, если выбрано
    const fileInput = document.querySelector("#image_file");
    if (fileInput && fileInput.files.length > 0) {
        let fd = new FormData();
        fd.append("image_file", fileInput.files[0]);
        const uploadRes = await fetch("/api/upload.php", { method: "POST", body: fd });
        const uploadJson = await uploadRes.json();
        if (uploadJson.url) imageUrl = uploadJson.url;
    }

    const formData = new FormData(productForm);
    const data = Object.fromEntries(formData.entries());
    delete data.image_file; // убираем файл

    if (!imageUrl) imageUrl = data.old_image_url || "";
    data.image_url = imageUrl;

    try {
        const res = await fetch(url, { method, headers: { "Content-Type": "application/json" }, body: JSON.stringify(data) });
        const result = await res.json();
        if (!res.ok) throw new Error(result.error || "Failed to save product");
        alert(`Product ${isUpdating ? 'updated' : 'created'} successfully!`);
        resetForm();
        loadProducts();
    } catch (err) { alert(err.message); }
}

// --- Редактирование
async function handleEditClick(e) {
    const id = e.target.closest('tr').dataset.id;
    const res = await fetch(`${API_URL}?id=${id}`);
    const product = await res.json();

    formTitle.textContent = 'Edit Product';
    productIdInput.value = product.id;
    oldImageInput.value = product.image_url;

    productForm.querySelector('#name').value = product.name;
    productForm.querySelector('#description').value = product.description;
    productForm.querySelector('#price').value = product.price;
    productForm.querySelector('#category_id').value = product.category_id;

    cancelEditBtn.style.display = 'block';
    window.scrollTo(0, 0);
}

// --- Удаление
async function handleDeleteClick(e) {
    const id = e.target.closest('tr').dataset.id;
    if (!confirm(`Delete product ${id}?`)) return;

    try {
        const res = await fetch(`${API_URL}?id=${id}`, { method: "DELETE" });
        const result = await res.json();
        if (!res.ok) throw new Error(result.error || "Failed to delete product");
        alert("Product deleted");
        loadProducts();
    } catch (err) { alert(err.message); }
}

// --- Сброс формы
function resetForm() {
    formTitle.textContent = 'Add New Product';
    productForm.reset();
    productIdInput.value = '';
    oldImageInput.value = '';
    cancelEditBtn.style.display = 'none';
}

// --- Временные категории
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
    categories.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = c.name;
        categorySelect.appendChild(opt);
    });
}
