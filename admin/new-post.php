<?php include_once '_header.php';
include_once '_subHeader.php'; ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <form id="newPost" action="app/app.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="author_id" value="<?php echo htmlspecialchars($_SESSION['author_id'] ?? ''); ?>">
            <input type="hidden" name="saveNew_post" value="1">

            <div class="row">
                <!-- Main Content Column -->
                <div class="col-lg-8">
                    <!-- Post Content Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0 text-primary fw-bold"><i class="bx bx-edit me-1"></i> Add New Post</h5>
                            <small class="text-muted"><i class="bx bx-user me-1"></i> Author: <?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></small>
                        </div>
                        <div class="card-body py-4">
                            <div class="alert alert-dismissible" id="message" role="alert" style="display:none;"></div>

                            <!-- Post Title -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="post_title">Post Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" name="post_title" id="post_title" placeholder="Enter headline or article title" required autocomplete="off">
                            </div>

                            <!-- Post Slug -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="post_slug">Post Slug (URL) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light text-muted">/news/</span>
                                    <input type="text" class="form-control" name="post_slug" id="post_slug" placeholder="post-url-slug" required>
                                    <button class="btn btn-outline-secondary" type="button" id="btn_regenerate_slug" title="Regenerate from Title">
                                        <i class="bx bx-refresh"></i>
                                    </button>
                                </div>
                                <div class="form-text">The slug is the user-friendly URL version of the title.</div>
                            </div>

                            <!-- Short Description -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="post_short_desc">Post Short Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="post_short_desc" id="post_short_desc" rows="2" placeholder="Brief summary of the article..." required></textarea>
                            </div>

                            <!-- Full Description -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="description">Full Content <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control summernote" placeholder="Write main post body content..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Optimization Card -->
                    <div class="card mb-4 shadow-sm border-top border-primary border-3">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3 bg-light">
                            <h5 class="mb-0 text-dark fw-bold"><i class="bx bx-search-alt text-primary me-2"></i> Search Engine Optimization (SEO)</h5>
                            <span class="badge bg-label-primary fs-tiny">SEO Tool</span>
                        </div>
                        <div class="card-body py-4">
                            <!-- Live Google Search Preview Box -->
                            <div class="p-3 mb-4 rounded border bg-light">
                                <div class="text-uppercase text-muted fw-bold fs-tiny mb-2"><i class="bx bxl-google me-1"></i> Google Search Preview</div>
                                <div class="google-snippet-preview">
                                    <div class="text-primary fw-semibold fs-5 text-truncate" id="seo_preview_title">Your Article Title Will Appear Here</div>
                                    <div class="text-success small text-truncate" id="seo_preview_url"><?php echo isset($_SERVER['HTTP_HOST']) ? 'https://' . $_SERVER['HTTP_HOST'] : 'https://example.com'; ?>/news/<span id="seo_preview_slug_text">your-post-slug</span></div>
                                    <div class="text-secondary small line-clamp-2 mt-1" id="seo_preview_desc">Your meta description or short description will be displayed here in search engine results...</div>
                                </div>
                            </div>

                            <!-- Meta Title -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label fw-semibold" for="meta_title">Meta Title</label>
                                    <small class="text-muted"><span id="meta_title_counter">0</span> / 60 Chars</small>
                                </div>
                                <input type="text" class="form-control" name="meta_title" id="meta_title" maxlength="70" placeholder="Custom SEO Title (Leave empty to use Post Title)">
                            </div>

                            <!-- Meta Description -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label fw-semibold" for="meta_description">Meta Description</label>
                                    <small class="text-muted"><span id="meta_desc_counter">0</span> / 160 Chars</small>
                                </div>
                                <textarea class="form-control" name="meta_description" id="meta_description" rows="3" maxlength="170" placeholder="Custom summary for search engines (Leave empty to use Short Description)"></textarea>
                            </div>

                            <!-- Meta Keywords -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="meta_keywords">Focus Keywords / Tags</label>
                                <input type="text" class="form-control" name="meta_keywords" id="meta_keywords" placeholder="e.g. news, breaking, sports, trending (comma separated)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings Column -->
                <div class="col-lg-4">
                    <!-- Publishing Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header border-bottom py-3 bg-light">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-paper-plane me-1 text-primary"></i> Publish Settings</h5>
                        </div>
                        <div class="card-body py-4">
                            <!-- Category Selection -->
                            <div class="mb-4">
                                <label for="post_category" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                                <?php
                                $categoryQ = "SELECT * FROM category WHERE categoryStatus = 'Y'";
                                $categoryR = mysqli_query($conn, $categoryQ);
                                if ($categoryR && mysqli_num_rows($categoryR) > 0) {
                                    ?>
                                    <select class="form-select form-select-md" id="post_category" name="post_category" required>
                                        <option value="" selected disabled>Select Category</option>
                                        <?php
                                        while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                                            echo "<option value='{$categoryD['category_id']}'>" . htmlspecialchars($categoryD['category_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                <?php } else { ?>
                                    <div class="alert alert-warning py-2 px-3 small">No active categories found! Please add a category first.</div>
                                <?php } ?>
                            </div>

                            <hr class="my-3">

                            <!-- Submit Button -->
                            <button type="button" id="saveNew_post" class="btn btn-primary w-100 btn-lg shadow-sm">
                                <i class="bx bx-plus-circle me-1"></i> Publish Post
                            </button>
                        </div>
                    </div>

                    <!-- Featured Image Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header border-bottom py-3 bg-light">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-image me-1 text-primary"></i> Featured Image</h5>
                        </div>
                        <div class="card-body py-4 text-center">
                            <div class="mb-3">
                                <label for="postImage" class="form-label fw-semibold text-start d-block">Upload Image <span class="text-danger">*</span></label>
                                <input class="form-control" type="file" id="postImage" name="postImage" accept="image/*" required>
                            </div>

                            <!-- Preview Box -->
                            <div class="card border border-dashed p-2 bg-light text-center" id="postImgShowWrapper" style="display:none;">
                                <img class="img-fluid rounded" id="postImgShow" src="" style="max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    // Helper function to create URL Slug
    function slugify(text) {
        return text.toString().toLowerCase()
            .trim()
            .replace(/\s+/g, '-')           // Replace spaces with -
            .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
            .replace(/\-\-+/g, '-')         // Replace multiple - with single -
            .replace(/^-+/, '')             // Trim - from start of text
            .replace(/-+$/, '');            // Trim - from end of text
    }

    $(document).ready(function() {
        // Initialize Summernote Editor
        $('.summernote').summernote({
            placeholder: 'Write your post description here...',
            tabsize: 2,
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });

        // Image Preview Handler
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#postImgShow').attr('src', e.target.result);
                    $('#postImgShowWrapper').css('display', 'block').show();
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).on('change input', '#postImage', function() {
            readURL(this);
        });

        // Slug Auto-generation & Sync
        var manualSlugEdit = false;

        $('#post_title').on('input keyup change', function() {
            var titleVal = $(this).val();
            if (!manualSlugEdit) {
                var generatedSlug = slugify(titleVal);
                $('#post_slug').val(generatedSlug);
                updateSeoPreview();
            } else {
                updateSeoPreview();
            }
        });

        $('#post_slug').on('input keyup change', function() {
            manualSlugEdit = true;
            var cleanSlug = slugify($(this).val());
            $(this).val(cleanSlug);
            updateSeoPreview();
        });

        $('#btn_regenerate_slug').click(function() {
            manualSlugEdit = false;
            var generatedSlug = slugify($('#post_title').val());
            $('#post_slug').val(generatedSlug);
            updateSeoPreview();
        });

        // SEO Realtime Preview & Counters
        function updateSeoPreview() {
            var title = $('#meta_title').val() || $('#post_title').val() || 'Your Article Title Will Appear Here';
            var slug = $('#post_slug').val() || 'your-post-slug';
            var desc = $('#meta_description').val() || $('#post_short_desc').val() || 'Your meta description or short description will be displayed here in search engine results...';

            $('#seo_preview_title').text(title);
            $('#seo_preview_slug_text').text(slug);
            $('#seo_preview_desc').text(desc);

            $('#meta_title_counter').text($('#meta_title').val().length);
            $('#meta_desc_counter').text($('#meta_description').val().length);
        }

        $('#meta_title, #meta_description, #post_short_desc').on('input keyup change', function() {
            updateSeoPreview();
        });

        // Form Submit Validation
        $('#saveNew_post').click(function() {
            var title = $('#post_title').val().trim();
            var shortDesc = $('#post_short_desc').val().trim();
            var postImage = $('#postImage').val();
            var post_category = $('#post_category').val();
            var isSummernoteEmpty = $('#description').summernote('isEmpty');
            var description = isSummernoteEmpty ? '' : $('#description').summernote('code').trim();

            if (title == '' || shortDesc == '' || postImage == '' || post_category == null || description == '') {
                $('#message').removeClass('alert-success').addClass('alert-danger')
                    .html('<i class="bx bx-error me-1"></i> Please fill in all required fields marked with (*).')
                    .fadeIn(500, function() {
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                        setTimeout(function() { $('#message').fadeOut(500); }, 4000);
                    });
            } else {
                $('#newPost').submit();
            }
        });
    });
</script>

<!-- / Content -->
<?php include_once '_footer.php'; ?>