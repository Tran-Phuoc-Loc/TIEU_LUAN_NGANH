// Hàm mở modal và hiển thị ảnh được chọn
function openModal(imageUrl) {
    // Tạo một phần tử div để làm modal nếu chưa có
    let modal = document.getElementById("imageModal");
    if (!modal) {
        modal = document.createElement("div");
        modal.id = "imageModal";
        modal.style.position = "fixed";
        modal.style.top = "0";
        modal.style.left = "0";
        modal.style.width = "100%";
        modal.style.height = "100%";
        modal.style.backgroundColor = "rgba(0,0,0,0.8)";
        modal.style.display = "flex";
        modal.style.justifyContent = "center";
        modal.style.alignItems = "center";
        modal.style.zIndex = "1000";

        // Thêm phần tử ảnh vào modal
        const img = document.createElement("img");
        img.id = "modalImage";
        img.style.maxWidth = "90%";
        img.style.maxHeight = "90%";
        img.style.borderRadius = "10px";
        img.style.boxShadow = "0 0 10px rgba(255,255,255,0.5)";
        modal.appendChild(img);

        // Thêm sự kiện đóng modal khi nhấn vào bên ngoài ảnh
        modal.addEventListener("click", function () {
            modal.style.display = "none";
        });

        // Thêm modal vào body
        document.body.appendChild(modal);
    }

    // Hiển thị modal và cập nhật ảnh
    const modalImage = document.getElementById("modalImage");
    modalImage.src = imageUrl;
    modal.style.display = "flex";
}

// Gán sự kiện cho các ảnh
document.querySelectorAll('.thumbnail').forEach(img => {
    img.addEventListener('click', function() {
        openModal(this.src);
    });
});
