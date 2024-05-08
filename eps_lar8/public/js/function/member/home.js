// efek Geser Banner Besar
let currentSlide = 0;
const slides = document.querySelectorAll(".banner-item");
const totalSlides = slides.length;

function showSlide(index) {
    slides.forEach((slide) => {
        slide.style.display = "none";
    });
    slides[index].style.display = "block";
}
// Fungsi untuk pindah ke slide berikutnya
function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}
// Tampilkan slide pertama saat halaman dimuat
showSlide(currentSlide);
// Ganti slide secara otomatis setiap 3 detik
setInterval(nextSlide, 3000);

// product
document.addEventListener("DOMContentLoaded", function () {
    const productGrid = document.getElementById("productGrid");
    const loadMoreButton = document.getElementById("loadMoreButton");

    if (productGrid) {
        const initialDisplayCount = 14;

        // Fungsi untuk memperlihatkan produk
        function showProducts(count) {
            const productItems = productGrid.querySelectorAll(".product-item");
            for (let i = 0; i < count && i < productItems.length; i++) {
                productItems[i].style.display = "block";
            }
            displayedCount = count;
            if (displayedCount >= productItems.length) {
                loadMoreButton.style.display = "none";
            }
        }

        let displayedCount = initialDisplayCount;
        showProducts(displayedCount);

        loadMoreButton.addEventListener("click", function () {
            displayedCount += initialDisplayCount;
            showProducts(displayedCount);
        });
    } else {
        console.error("Element #productGrid not found");
    }
});

// qty Product
function increaseQuantity() {
    const quantityInput = document.getElementById("quantity");
    let newQuantity = parseInt(quantityInput.value) + 1;
    // Pastikan nilai tidak melebihi batas maksimum
    if (newQuantity <= parseInt(quantityInput.max)) {
        quantityInput.value = newQuantity;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById("quantity");
    let newQuantity = parseInt(quantityInput.value) - 1;
    // Pastikan nilai tidak kurang dari batas minimum
    if (newQuantity >= parseInt(quantityInput.min)) {
        quantityInput.value = newQuantity;
    }
}
