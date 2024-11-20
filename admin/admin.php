<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../activity/header.php';
?>

<section class="forms-section">
    <div class="forms">
        <!-- Add User Form -->
        <div class="form-wrapper is-active">
            <button type="button" class="switcher switcher-add-user">Add User <span class="underline"></span></button>
            <form class="form form-add-user" action="add_user.php" method="POST">
                <fieldset>
                    <legend>Enter user information to add a new user.</legend>
                    <div class="input-block">
                        <label for="user-name">Username</label>
                        <input id="user-name" name="username" type="text" required>
                    </div>
                    <div class="input-block">
                        <label for="user-email">Email</label>
                        <input id="user-email" name="email" type="email" required>
                    </div>
                    <div class="input-block">
                        <label for="user-role">Role</label>
                        <select id="user-role" name="role" required>
                            <option value="Admin">Admin</option>
                            <option value="Member">Member</option>
                            <option value="Professional">Professional</option>
                            <option value="Influencer">Influencer</option>
                        </select>
                    </div>
                </fieldset>
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
                        <input id="search-user" name="search" type="text">
                    </div>
                    <button type="submit" class="btn-view-users">Search</button>
                </fieldset>
            </form>
        </div>

        <!-- Settings Form -->
        <div class="form-wrapper">
            <button type="button" class="switcher switcher-settings">Settings <span class="underline"></span></button>
            <form class="form form-settings" action="update_settings.php" method="POST">
                <fieldset>
                    <legend>Update system settings.</legend>
                    <div class="input-block">
                        <label for="site-title">Site Title</label>
                        <input id="site-title" name="site_title" type="text" required>
                    </div>
                    <div class="input-block">
                        <label for="site-description">Site Description</label>
                        <input id="site-description" name="site_description" type="text" required>
                    </div>
                </fieldset>
                <button type="submit" class="btn-settings">Save Settings</button>
            </form>
        </div>
    </div>
</section>

<?php include '../activity/footer.php'; ?>