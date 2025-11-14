// Выбираем элемент extra_image
const extraImageInput = document.getElementById('extra_image');

extraImageInput.addEventListener('change', async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    try {
        const fd = new FormData();
        fd.append('image_file', file);

        const response = await fetch('/api/upload.php', {
            method: 'POST',
            body: fd
        });

        if (!response.ok) throw new Error('HTTP error! status: ' + response.status);

        const result = await response.json();


        if (result.error) {
            console.error('Upload error:', result.error);
            alert('Failed to upload extra image');
        } else {
            console.log('Extra image uploaded to Cloudinary successfully!');
            // URL нам не нужен, просто для логов
        }

    } catch (error) {
        console.error('Upload failed:', error);
        alert('Upload failed');
    }
});
