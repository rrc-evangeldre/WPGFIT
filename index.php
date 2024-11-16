<?php 
/*******w******** 
 
    Name: Raphael Evangelista
    Date: November 12, 2024
    Description: This is the main page of the site where posts are displayed.

****************/

session_start();
include 'db_connect.php';
include 'activity/header.php'; 

// Define the number of posts per page
$postsPerPage = 6;

// Get the current page from the URL (default is page 1)
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $postsPerPage;

// Fetch posts from the database with LIMIT and OFFSET for pagination
$query = $db->prepare("SELECT Posts.*, Users.Username, 
                            (SELECT COUNT(*) FROM Comments WHERE Comments.PostID = Posts.PostID) AS CommentCount 
                     FROM Posts 
                     JOIN Users ON Posts.UserID = Users.UserID 
                     ORDER BY DateCreated DESC 
                     LIMIT :postsPerPage OFFSET :offset");
$query->bindParam(':postsPerPage', $postsPerPage, PDO::PARAM_INT);
$query->bindParam(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$posts = $query->fetchAll(PDO::FETCH_ASSOC);

// Fetch total number of posts for pagination
$totalPostsQuery = $db->query("SELECT COUNT(*) FROM Posts");
$totalPosts = $totalPostsQuery->fetchColumn();
$totalPages = ceil($totalPosts / $postsPerPage);

$defaultImagePath = 'img/defaultimage.png';
?>

<div class="container-fluid">
    <main class="main-content">
        <!-- Search form -->
        <div class="row">
            <div class="col-12">
                <form method="GET" class="form-inline search-form">
                    <input class="form-control search-input" name="query" type="text" placeholder="Search..." aria-label="Search">
                    <button class="search-button" type="submit">
                        <i class="fas fa-search search-icon" aria-hidden="true"></i>
                    </button>                                 
                </form>
            </div>                
        </div>

        <!-- Posts -->
        <div class="row row-posts">
            <?php foreach ($posts as $post): ?>
                <article class="col-12 col-md-6 post">
                    <hr class="post-separator">
                    
                    <!-- Use default image if user doesn't attach an image to their post -->
                    <?php
                    $imagePath = !empty($post['filePath']) ? htmlspecialchars($post['filePath']) : $defaultImagePath;
                    ?>
                    <img src="<?= $imagePath ?>" alt="Post Image" class="img-fluid">
                    
                    <!-- Make the post title a link to the full post page -->
                    <h2 class="post-title color-primary post-title-size">
                        <a href="single_post.php?postid=<?= $post['PostID'] ?>"><?= htmlspecialchars($post['Title']) ?></a>
                    </h2>
                    
                    <!-- Post Description with limit and "Read More" link -->
                    <p class="post-description">
                        <?= strlen($post['Content']) > 50 ? htmlspecialchars(substr($post['Content'], 0, 50)) . "... <a href='single_post.php?postid=" . $post['PostID'] . "'>Read More</a>" : htmlspecialchars($post['Content']); ?>
                    </p>
                    
                    <div class="d-flex justify-content-between info-margin">
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
        </div>

        <!-- Pagination -->
        <div class="row prev-next-margin">
            <div class="prev-next-wrapper">
                <a href="?page=<?= max(1, $currentPage - 1) ?>" class="mb-2 pg-btn pg-btn-primary prev-next <?= $currentPage <= 1 ? 'disabled' : '' ?> prev-next-gap">Prev</a>
                <a href="?page=<?= min($totalPages, $currentPage + 1) ?>" class="mb-2 pg-btn pg-btn-primary prev-next <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">Next</a>
            </div>
            <div class="page-wrapper">
                <span class="d-inline-block mr-3">Page</span>
                <nav class="paging-nav d-inline-block">
                    <ul>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="pg-num <?= $i === $currentPage ? 'active' : '' ?>">
                                <a href="?page=<?= $i ?>" class="mb-2 pg-btn pg-num-link"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </main>
</div>

<?php include 'activity/footer.php'; ?>