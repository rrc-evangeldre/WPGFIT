<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../activity/header.php';

// Redirect non-admin users to another page
if (!isset($_SESSION['role']) || !in_array('Admin', (array)$_SESSION['role'])) {
    $_SESSION['error_message'] = "You do not have permission to access the admin page.";
    header("Location: ../index.php");
    exit();
}
?>

<!-- Message Container -->
<div class="message-container">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['register_error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['register_error']; ?></div>
        <?php unset($_SESSION['register_error']); ?>
    <?php endif; ?>
</div>

<section class="forms-section">
    <div class="forms">
        <!-- Add User Form -->
        <div class="form-wrapper is-active">
            <button type="button" class="switcher switcher-add-user">Add User <span class="underline"></span></button>
            <form class="form form-add-user" action="../logins/register.php" method="POST">
                <fieldset>
                    <legend>Enter user information to add a new user.</legend>
                    <div class="admin-input-block">
                        <label for="add-username">Username</label>
                        <input id="add-username" name="username" type="text" autocomplete="off" required>
                    </div>
                    <div class="admin-input-block">
                        <label for="add-email">Email</label>
                        <input id="add-email" name="email" type="email" autocomplete="off" required>
                    </div>
                    <div class="admin-input-block">
                        <label for="add-password">Password</label>
                        <input id="add-password" name="password" type="password" autocomplete="new-password" required>
                    </div>
                    <div class="admin-input-block">
                        <label for="confirm-password">Confirm Password</label>
                        <input id="confirm-password" name="password_confirm" type="password" autocomplete="new-password" required>
                    </div>
                    <div class="admin-input-block">
                        <label for="add-roles">Roles</label>
                        <div class="checkbox-group">
                            <div>
                                <label>
                                    <input type="checkbox" name="roles[]" value="Admin"> Admin
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" name="roles[]" value="Professional"> Professional
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" name="roles[]" value="Influencer"> Influencer
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <input type="hidden" name="is_admin_action" value="true">
                <button type="submit" class="btn-add-user">Add User</button>
            </form>
        </div>

        <!-- View Users Form -->
        <div class="form-wrapper">
            <button type="button" class="switcher switcher-view-users">View Users <span class="underline"></span></button>
            <form class="form form-view-users" action="view_users.php" method="POST">
                <fieldset>
                    <legend>Manage existing users.</legend>
                    <div class="input-block">
                        <label for="search-user">Search User</label>
                        <input id="search-user" name="search" type="text" autocomplete="off">
                    </div>
                    <button type="submit" class="btn-view-users">Search</button>
                </fieldset>
            </form>
        </div>
    </div>
</section>

<?php include '../activity/footer.php'; ?>