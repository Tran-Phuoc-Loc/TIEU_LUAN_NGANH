@extends('layouts.users')

@section('content')
<div class="welcome-contents">
    <h1>Bài Viết Đã Lưu</h1>
    <div id="posts-list">
        <!-- Bài viết sẽ được tải qua AJAX -->
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        // Gọi Ajax để lấy danh sách bài viết đã lưu
        $.ajax({
            url: '/api/saved-posts', // URL API trả về danh sách bài viết đã lưu
            method: 'GET',
            success: function(response) {
                if (response.length === 0) {
                    $('#posts-list').html('<p>Bạn chưa có bài viết nào đã lưu.</p>');
                } else {
                    let postsHtml = '';
                    response.forEach(function(post) {
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
                                            <span class="comment-toggle" style="cursor:pointer;" data-post-id="${post.id}"><i class="fas fa-comment-dots"></i> Xem Bình Luận (${post.comments_count})</span>
                                            <button class="btn btn-link unsave-post" data-post-id="${post.id}"><i class="fas fa-bookmark"></i> Bỏ lưu</button>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    });
                    $('#posts-list').html(postsHtml);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error details:', xhr.responseText);
                alert('Có lỗi xảy ra khi tải danh sách bài viết: ' + error);
            }

        });
        $(document).on('click', '.comment-toggle', function() {
            const postId = $(this).data('post-id'); // Lấy ID bài viết
            const commentSection = $(this).closest('.post-card').find('.comments-section');

            if (commentSection.is(':visible')) {
                commentSection.hide(); // Ẩn bình luận nếu đang hiển thị
                $(this).text(`Xem Bình Luận (${commentSection.data('comments-count')})`);
            } else {
                // Nếu bình luận chưa được tải thì gọi API để lấy danh sách bình luận
                if (commentSection.children().length === 0) {
                    $.ajax({
                        url: `/api/posts/${postId}/comments`, // API để lấy bình luận
                        method: 'GET',
                        success: function(response) {
                            let commentsHtml = '';
                            response.comments.forEach(function(comment) {
                                commentsHtml += `
                            <div class="comment">
                                <strong>${comment.user.username}</strong>: ${comment.content}
                                <small>${moment(comment.created_at).fromNow()}</small>
                                <div class="comment-actions">
                                    <button class="reply-button" data-comment-id="${comment.id}">Trả lời</button>
                                    <button class="like-button" data-comment-id="${comment.id}"><i class="far fa-thumbs-up"></i> <span>${comment.like_count}</span></button>
                                </div>
                            </div>
                        `;
                            });
                            commentSection.html(commentsHtml);
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi tải bình luận.');
                        }
                    });
                }
                commentSection.show(); // Hiển thị bình luận
                $(this).text('Ẩn Bình Luận');
            }
        });
        $(document).on('click', '.save-post', function() {
            const postId = $(this).data('post-id');
            const button = $(this);

            $.ajax({
                url: `/api/posts/${postId}/save`, // API để lưu bài viết
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                }, // Gửi token CSRF để xác thực
                success: function(response) {
                    if (response.saved) {
                        button.html('<i class="fas fa-bookmark"></i> Đã lưu');
                    } else {
                        button.html('<i class="fas fa-bookmark"></i> Lưu');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi lưu bài viết.');
                }
            });
        });

        // Toggle nội dung bài viết
        $(document).on('click', '.toggle-content', function() {
            var contentFull = $(this).siblings('.content-full');
            var contentPreview = $(this).siblings('.content-preview');
            if (contentFull.is(':visible')) {
                contentFull.hide();
                contentPreview.show();
                $(this).text('Xem thêm');
            } else {
                contentFull.show();
                contentPreview.hide();
                $(this).text('Thu gọn');
            }
        });
    });
</script>
@endsection