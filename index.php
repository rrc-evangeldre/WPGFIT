<?php 
/*******w******** 
        
    Name: Raphael Evangelista
    Date: November 12, 2024
    Description: This is the main page of the site where posts are displayed.

****************/

    session_start(); 

    // Check if a login success message is set in the session and display it
    if (isset($_SESSION['login_success'])) {
        echo "<script>alert('" . $_SESSION['login_success'] . "');</script>";
        unset($_SESSION['login_success']); // Clear the message after displaying
    }

    include 'activity/header.php'; 
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
            <!-- Each post is in a Bootstrap column for responsive design -->
            <article class="col-12 col-md-6 post">
                <hr class="post-separator">
                <img src="img/gym1.jpg" alt="Image" class="img-fluid">
                <span class="position-absolute new-badge">New</span>
                <h2 class="post-title color-primary post-title-size">Template for Post 1</h2>
                <p class="post-title">
                    Post will be created soon. *Also edit pic sizes*
                </p>
                <div class="d-flex justify-content-between info-margin">
                    <span class="color-primary">Cardio . Advice</span>
                    <span class="color-primary">June 8, 2024</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>12 comments</span>
                    <span>by dwhitewalkers</span>
                </div>
            </article>

            <article class="col-12 col-md-6 post">
                <hr class="post-separator">
                <img src="img/gym2.jpg" alt="Image" class="img-fluid">
                <h2 class="post-title color-primary post-title-size">Template for Post 2</h2>
                <p class="post-title">
                    Post will be created soon.
                </p>
                <div class="d-flex justify-content-between info-margin">
                    <span class="color-primary">Nutrition . Strength Training</span>
                    <span class="color-primary">May 1, 2024</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>48 comments</span>
                    <span>by khaldrogo</span>
                </div>
            </article>

            <article class="col-12 col-md-6 post">
                <hr class="post-separator">
                <img src="img/gym2.jpg" alt="Image" class="img-fluid">                            
                <span class="position-absolute new-badge">New</span>
                <h2 class="post-title color-primary post-title-size">Template for Post 2</h2>
                <p class="post-title">
                    Post will be created soon.
                </p>
                <div class="d-flex justify-content-between info-margin">
                    <span class="color-primary">Nutrition . Strength Training</span>
                    <span class="color-primary">May 1, 2024</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>48 comments</span>
                    <span>by khaldrogo</span>
                </div>
            </article>

            <article class="col-12 col-md-6 post">
                <hr class="post-separator">
                <img src="img/gym3.jpg" alt="Image" class="img-fluid">
                <h2 class="post-title color-primary post-title-size">Template for Post 3</h2>
                <p class="post-title">
                    Post will be created soon.
                </p>
                <div class="d-flex justify-content-between info-margin">
                    <span class="color-primary">Advice . Nutrition</span>
                    <span class="color-primary">April 11, 2024</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>21 comments</span>
                    <span>by jonsnow</span>
                </div>
            </article>

            <article class="col-12 col-md-6 post">
                <hr class="post-separator">
                <img src="img/gym4.jpg" alt="Image" class="img-fluid">
                <h2 class="post-title color-primary post-title-size">Template for Post 4</h2>
                <p class="post-title">
                    Post will be created soon.
                <div class="d-flex justify-content-between info-margin">
                    <span class="color-primary">Cardio</span>
                    <span class="color-primary">March 4, 2024</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>72 comments</span>
                    <span>by samwelltarly</span>
                </div>
            </article>
        </div>

<!-- Pagination -->
<div class="row prev-next-margin">
    <div class="prev-next-wrapper">
        <a href="#" class="mb-2 pg-btn pg-btn-primary prev-next disabled prev-next-gap">Prev</a>
        <a href="#" class="mb-2 pg-btn pg-btn-primary prev-next">Next</a>
    </div>
    <div class="page-wrapper">
        <span class="d-inline-block mr-3">Page</span>
        <nav class="paging-nav d-inline-block">
            <ul>
                <li class="pg-num active"><a href="#" class="mb-2 pg-btn pg-num-link">1</a></li>
                <li class="pg-num"><a href="#" class="mb-2 pg-btn pg-num-link">2</a></li>
                <li class="pg-num"><a href="#" class="mb-2 pg-btn pg-num-link">3</a></li>
                <li class="pg-num"><a href="#" class="mb-2 pg-btn pg-num-link">4</a></li>
            </ul>
        </nav>
    </div>
</div>

</main>
</div>

<?php include 'activity/footer.php'; ?>
