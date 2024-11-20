<?php
/*******w******** 
        
    Name: Raphael Evangelista
    Date: November 11, 2024
    Description: This is the header template that manages session start, page titles, and active state for navigation.

****************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Set page title and active state
switch ($current_page) {
    case '../logins/login.php':
        $page_title = "WPG FIT | Login";
        $active_page = 'login';
        break;
    case '../navlinks/post.php':
        $page_title = "WPG FIT | Post";
        $active_page = 'post';
        break;
    case '../navlinks/friends_groups.php':
        $page_title = "WPG FIT | Friends and Groups";
        $active_page = 'friends_groups';
        break;
    case '../navlinks/leaderboard.php':
        $page_title = "WPG FIT | Leaderboard";
        $active_page = 'leaderboard';
        break;
    case '../navlinks/profile.php':
        $page_title = "WPG FIT | Profile";
        $active_page = 'profile';
        break;
    default:
        $page_title = "WPG FIT | Blog Home";
        $active_page = 'home';
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/11517d5e6e.js" crossorigin="anonymous"></script>
    <link rel="icon" href="../img/wpgfit.png" type="image/png">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/mainstyle.css" rel="stylesheet">
</head>
<body>
    <header class="header" id="header">
        <div class="side-toggler">
            <button class="navbar-toggler" type="button" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="site-logo">
                <img src="../img/wpgfit.png" alt="Image" class="img-fluid">                            
            </div>
            <nav class="side-nav" id="side-nav">            
                <ul>
                    <li class="side-nav-item <?php if($active_page == 'home') echo 'active'; ?>">
                        <a href="../navlinks/index.php" class="side-nav-link">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </li>
                    <li class="side-nav-item <?php if($active_page == 'post') echo 'active'; ?>">
                        <a href="../navlinks/post.php" class="side-nav-link">
                            <i class="fas fa-pen"></i> Post
                        </a>
                    </li>
                    <li class="side-nav-item <?php if($active_page == 'friends_groups') echo 'active'; ?>">
                        <a href="../navlinks/friends_groups.php" class="side-nav-link">
                            <i class="fas fa-users"></i> Friends and Groups
                        </a>
                    </li>
                    <li class="side-nav-item <?php if($active_page == 'leaderboard') echo 'active'; ?>">
                        <a href="../navlinks/leaderboard.php" class="side-nav-link">
                            <i class="fa-brands fa-flipboard fa-rotate-180"></i> Leaderboard
                        </a>
                    </li>
                    
                    <?php if(isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true): ?>
                        <!-- My Account and Log Out links for logged-in users -->
                        <li class="side-nav-item <?php if($active_page == 'profile') echo 'active'; ?>">
                            <a href="../navlinks/profile.php" class="side-nav-link">
                                <i class="fas fa-user"></i> My Account
                            </a>
                        </li>
                        <li class="side-nav-item">
                            <a href="../logins/logout.php" class="side-nav-link">
                                <i class="fas fa-sign-out-alt"></i> Log Out
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Sign In link for guests -->
                        <li class="side-nav-item <?php if($active_page == 'login') echo 'active'; ?>">
                            <a href="../logins/login.php" class="side-nav-link">
                                <i class="fa fa-sign-in"></i> Sign In
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
</body>
<!-- Popper.js and Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>

</html>
