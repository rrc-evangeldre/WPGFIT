<?php
/*******w******** 
    Name: Raphael Evangelista
    Date: November 12, 2024
    Description: Updated to filter posts by both category and search keyword.
****************/

session_start();
include '../activity/db_connect.php';
include '../activity/header.php';

// Define the number of posts per page
$postsPerPage = 6;

// Get the current page from the URL
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $postsPerPage;

// Handle search query and category filter
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : '';

// Build the base SQL query
$sql = "SELECT Posts.*, Users.Username, 
               (SELECT COUNT(*) FROM Comments WHERE Comments.PostID = Posts.PostID) AS CommentCount 
        FROM Posts 
        JOIN Users ON Posts.UserID = Users.UserID";

// Add search filtering for provided query and category
$conditions = [];
$params = [];

if (!empty($searchQuery)) {
    $conditions[] = "(Posts.Title LIKE :searchQuery OR Posts.Content LIKE :searchQuery)";
    $params[':searchQuery'] = '%' . $searchQuery . '%';
}

if (!empty($selectedCategory) && $selectedCategory !== 'all') {
    $conditions[] = "Posts.Category = :selectedCategory";
    $params[':selectedCategory'] = $selectedCategory;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Add sorting and pagination
$sql .= " ORDER BY DateCreated DESC LIMIT :postsPerPage OFFSET :offset";

// Prepare and bind parameters
$query = $db->prepare($sql);

foreach ($params as $param => $value) {
    $query->bindValue($param, $value, PDO::PARAM_STR);
}

$query->bindValue(':postsPerPage', $postsPerPage, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$posts = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of posts for pagination
$totalPostsSql = "SELECT COUNT(*) FROM Posts";
if (!empty($conditions)) {
    $totalPostsSql .= " WHERE " . implode(" AND ", $conditions);
}
$totalPostsQuery = $db->prepare($totalPostsSql);

foreach ($params as $param => $value) {
    $totalPostsQuery->bindValue($param, $value, PDO::PARAM_STR);
}

$totalPostsQuery->execute();
$totalPosts = $totalPostsQuery->fetchColumn();
$totalPages = ceil($totalPosts / $postsPerPage);

$categoryQuery = $db->prepare("SELECT DISTINCT CategoryName FROM Categories WHERE CategoryID BETWEEN 1 AND 8");
$categoryQuery->execute();
$categories = $categoryQuery->fetchAll(PDO::FETCH_COLUMN);

$defaultImagePath = '../img/defaultimage.png';
?>

<div class="container-fluid">
    <main class="main-content">
        <!-- Search form with Category Dropdown -->
        <div class="row">
            <div class="col-12">
                <form method="GET" class="form-inline search-form d-flex align-items-center">
                    <!-- Category Dropdown -->
                    <select name="category" class="form-control mr-2">
                        <option value="all" <?= $selectedCategory === 'all' || empty($selectedCategory) ? 'selected' : '' ?>>All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" <?= $selectedCategory === $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Search Bar -->
                    <input class="form-control search-input" name="query" type="text" placeholder="Search..." aria-label="Search" value="<?= htmlspecialchars($searchQuery) ?>">
                    <button class="search-button" type="submit">
                        <i class="fas fa-search search-icon" aria-hidden="true"></i>
                    </button>                                 
                </form>
            </div>                
        </div>

        <!-- Posts -->
        <div class="row row-posts">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <article class="col-12 col-md-6 post">
                        <hr class="post-separator">
                        
                        <!-- Use default image if user doesn't attach an image to their post -->
                        <?php
                        $imagePath = !empty($post['filePath']) ? '../uploads/' . htmlspecialchars($post['filePath']) : $defaultImagePath;
                        ?>
                        <img src="<?= $imagePath ?>" alt="Post Image" class="img-fluid">
                        
                        <!-- Post Title -->
                        <h2 class="post-title color-primary post-title-size">
                            <a href="single_post.php?postid=<?= $post['PostID'] ?>"><?= htmlspecialchars($post['Title']) ?></a>
                        </h2>
                        
                        <!-- Post Description -->
                        <p class="post-description">
                            <?= strlen($post['Content']) > 50 ? htmlspecialchars(substr($post['Content'], 0, 50)) . "... <a href='single_post.php?postid=" . $post['PostID'] . "'>Read More</a>" : htmlspecialchars($post['Content']); ?>
                        </p>
                        
                        <div class="d-flex justify-content-between info-margin">
                            <!-- Display category name -->
                            <span class="color-primary"><?= htmlspecialchars($post['Category']) ?></span>
                            <span class="color-primary"><?= date("F j, Y", strtotime($post['DateCreated'])) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span><?= htmlspecialchars($post['CommentCount']) ?> comments</span>
                            <span>by <?= htmlspecialchars($post['Username']) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-12">No posts found matching your search or category filter.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="row prev-next-margin">
            <div class="prev-next-wrapper">
                <a href="?page=<?= max(1, $currentPage - 1) ?>&query=<?= urlencode($searchQuery) ?>&category=<?= urlencode($selectedCategory) ?>" class="mb-2 pg-btn pg-btn-primary prev-next <?= $currentPage <= 1 ? 'disabled' : '' ?> prev-next-gap">Prev</a>
                <a href="?page=<?= min($totalPages, $currentPage + 1) ?>&query=<?= urlencode($searchQuery) ?>&category=<?= urlencode($selectedCategory) ?>" class="mb-2 pg-btn pg-btn-primary prev-next <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">Next</a>
            </div>
            <div class="page-wrapper">
                <span class="d-inline-block mr-3">Page</span>
                <nav class="paging-nav d-inline-block">
                    <ul>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="pg-num <?= $i === $currentPage ? 'active' : '' ?>">
                                <a href="?page=<?= $i ?>&query=<?= urlencode($searchQuery) ?>&category=<?= urlencode($selectedCategory) ?>" class="mb-2 pg-btn pg-num-link"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </main>
</div>

<?php include '../activity/footer.php'; ?>
