<?php
include_once "_header.php";
include_once "_subHeader.php";

// Authorization check: Only Super Admin (role 1) and Admin/Editor (role 2)
if (!isset($_SESSION['role']) || ((int)$_SESSION['role'] !== 1 && (int)$_SESSION['role'] !== 2)) {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit();
}

// Ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Parameters & Filters
$activeTab = htmlspecialchars($_GET['tab'] ?? 'posts');
$validTabs = ['posts', 'categories', 'add-redirect'];
if (!in_array($activeTab, $validTabs, true)) {
    $activeTab = 'posts';
}

$searchQuery = trim($_GET['search'] ?? '');
$categoryFilter = (int)($_GET['category'] ?? 0);
$focusPostId = (int)($_GET['post_id'] ?? 0);

// Fetch Metrics
$totalSlugsQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `post_slugs`");
$totalSlugs = $totalSlugsQ ? (int)mysqli_fetch_assoc($totalSlugsQ)['total'] : 0;

$canonicalSlugsQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `post` WHERE `post_slug` IS NOT NULL AND `post_slug` != ''");
$canonicalSlugs = $canonicalSlugsQ ? (int)mysqli_fetch_assoc($canonicalSlugsQ)['total'] : 0;

$aliasSlugsQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `post_slugs` WHERE `is_primary` = 0");
$aliasSlugs = $aliasSlugsQ ? (int)mysqli_fetch_assoc($aliasSlugsQ)['total'] : 0;

$categorySlugsQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `category` WHERE `category_slug` IS NOT NULL AND `category_slug` != ''");
$categorySlugs = $categorySlugsQ ? (int)mysqli_fetch_assoc($categorySlugsQ)['total'] : 0;

// Fetch Categories for Dropdowns & Category Tab
$allCategoriesQ = mysqli_query($conn, "SELECT category_id, category_name, category_slug, 
    (SELECT COUNT(*) FROM `post` WHERE `post`.`category` = `category`.`category_id`) AS post_count 
    FROM `category` ORDER BY category_name ASC");
$categoriesList = [];
if ($allCategoriesQ) {
    while ($catRow = mysqli_fetch_assoc($allCategoriesQ)) {
        $categoriesList[] = $catRow;
    }
}

// Build Posts Query
$whereClauses = ["1=1"];
if (!empty($categoryFilter)) {
    $whereClauses[] = "post.category = {$categoryFilter}";
}
if (!empty($focusPostId)) {
    $whereClauses[] = "post.post_id = {$focusPostId}";
}
if (!empty($searchQuery)) {
    $escapedSearch = mysqli_real_escape_string($conn, $searchQuery);
    $whereClauses[] = "(post.title LIKE '%{$escapedSearch}%' OR post.post_slug LIKE '%{$escapedSearch}%' OR post.slug_aliases LIKE '%{$escapedSearch}%')";
}
$whereSql = implode(' AND ', $whereClauses);

$postsSql = "SELECT post.post_id, post.title, post.post_slug, post.slug_aliases, post.post_date, post.postStatus, post.author,
    category.category_id, category.category_name, category.category_slug,
    user.first_name, user.last_name, user.username
    FROM `post`
    LEFT JOIN `category` ON category.category_id = post.category
    LEFT JOIN `user` ON user.user_id = post.author
    WHERE {$whereSql}
    ORDER BY post.post_id DESC";
$postsResult = mysqli_query($conn, $postsSql);

// Fetch all aliases per post from post_slugs index
$postAliasesMap = [];
$aliasesIndexQ = mysqli_query($conn, "SELECT post_id, slug, is_primary FROM `post_slugs` WHERE is_primary = 0 ORDER BY id ASC");
if ($aliasesIndexQ) {
    while ($alRow = mysqli_fetch_assoc($aliasesIndexQ)) {
        $pid = (int)$alRow['post_id'];
        if (!isset($postAliasesMap[$pid])) {
            $postAliasesMap[$pid] = [];
        }
        $postAliasesMap[$pid][] = $alRow['slug'];
    }
}

// All posts for Quick Redirect Form
$allPostsDropdownQ = mysqli_query($conn, "SELECT post_id, title, post_slug, category FROM `post` ORDER BY post_id DESC");
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold py-1 mb-1">
                    <span class="text-muted fw-light">System /</span> Slug &amp; URL Manager
                </h4>
                <p class="text-muted mb-0">Centrally monitor, configure, and optimize SEO canonical URLs and multi-slug 301 redirect aliases.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalQuickAddAlias">
                    <i class="bx bx-plus-circle me-1"></i> Add 301 Alias
                </button>
                <a href="view-post.php" class="btn btn-outline-secondary">
                    <i class="bx bx-news me-1"></i> View Posts
                </a>
                <a href="<?php echo htmlspecialchars($url); ?>" target="_blank" class="btn btn-outline-primary">
                    <i class="bx bx-globe me-1"></i> Visit Site
                </a>
            </div>
        </div>

        <!-- SEO Architecture Explainer Banner -->
        <div class="alert alert-primary alert-dismissible shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="bx bx-info-circle fs-3 me-3 mt-1 text-primary"></i>
                <div class="flex-grow-1">
                    <h6 class="alert-heading fw-bold mb-1">Clean Multi-Segment Routing &amp; 301 Multi-Slug Engine</h6>
                    <p class="mb-1 text-dark">
                        Every article automatically uses the hierarchical canonical URL: <code class="bg-light px-2 py-1 rounded text-primary">/<?php echo '{category_slug}/{primary_slug}'; ?></code>.
                    </p>
                    <small class="text-muted">
                        <strong>Multi-Slug 301 Redirects:</strong> Add keyword variants, campaign links, or preserve previous permalinks. Any request matching an alias will automatically issue an HTTP 301 Permanent Redirect to the canonical URL, preserving 100% of backlinks and SEO ranking with zero duplicate content issues.
                    </small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <!-- Metrics Overview Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-link fs-4"></i></span>
                            </div>
                            <div class="text-end">
                                <span class="fw-semibold d-block mb-1 text-muted">Total Indexed Slugs</span>
                                <h3 class="card-title mb-0 text-primary"><?php echo number_format($totalSlugs); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-check-double fs-4"></i></span>
                            </div>
                            <div class="text-end">
                                <span class="fw-semibold d-block mb-1 text-muted">Canonical Post Slugs</span>
                                <h3 class="card-title mb-0 text-success"><?php echo number_format($canonicalSlugs); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-git-merge fs-4"></i></span>
                            </div>
                            <div class="text-end">
                                <span class="fw-semibold d-block mb-1 text-muted">Active 301 Aliases</span>
                                <h3 class="card-title mb-0 text-warning"><?php echo number_format($aliasSlugs); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-category fs-4"></i></span>
                            </div>
                            <div class="text-end">
                                <span class="fw-semibold d-block mb-1 text-muted">Category Slugs</span>
                                <h3 class="card-title mb-0 text-info"><?php echo number_format($categorySlugs); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills flex-column flex-md-row mb-4" id="slugManagerTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'posts') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-posts" type="button" role="tab">
                    <i class="bx bx-file me-1"></i> Post Slugs &amp; 301 Aliases
                    <span class="badge rounded-pill bg-label-primary ms-1"><?php echo number_format($canonicalSlugs); ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'categories') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-categories" type="button" role="tab">
                    <i class="bx bx-category-alt me-1"></i> Category Slugs
                    <span class="badge rounded-pill bg-label-info ms-1"><?php echo number_format(count($categoriesList)); ?></span>
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'add-redirect') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-add-redirect" type="button" role="tab">
                    <i class="bx bx-plus me-1"></i> Quick 301 Redirect Form
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content p-0" id="slugManagerContent">

            <!-- TAB 1: POST SLUGS & 301 ALIASES -->
            <div class="tab-pane fade <?php echo ($activeTab === 'posts') ? 'show active' : ''; ?>" id="tab-posts" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3">
                        <form method="GET" action="manage-slugs.php" class="row g-2 align-items-center">
                            <input type="hidden" name="tab" value="posts">
                            
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" class="form-control" name="search" placeholder="Search by title, primary slug, or alias..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <select class="form-select" name="category" onchange="this.form.submit()">
                                    <option value="0">All Categories</option>
                                    <?php foreach ($categoriesList as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>" <?php echo ($categoryFilter === (int)$cat['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['category_name']); ?> (<?php echo $cat['post_count']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bx bx-filter-alt me-1"></i> Filter
                                </button>
                                <?php if (!empty($searchQuery) || !empty($categoryFilter) || !empty($focusPostId)): ?>
                                    <a href="manage-slugs.php?tab=posts" class="btn btn-outline-secondary" title="Reset Filters">
                                        <i class="bx bx-reset"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <?php if (!empty($focusPostId)): ?>
                        <div class="px-4 py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                            <small class="text-primary fw-semibold"><i class="bx bx-target-lock me-1"></i> Filtering by Post ID: #<?php echo $focusPostId; ?></small>
                            <a href="manage-slugs.php?tab=posts" class="btn btn-sm btn-link text-decoration-none p-0">Clear filter</a>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">ID</th>
                                    <th>Post Title</th>
                                    <th>Category</th>
                                    <th>Primary Canonical Slug</th>
                                    <th>301 Alias Redirects (Multi-Slug)</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($postsResult && mysqli_num_rows($postsResult) > 0) {
                                    while ($p = mysqli_fetch_assoc($postsResult)) {
                                        $pid = (int)$p['post_id'];
                                        $canonicalUrl = getPostUrl($p, $url);
                                        $catSlug = !empty($p['category_slug']) ? $p['category_slug'] : 'post';
                                        $primarySlug = $p['post_slug'];
                                        
                                        // Collect aliases from post_slugs index or fallback to slug_aliases
                                        $aliases = $postAliasesMap[$pid] ?? [];
                                        if (empty($aliases) && !empty($p['slug_aliases'])) {
                                            $raw = explode(',', $p['slug_aliases']);
                                            foreach ($raw as $ra) {
                                                $cl = trim($ra);
                                                if (!empty($cl) && !in_array($cl, $aliases)) {
                                                    $aliases[] = $cl;
                                                }
                                            }
                                        }
                                        $isFocused = ($focusPostId === $pid);
                                        ?>
                                        <tr class="<?php echo $isFocused ? 'table-warning' : ''; ?>" id="post-row-<?php echo $pid; ?>">
                                            <td><span class="text-muted fw-bold">#<?php echo $pid; ?></span></td>
                                            <td style="white-space: normal; min-width: 250px; max-width: 350px;">
                                                <a href="update-post.php?postid=<?php echo base64_encode($pid); ?>" class="fw-semibold text-dark text-decoration-none d-block">
                                                    <?php echo htmlspecialchars($p['title']); ?>
                                                </a>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="bx bx-user me-1"></i><?php echo htmlspecialchars(trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?: ($p['username'] ?? 'Author')); ?> 
                                                    &bull; <i class="bx bx-calendar me-1"></i><?php echo htmlspecialchars($p['post_date'] ?? ''); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">
                                                    <i class="bx bx-folder me-1"></i><?php echo htmlspecialchars($p['category_name'] ?? 'Uncategorized'); ?>
                                                </span>
                                            </td>
                                            <td style="min-width: 220px;">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="badge bg-label-success font-monospace px-2 py-1 text-wrap" style="font-size: 0.85rem;">
                                                        /<?php echo htmlspecialchars($primarySlug); ?>
                                                    </span>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary copy-btn" data-url="<?php echo htmlspecialchars($canonicalUrl); ?>" title="Copy Canonical URL">
                                                        <i class="bx bx-copy"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-outline-primary btn-edit-primary" 
                                                        data-pid="<?php echo $pid; ?>" 
                                                        data-title="<?php echo htmlspecialchars($p['title']); ?>" 
                                                        data-slug="<?php echo htmlspecialchars($primarySlug); ?>" 
                                                        title="Edit Canonical Slug">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                                        Route: /<?php echo htmlspecialchars($catSlug); ?>/<strong><?php echo htmlspecialchars($primarySlug); ?></strong>
                                                    </small>
                                                </div>
                                            </td>
                                            <td style="white-space: normal; min-width: 250px;">
                                                <div class="d-flex flex-wrap align-items-center gap-1">
                                                    <?php if (!empty($aliases)): ?>
                                                        <?php foreach ($aliases as $alias): ?>
                                                            <span class="badge bg-label-warning d-inline-flex align-items-center py-1 px-2 font-monospace" style="font-size: 0.8rem;">
                                                                <i class="bx bx-git-merge me-1"></i>/<?php echo htmlspecialchars($alias); ?>
                                                                <button type="button" class="btn-close ms-2 btn-delete-alias" 
                                                                    data-pid="<?php echo $pid; ?>" 
                                                                    data-alias="<?php echo htmlspecialchars($alias); ?>" 
                                                                    style="font-size: 0.5rem;" title="Delete this 301 alias redirect"></button>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted small fst-italic">None</span>
                                                    <?php endif; ?>

                                                    <button type="button" class="btn btn-xs btn-outline-primary btn-add-alias ms-1" 
                                                        data-pid="<?php echo $pid; ?>" 
                                                        data-title="<?php echo htmlspecialchars($p['title']); ?>" 
                                                        data-catslug="<?php echo htmlspecialchars($catSlug); ?>" 
                                                        title="Add new 301 redirect alias">
                                                        <i class="bx bx-plus me-1"></i> Add
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="<?php echo htmlspecialchars($canonicalUrl); ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-success" title="Test Live Canonical URL">
                                                        <i class="bx bx-link-external"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-edit-primary" 
                                                        data-pid="<?php echo $pid; ?>" 
                                                        data-title="<?php echo htmlspecialchars($p['title']); ?>" 
                                                        data-slug="<?php echo htmlspecialchars($primarySlug); ?>" 
                                                        title="Manage Slugs">
                                                        <i class="bx bx-cog"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bx bx-search-alt fs-1 d-block mb-2 text-secondary"></i>
                                            <p class="mb-0">No posts matched your search or filter.</p>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: CATEGORY SLUGS -->
            <div class="tab-pane fade <?php echo ($activeTab === 'categories') ? 'show active' : ''; ?>" id="tab-categories" role="tabpanel">
                <div class="card shadow-sm border-0">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 fw-bold"><i class="bx bx-category-alt me-2 text-info"></i>Category URL Slugs</h5>
                            <small class="text-muted">Manage the root directory slug prefix for each category section.</small>
                        </div>
                        <a href="new-category.php" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus me-1"></i> New Category
                        </a>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Category Name</th>
                                    <th>Clean URL Slug</th>
                                    <th>Live URL Path</th>
                                    <th>Articles</th>
                                    <th class="text-center" style="width: 140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categoriesList)): ?>
                                    <?php foreach ($categoriesList as $cat): 
                                        $catUrl = getCategoryUrl($cat, $url);
                                    ?>
                                        <tr>
                                            <td><strong>#<?php echo $cat['category_id']; ?></strong></td>
                                            <td>
                                                <span class="fw-semibold text-dark"><?php echo htmlspecialchars($cat['category_name']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info font-monospace px-2 py-1" style="font-size: 0.85rem;">
                                                    <?php echo htmlspecialchars($cat['category_slug']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <code class="text-primary">/category/<?php echo htmlspecialchars($cat['category_slug']); ?></code>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-secondary"><?php echo number_format($cat['post_count']); ?> posts</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <a href="<?php echo htmlspecialchars($catUrl); ?>" target="_blank" class="btn btn-sm btn-icon btn-outline-success" title="Test Live Category URL">
                                                        <i class="bx bx-link-external"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-primary btn-edit-category" 
                                                        data-cid="<?php echo $cat['category_id']; ?>" 
                                                        data-name="<?php echo htmlspecialchars($cat['category_name']); ?>" 
                                                        data-slug="<?php echo htmlspecialchars($cat['category_slug']); ?>" 
                                                        title="Edit Category Slug">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No categories found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: QUICK 301 REDIRECT FORM -->
            <div class="tab-pane fade <?php echo ($activeTab === 'add-redirect') ? 'show active' : ''; ?>" id="tab-add-redirect" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="card shadow-sm border-0">
                            <div class="card-header border-bottom py-3">
                                <h5 class="mb-0 fw-bold"><i class="bx bx-git-merge me-2 text-warning"></i>Quick 301 Alias Redirect Creator</h5>
                                <small class="text-muted">Register a custom alias permalink that will 301-redirect seamlessly to any canonical article.</small>
                            </div>
                            <div class="card-body py-4">
                                <form action="app/app.php" method="POST" id="formQuickAddRedirect">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="add_slug_alias" value="1">

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="quick_post_id">Target Article <span class="text-danger">*</span></label>
                                        <select class="form-select form-select-lg" name="post_id" id="quick_post_id" required>
                                            <option value="" disabled selected>-- Select an article --</option>
                                            <?php
                                            if ($allPostsDropdownQ && mysqli_num_rows($allPostsDropdownQ) > 0) {
                                                while ($drp = mysqli_fetch_assoc($allPostsDropdownQ)) {
                                                    echo '<option value="' . $drp['post_id'] . '">' . htmlspecialchars($drp['title']) . ' (/' . htmlspecialchars($drp['post_slug']) . ')</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="form-text">Choose the published article to which this alias should redirect.</div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold" for="quick_alias_slug">New Custom Alias Slug <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">/</span>
                                            <input type="text" class="form-control form-control-lg font-monospace" name="alias_slug" id="quick_alias_slug" placeholder="e.g. old-campaign-url or previous-slug" required>
                                        </div>
                                        <div class="form-text">Will be automatically converted into a clean URL-safe slug format (lowercase, hyphens only).</div>
                                    </div>

                                    <div class="alert alert-info py-2 mb-4 d-flex align-items-center">
                                        <i class="bx bx-check-shield fs-4 me-2"></i>
                                        <small>Once saved, any visitor or search engine requesting this alias will be instantly redirected via <strong>HTTP 301 (Permanent Redirect)</strong>.</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                        <i class="bx bx-save me-1"></i> Register 301 Alias Redirect
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /tab-content -->

    </div><!-- /container-xxl -->
</div><!-- /content-wrapper -->

<!-- MODAL: EDIT PRIMARY SLUG -->
<div class="modal fade" id="modalEditPrimary" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="app/app.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="update_primary_slug" value="1">
            <input type="hidden" name="post_id" id="edit_primary_post_id">

            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bx bx-edit text-primary me-2"></i>Update Canonical Primary Slug</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label text-muted small">Article Title</label>
                    <div class="fw-semibold text-dark" id="edit_primary_title_display"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="edit_primary_slug_input">New Canonical Primary Slug <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">/</span>
                        <input type="text" class="form-control font-monospace" name="primary_slug" id="edit_primary_slug_input" required>
                    </div>
                    <div class="form-text">This will be the main canonical URL of this article.</div>
                </div>

                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="preserve_alias" id="edit_preserve_alias" value="1" checked>
                    <label class="form-check-label fw-semibold text-dark" for="edit_preserve_alias">
                        Keep old slug as 301 redirect alias <span class="badge bg-label-success ms-1">Recommended</span>
                    </label>
                    <div class="form-text text-muted small">Automatically preserves old URL so existing links, bookmarks, and search engine indices continue to work seamlessly.</div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: ADD ALIAS REDIRECT -->
<div class="modal fade" id="modalAddAlias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="app/app.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="add_slug_alias" value="1">
            <input type="hidden" name="post_id" id="add_alias_post_id">

            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bx bx-git-merge text-warning me-2"></i>Add 301 Alias Redirect</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label text-muted small">Article Title</label>
                    <div class="fw-semibold text-dark" id="add_alias_title_display"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="add_alias_slug_input">New Alias Slug <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">/</span>
                        <input type="text" class="form-control font-monospace" name="alias_slug" id="add_alias_slug_input" placeholder="alternative-slug" required>
                    </div>
                    <div class="form-text">Enter an alternative keyword or old permalink. Requests to this URL will 301-redirect to canonical.</div>
                </div>

                <div class="bg-light p-3 rounded border">
                    <small class="text-muted d-block mb-1">Redirect Preview:</small>
                    <div class="font-monospace text-primary small">
                        <span id="preview_alias_path">/category/alias-slug</span> 
                        <i class="bx bx-right-arrow-alt text-dark mx-1"></i> 
                        <span class="badge bg-label-success">301 Redirect</span> 
                        <i class="bx bx-right-arrow-alt text-dark mx-1"></i> 
                        <span class="text-success fw-bold">Canonical URL</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-plus-circle me-1"></i> Add Alias</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: QUICK ADD ALIAS FROM HEADER -->
<div class="modal fade" id="modalQuickAddAlias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="app/app.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="add_slug_alias" value="1">

            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bx bx-git-merge text-warning me-2"></i>Quick Add 301 Redirect Alias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="quick_modal_post_id">Target Article <span class="text-danger">*</span></label>
                    <select class="form-select" name="post_id" id="quick_modal_post_id" required>
                        <option value="" disabled selected>-- Select an article --</option>
                        <?php
                        if ($allPostsDropdownQ && mysqli_num_rows($allPostsDropdownQ) > 0) {
                            mysqli_data_seek($allPostsDropdownQ, 0);
                            while ($drp = mysqli_fetch_assoc($allPostsDropdownQ)) {
                                echo '<option value="' . $drp['post_id'] . '">' . htmlspecialchars($drp['title']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="quick_modal_alias_input">New Alias Slug <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">/</span>
                        <input type="text" class="form-control font-monospace" name="alias_slug" id="quick_modal_alias_input" placeholder="alternative-slug" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Add Alias</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT CATEGORY SLUG -->
<div class="modal fade" id="modalEditCategory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="app/app.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="update_category_slug_direct" value="1">
            <input type="hidden" name="category_id" id="edit_cat_id">

            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"><i class="bx bx-edit text-info me-2"></i>Update Category Slug</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label text-muted small">Category Name</label>
                    <div class="fw-semibold text-dark" id="edit_cat_name_display"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold" for="edit_cat_slug_input">Category Slug <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">/category/</span>
                        <input type="text" class="form-control font-monospace" name="category_slug" id="edit_cat_slug_input" required>
                    </div>
                    <div class="form-text">Defines the root category permalink prefix for all articles in this category.</div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Update Category Slug</button>
            </div>
        </form>
    </div>
</div>

<!-- FORM: HIDDEN DELETE ALIAS -->
<form id="formDeleteAlias" action="app/app.php" method="POST" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="remove_slug_alias" value="1">
    <input type="hidden" name="post_id" id="del_alias_post_id">
    <input type="hidden" name="alias_slug" id="del_alias_slug">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Slugify helper
        function slugifyText(text) {
            return text.toString().toLowerCase().trim()
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        // Copy button with feedback
        $('.copy-btn').click(function () {
            var url = $(this).data('url');
            var btn = $(this);
            navigator.clipboard.writeText(url).then(function () {
                btn.html('<i class="bx bx-check text-success"></i>');
                setTimeout(function () {
                    btn.html('<i class="bx bx-copy"></i>');
                }, 1500);
            });
        });

        // Edit Primary Slug Modal open
        $('.btn-edit-primary').click(function () {
            var pid = $(this).data('pid');
            var title = $(this).data('title');
            var slug = $(this).data('slug');

            $('#edit_primary_post_id').val(pid);
            $('#edit_primary_title_display').text(title);
            $('#edit_primary_slug_input').val(slug);
            new bootstrap.Modal(document.getElementById('modalEditPrimary')).show();
        });

        // Add Alias Modal open
        $('.btn-add-alias').click(function () {
            var pid = $(this).data('pid');
            var title = $(this).data('title');
            var catSlug = $(this).data('catslug') || 'category';

            $('#add_alias_post_id').val(pid);
            $('#add_alias_title_display').text(title);
            $('#add_alias_slug_input').val('');
            $('#preview_alias_path').text('/' + catSlug + '/[new-alias]');
            new bootstrap.Modal(document.getElementById('modalAddAlias')).show();
        });

        // Add Alias typing preview
        $('#add_alias_slug_input').on('input', function () {
            var val = slugifyText($(this).val());
            $('#preview_alias_path').text('/.../' + (val || '[new-alias]'));
        });

        // Edit Category Modal open
        $('.btn-edit-category').click(function () {
            var cid = $(this).data('cid');
            var name = $(this).data('name');
            var slug = $(this).data('slug');

            $('#edit_cat_id').val(cid);
            $('#edit_cat_name_display').text(name);
            $('#edit_cat_slug_input').val(slug);
            new bootstrap.Modal(document.getElementById('modalEditCategory')).show();
        });

        // Delete Alias Click
        $('.btn-delete-alias').click(function (e) {
            e.preventDefault();
            var pid = $(this).data('pid');
            var alias = $(this).data('alias');

            if (confirm("Are you sure you want to remove the 301 alias redirect '/" + alias + "'? Any links pointing to this alias will no longer redirect.")) {
                $('#del_alias_post_id').val(pid);
                $('#del_alias_slug').val(alias);
                $('#formDeleteAlias').submit();
            }
        });

        // Sync tab clicks with URL query param so refresh stays on active tab
        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('data-bs-target').replace('#tab-', '');
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + target;
                window.history.pushState({path: newurl}, '', newurl);
            }
        });
    });
</script>

<?php include_once "_footer.php"; ?>
