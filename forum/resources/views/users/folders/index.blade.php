@extends('layouts.users')

@section('content')
<div class="row">
    <div class="post-container">
        <h1>Bài Viết Đã Lưu</h1>
        <div id="folders-list">
            <!-- Các thư mục sẽ được tải vào đây -->
        </div>
        <div id="posts-list">
            <!-- Bài viết sẽ được tải qua AJAX -->
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        // Gọi Ajax để lấy danh sách bài viết đã lưu
        $.ajax({
            url: '/api/saved-posts',
            method: 'GET',
            success: function(response) {
                console.log('Response:', response); // Ghi log toàn bộ phản hồi để kiểm tra
                if (Object.keys(response).length === 0) {
                    $('#folders-list').html('<p>Bạn chưa có thư mục nào.</p>');
                    $('#posts-list').html(''); // Xóa nội dung danh sách bài viết
                } else {
                    let foldersHtml = ''; // Biến chứa HTML cho danh sách thư mục

                    // Lặp qua từng thư mục trong phản hồi
                    for (const folderName in response) {
                        const folder = response[folderName]; // Lấy đối tượng thư mục
                        foldersHtml += `<button class="folder-button" data-folder="${folderName}">${folder.folder}</button>`; // Tạo nút cho thư mục
                    }

                    $('#folders-list').html(foldersHtml); // Cập nhật danh sách thư mục

                    // Sự kiện click cho nút thư mục
                    $('.folder-button').on('click', function() {
                        const selectedFolder = $(this).data('folder'); // Lấy tên thư mục đã chọn
                        const folderData = response[selectedFolder]; // Lấy dữ liệu thư mục

                        let postsHtml = ''; // Biến chứa HTML cho danh sách bài viết

                        // Kiểm tra xem thư mục có bài viết nào không
                        if (folderData.posts.length === 0) {
                            postsHtml += '<p>Không có bài viết nào trong thư mục này.</p>'; // Thông báo nếu thư mục trống
                        } else {
                            // Lặp qua từng bài viết trong thư mục
                            folderData.posts.forEach(function(post) {
                                postsHtml += `
                        <div class="post-card">
                            <div class="post-meta d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-center">
                                    <a href="/users/${post.author}">
                                        <img src="${post.author_avatar}" alt="Avatar" class="post-avatar" loading="lazy">
                                    </a>
                                    <span class="post-author">Đăng bởi: <strong>${post.author}</strong></span> |
                                    <span class="post-time">${post.published_at}</span>
                                </div>
                            </div>
                            <div class="post-category mt-1">
                                <span>Danh mục: <strong>${post.category}</strong></span>
                            </div>
                            <div class="post-content">
                                <div class="post-title">${post.title}</div>
                                <div class="post-description">
                                    <span class="content-preview">${post.content.substring(0, 100)}...</span>
                                    <span class="content-full" style="display: none;">${post.content}</span>
                                </div>
                                <button class="btn btn-link toggle-content">Xem thêm</button>
                                ${post.image_url ? `<div class="post-image"><img src="/storage/${post.image_url}" alt="${post.title}" loading="lazy"></div>` : ''}
                                <div class="post-footer">
                                    <div class="post-actions">
                                        <button class="like-button" data-post-id="${post.id}"><i class="far fa-thumbs-up fa-lg"></i> <span class="like-count">${post.like_count}</span></button>
                                        <span class="comment-toggle" style="cursor:pointer;" data-post-id="${post.id}">
                                            <i class="fas fa-comment-dots"></i> Xem Bình Luận (${post.comments_count})
                                        </span>
                                        <button class="btn btn-link unsave-post" data-post-id="${post.id}"><i class="fas fa-bookmark"></i> Bỏ lưu</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                            });
                        }

                        $('#posts-list').html(postsHtml); // Cập nhật danh sách bài viết
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error details:', xhr.responseText); // Ghi log chi tiết lỗi
                alert('Có lỗi xảy ra khi tải danh sách bài viết: ' + error); // Thông báo lỗi cho người dùng
            }
        });
    });
</script>
@endsection