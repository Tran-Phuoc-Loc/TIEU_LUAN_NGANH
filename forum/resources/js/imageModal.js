// Hàm mở modal và hiển thị ảnh được chọn
function openModal(imageUrl, images, currentIndex) {
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

        // Thêm nút mũi tên trái
        const prevButton = document.createElement("button");
        prevButton.innerHTML = "&#10094;";
        prevButton.id = "prevButton";
        prevButton.style.position = "absolute";
        prevButton.style.left = "20px";
        prevButton.style.fontSize = "30px";
        prevButton.style.color = "white";
        prevButton.style.backgroundColor = "transparent";
        prevButton.style.border = "none";
        prevButton.style.cursor = "pointer";
        prevButton.style.zIndex = "1001";
        modal.appendChild(prevButton);

        // Thêm nút mũi tên phải
        const nextButton = document.createElement("button");
        nextButton.innerHTML = "&#10095;";
        nextButton.id = "nextButton";
        nextButton.style.position = "absolute";
        nextButton.style.right = "20px";
        nextButton.style.fontSize = "30px";
        nextButton.style.color = "white";
        nextButton.style.backgroundColor = "transparent";
        nextButton.style.border = "none";
        nextButton.style.cursor = "pointer";
        nextButton.style.zIndex = "1001";
        modal.appendChild(nextButton);

        // Thêm sự kiện đóng modal khi nhấn vào bên ngoài ảnh
        modal.addEventListener("click", function (event) {
            // Chỉ đóng modal nếu nhấn vào vùng ngoài ảnh
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });

        // Thêm modal vào body
        document.body.appendChild(modal);
    }

    // Hiển thị modal và cập nhật ảnh
    const modalImage = document.getElementById("modalImage");
    modalImage.src = imageUrl;
    modal.style.display = "flex";

    // Lấy các nút điều hướng và cập nhật sự kiện cho chúng
    const prevButton = document.getElementById("prevButton");
    const nextButton = document.getElementById("nextButton");

    // Xử lý sự kiện nút "Trước"
    prevButton.onclick = function() {
        currentIndex = (currentIndex === 0) ? images.length - 1 : currentIndex - 1;
        modalImage.src = images[currentIndex].src;
    };

    // Xử lý sự kiện nút "Sau"
    nextButton.onclick = function() {
        currentIndex = (currentIndex === images.length - 1) ? 0 : currentIndex + 1;
        modalImage.src = images[currentIndex].src;
    };
}

// Gán sự kiện cho các ảnh thu nhỏ
document.querySelectorAll('.thumbnail').forEach((img, index, allImages) => {
    img.addEventListener('click', function() {
        openModal(this.src, allImages, index);
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const videos = document.querySelectorAll('.video-player');

    let currentlyPlayingVideo = null;

    // Lắng nghe sự kiện "play" trên tất cả video
    videos.forEach(video => {
        video.addEventListener('play', function () {
            // Nếu có video khác đang phát, dừng nó
            if (currentlyPlayingVideo && currentlyPlayingVideo !== this) {
                currentlyPlayingVideo.pause();
                currentlyPlayingVideo.currentTime = 0; // Đặt lại về đầu
            }
            currentlyPlayingVideo = this;
        });

        // Khi video bị dừng hoặc kết thúc, xóa tham chiếu
        video.addEventListener('pause', function () {
            if (currentlyPlayingVideo === this) {
                currentlyPlayingVideo = null;
            }
        });

        video.addEventListener('ended', function () {
            currentlyPlayingVideo = null;
        });
    });

    // Dừng tất cả video khi người dùng bấm nút tạo bài viết hoặc rời trang
    const createPostButton = document.querySelector('#createPostButton');
    if (createPostButton) {
        createPostButton.addEventListener('click', () => {
            videos.forEach(video => {
                video.pause();
                video.currentTime = 0; // Đặt lại về đầu
            });
        });
    }

    // Dừng tất cả video khi rời khỏi trang
    window.addEventListener('beforeunload', () => {
        videos.forEach(video => {
            video.pause();
            video.currentTime = 0;
        });
    });
    const mediaSingleInput = document.getElementById('media_single');
    const mediaMultipleInput = document.getElementById('media_multiple');

    // Kiểm tra nếu phần tử mediaSingleInput tồn tại
    if (mediaSingleInput) {
        mediaSingleInput.addEventListener('change', toggleMediaInputs);
    }

    function toggleMediaInputs() {
        if (mediaSingleInput.files.length > 0) {
            const file = mediaSingleInput.files[0];
            const isVideo = file.type.includes('video');

            if (isVideo) {
                // Nếu người dùng chọn video, vô hiệu hóa input nhiều ảnh
                mediaMultipleInput.value = ''; // Reset input
                mediaMultipleInput.disabled = true;
            } else {
                // Nếu là ảnh, cho phép chọn thêm ảnh phụ
                mediaMultipleInput.disabled = false;
            }
        } else {
            // Nếu không có file nào, bật lại input nhiều ảnh
            mediaMultipleInput.disabled = false;
        }
    }

    // Hàm mở modal
window.openModal = function(element) {
    const postId = element.getAttribute('data-post-id');
    const imageUrl = element.getAttribute('data-image-url');

    // Kiểm tra nếu modal đã tồn tại, nếu không thì tạo mới
    let modal = document.getElementById(`modal-${postId}`);
    if (!modal) {
        // Tạo modal mới
        modal = document.createElement('div');
        modal.id = `modal-${postId}`;
        modal.className = 'image-modal';
        modal.style.display = 'none';
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0,0,0,0.8)';
        modal.style.zIndex = '1000';
        modal.style.display = 'flex';
        modal.style.justifyContent = 'center';
        modal.style.alignItems = 'center';

        // Thêm nút đóng modal
        const closeBtn = document.createElement('span');
        closeBtn.className = 'close-btn';
        closeBtn.innerHTML = '&times;';
        closeBtn.setAttribute('data-post-id', postId);
        closeBtn.onclick = function () { window.closeModal(this); };
        closeBtn.style.position = 'absolute';
        closeBtn.style.top = '10px';
        closeBtn.style.right = '10px';
        closeBtn.style.fontSize = '30px';
        closeBtn.style.color = 'white';
        closeBtn.style.cursor = 'pointer';
        modal.appendChild(closeBtn);

        // Thêm hình ảnh vào modal
        const modalImage = document.createElement('img');
        modalImage.id = `modalImage-${postId}`;
        modalImage.style.maxWidth = '90%';
        modalImage.style.maxHeight = '90%';
        modalImage.style.borderRadius = '10px';
        modalImage.style.boxShadow = '0 0 10px rgba(255,255,255,0.5)';
        modalImage.className = 'modal-img';
        modal.appendChild(modalImage);

        // Thêm nút mũi tên trái
        const prevButton = document.createElement("button");
        prevButton.innerHTML = "&#10094;";
        prevButton.className = "prev-btn";
        prevButton.setAttribute('data-post-id', postId);
        prevButton.onclick = function () { window.changeImage(this, -1); };
        prevButton.style.position = "absolute";
        prevButton.style.left = "20px";
        prevButton.style.fontSize = "30px";
        prevButton.style.color = "white";
        prevButton.style.backgroundColor = "transparent";
        prevButton.style.border = "none";
        prevButton.style.cursor = "pointer";
        prevButton.style.zIndex = "1001";
        modal.appendChild(prevButton);

        // Thêm nút mũi tên phải
        const nextButton = document.createElement("button");
        nextButton.innerHTML = "&#10095;";
        nextButton.className = "next-btn";
        nextButton.setAttribute('data-post-id', postId);
        nextButton.onclick = function () { window.changeImage(this, 1); };
        nextButton.style.position = "absolute";
        nextButton.style.right = "20px";
        nextButton.style.fontSize = "30px";
        nextButton.style.color = "white";
        nextButton.style.backgroundColor = "transparent";
        nextButton.style.border = "none";
        nextButton.style.cursor = "pointer";
        nextButton.style.zIndex = "1001";
        modal.appendChild(nextButton);

        // Thêm modal vào body
        document.body.appendChild(modal);
    }

    // Hiển thị modal và cập nhật ảnh
    const modalImage = document.getElementById(`modalImage-${postId}`);
    modalImage.src = imageUrl;

    // Hiển thị modal
    modal.style.display = 'flex';

    // Lưu chỉ mục hiện tại của ảnh
    const thumbnails = document.querySelectorAll(`.post-thumbnail[data-post-id="${postId}"]`);
    modalImage.setAttribute('data-current-index', Array.from(thumbnails).findIndex(img => img.getAttribute('data-image-url') === imageUrl));
};

// Hàm đóng modal
window.closeModal = function(element) {
    const postId = element.getAttribute('data-post-id');
    const modal = document.getElementById(`modal-${postId}`);
    if (modal) {
        modal.style.display = 'none';
    }
};

// Hàm chuyển đổi ảnh
window.changeImage = function(element, direction) {
    const postId = element.getAttribute('data-post-id');
    const modalImage = document.getElementById(`modalImage-${postId}`);
    const thumbnails = document.querySelectorAll(`.post-thumbnail[data-post-id="${postId}"]`);
    
    let currentIndex = parseInt(modalImage.getAttribute('data-current-index'));
    let newIndex = currentIndex + direction;

    // Vòng lặp để chuyển ảnh
    if (newIndex < 0) newIndex = thumbnails.length - 1;
    if (newIndex >= thumbnails.length) newIndex = 0;

    modalImage.src = thumbnails[newIndex].getAttribute('data-image-url');
    modalImage.setAttribute('data-current-index', newIndex);
};

});
