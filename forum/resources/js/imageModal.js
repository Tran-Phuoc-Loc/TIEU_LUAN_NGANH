// Hàm mở modal và hiển thị ảnh được chọn
function openModal(imageUrl, images, currentIndex) {
    // Tạo một phần tử div để làm modal nếu chưa có
    let modal = $("#imageModal");
    if (modal.length === 0) {
        modal = $("<div>").attr("id", "imageModal").css({
            position: "fixed",
            top: "0",
            left: "0",
            width: "100%",
            height: "100%",
            backgroundColor: "rgba(0,0,0,0.8)",
            display: "flex",
            justifyContent: "center",
            alignItems: "center",
            zIndex: "1000"
        });

        // Thêm phần tử ảnh vào modal
        const img = $("<img>").attr("id", "modalImage").css({
            maxWidth: "90%",
            maxHeight: "90%",
            borderRadius: "10px",
            boxShadow: "0 0 10px rgba(255,255,255,0.5)"
        });
        modal.append(img);

        // Thêm nút mũi tên trái
        const prevButton = $("<button>").html("&#10094;").attr("id", "prevButton").css({
            position: "absolute",
            left: "20px",
            fontSize: "30px",
            color: "white",
            backgroundColor: "transparent",
            border: "none",
            cursor: "pointer",
            zIndex: "1001"
        });
        modal.append(prevButton);

        // Thêm nút mũi tên phải
        const nextButton = $("<button>").html("&#10095;").attr("id", "nextButton").css({
            position: "absolute",
            right: "20px",
            fontSize: "30px",
            color: "white",
            backgroundColor: "transparent",
            border: "none",
            cursor: "pointer",
            zIndex: "1001"
        });
        modal.append(nextButton);

        // Thêm sự kiện đóng modal khi nhấn vào bên ngoài ảnh
        modal.on("click", function (event) {
            if (event.target === modal[0]) {
                modal.css("display", "none");
            }
        });

        // Thêm modal vào body
        $("body").append(modal);
    }

    // Hiển thị modal và cập nhật ảnh
    $("#modalImage").attr("src", imageUrl);
    modal.css("display", "flex");

    // Lấy các nút điều hướng và cập nhật sự kiện cho chúng
    const prevButton = $("#prevButton");
    const nextButton = $("#nextButton");

    // Xử lý sự kiện nút "Trước"
    prevButton.off("click").on("click", function() {
        currentIndex = (currentIndex === 0) ? images.length - 1 : currentIndex - 1;
        $("#modalImage").attr("src", images[currentIndex].src);
    });

    // Xử lý sự kiện nút "Sau"
    nextButton.off("click").on("click", function() {
        currentIndex = (currentIndex === images.length - 1) ? 0 : currentIndex + 1;
        $("#modalImage").attr("src", images[currentIndex].src);
    });
}

$(document).ready(function() {
    // Gán sự kiện cho các ảnh thu nhỏ
    $('.thumbnail').each(function(index, img) {
        $(img).on('click', function() {
            openModal(this.src, $('.thumbnail'), index); // Gọi hàm openModal khi ảnh thu nhỏ được nhấp
        });
    });

    let currentlyPlayingVideo = null;

    // Lắng nghe sự kiện "play" trên tất cả video
    $('.video-player').each(function() {
        $(this).on('play', function() {
            // Nếu có video khác đang phát, dừng nó
            if (currentlyPlayingVideo && currentlyPlayingVideo !== this) {
                currentlyPlayingVideo.pause();
                currentlyPlayingVideo.currentTime = 0; // Đặt lại về đầu
            }
            currentlyPlayingVideo = this; // Lưu lại video đang phát
        });

        // Khi video bị dừng hoặc kết thúc, xóa tham chiếu
        $(this).on('pause', function() {
            if (currentlyPlayingVideo === this) {
                currentlyPlayingVideo = null; // Xóa tham chiếu khi video bị dừng
            }
        });

        $(this).on('ended', function() {
            currentlyPlayingVideo = null; // Xóa tham chiếu khi video kết thúc
        });
    });

    // Dừng tất cả video khi người dùng bấm nút tạo bài viết hoặc rời trang
    $('#createPostButton').on('click', function() {
        $('.video-player').each(function() {
            this.pause(); // Dừng tất cả video
            this.currentTime = 0; // Đặt lại video về đầu
        });
    });

    // Dừng tất cả video khi rời khỏi trang
    $(window).on('beforeunload', function() {
        $('.video-player').each(function() {
            this.pause(); // Dừng tất cả video
            this.currentTime = 0; // Đặt lại video về đầu
        });
    });

    const mediaSingleInput = $('#media_single');
    const mediaMultipleInput = $('#media_multiple');

    // Kiểm tra nếu phần tử mediaSingleInput tồn tại
    if (mediaSingleInput.length > 0) {
        mediaSingleInput.on('change', toggleMediaInputs); // Gọi toggleMediaInputs khi thay đổi file
    }

    // Hàm xử lý thay đổi input
    function toggleMediaInputs() {
        if (mediaSingleInput[0].files.length > 0) {
            const file = mediaSingleInput[0].files[0];
            const isVideo = file.type.includes('video'); // Kiểm tra nếu file là video

            if (isVideo) {
                // Nếu người dùng chọn video, vô hiệu hóa input nhiều ảnh
                mediaMultipleInput.val(''); // Reset input
                mediaMultipleInput.prop('disabled', true); // Vô hiệu hóa input ảnh
            } else {
                // Nếu là ảnh, cho phép chọn thêm ảnh phụ
                mediaMultipleInput.prop('disabled', false); // Bật input ảnh
            }
        } else {
            // Nếu không có file nào, bật lại input nhiều ảnh
            mediaMultipleInput.prop('disabled', false); // Bật input ảnh
        }
    }

    // Hàm mở modal
    window.openModal = function(element) {
    const postId = $(element).data('post-id'); // Lấy ID bài viết từ thuộc tính data
    const imageUrl = $(element).data('image-url'); // Lấy URL ảnh từ thuộc tính data

    // Kiểm tra xem modal đã tồn tại hay chưa, nếu chưa thì tạo mới
    let modal = $(`#modal-${postId}`);
    if (modal.length === 0) {
        // Tạo modal mới
        modal = $('<div>')
            .attr('id', `modal-${postId}`)
            .addClass('image-modal')
            .css({
                display: 'none',
                position: 'fixed',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0,0,0,0.8)',
                zIndex: '1000',
                justifyContent: 'center',
                alignItems: 'center',
                display: 'flex',
            });

        // Tạo nút đóng modal
        const closeBtn = $('<span>')
            .addClass('close-btn')
            .html('&times;')
            .data('post-id', postId)
            .on('click', function () { window.closeModal(this); })
            .css({
                position: 'absolute',
                top: '10px',
                right: '10px',
                fontSize: '30px',
                color: 'white',
                cursor: 'pointer',
            });

        modal.append(closeBtn);

        // Thêm ảnh vào modal
        const modalImage = $('<img>')
            .attr('id', `modalImage-${postId}`)
            .addClass('modal-img')
            .css({
                maxWidth: '90%',
                maxHeight: '90%',
                borderRadius: '10px',
                boxShadow: '0 0 10px rgba(255,255,255,0.5)',
            });

        modal.append(modalImage);

        // Thêm nút mũi tên trái
        const prevButton = $('<button>')
            .html('&#10094;')
            .addClass('prev-btn')
            .data('post-id', postId)
            .on('click', function () { window.changeImage(this, -1); })
            .css({
                position: 'absolute',
                left: '20px',
                fontSize: '30px',
                color: 'white',
                backgroundColor: 'transparent',
                border: 'none',
                cursor: 'pointer',
                zIndex: '1001',
            });

        modal.append(prevButton);

        // Thêm nút mũi tên phải
        const nextButton = $('<button>')
            .html('&#10095;')
            .addClass('next-btn')
            .data('post-id', postId)
            .on('click', function () { window.changeImage(this, 1); })
            .css({
                position: 'absolute',
                right: '20px',
                fontSize: '30px',
                color: 'white',
                backgroundColor: 'transparent',
                border: 'none',
                cursor: 'pointer',
                zIndex: '1001',
            });

        modal.append(nextButton);

        // Thêm modal vào body
        $('body').append(modal);
    }

    // Cập nhật ảnh trong modal và hiển thị modal
    const modalImage = $(`#modalImage-${postId}`);
    modalImage.attr('src', imageUrl);
    modal.show(); // Hiển thị modal

    // Lưu chỉ mục hiện tại của ảnh
    const thumbnails = $(`.post-thumbnail[data-post-id="${postId}"]`);
    modalImage.data('current-index', thumbnails.index(thumbnails.filter(`[data-image-url="${imageUrl}"]`)));
};

    // Hàm đóng modal
    window.closeModal = function(element) {
    const postId = $(element).data('post-id'); // Lấy ID bài viết
    const modal = $(`#modal-${postId}`);
    if (modal.length) {
        modal.hide(); // Ẩn modal
    }
};

    // Hàm chuyển đổi ảnh
    window.changeImage = function(element, direction) {
    const postId = $(element).data('post-id'); // Lấy ID bài viết
    const modalImage = $(`#modalImage-${postId}`);
    const thumbnails = $(`.post-thumbnail[data-post-id="${postId}"]`);

    let currentIndex = parseInt(modalImage.data('current-index')); // Lấy chỉ mục ảnh hiện tại
    let newIndex = currentIndex + direction; // Tính chỉ mục ảnh mới

    // Điều chỉnh chỉ mục nếu vượt qua phạm vi ảnh
    if (newIndex < 0) newIndex = thumbnails.length - 1; // Nếu đi ngược lại, quay về ảnh cuối
    if (newIndex >= thumbnails.length) newIndex = 0; // Nếu đi qua ảnh cuối, quay về ảnh đầu

    // Cập nhật ảnh trong modal
    modalImage.attr('src', $(thumbnails[newIndex]).data('image-url'));
    modalImage.data('current-index', newIndex); // Cập nhật lại chỉ mục ảnh hiện tại
};

});
