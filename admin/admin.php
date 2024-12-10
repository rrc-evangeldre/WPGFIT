<?php
/*******w******** 
 
    Name: Raphael Evangelista
    Date: December 9, 2024
    Description: This contains the admin interface for managing users.
                 Admins can add new users and view/manage existing users, 
                 including editing their roles and deleting them. 
    
****************/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../activity/header.php';
include '../activity/db_connect.php';

// Redirect non-admin users to home page with an error message
if (!isset($_SESSION['role']) || !in_array('Admin', (array)$_SESSION['role'])) {
    header("Location: ../navlinks/index.php");
    exit();
}

// Fetch all users in alphabetical order
$users = [];
try {
    $stmt = $db->prepare("SELECT UserID, Username, Role FROM users ORDER BY Username ASC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['register_error'] = "Failed to fetch users: " . $e->getMessage();
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

        <!-- View/Manage Users Form -->
        <div class="form-wrapper">
            <button type="button" class="switcher switcher-view-users">View Users <span class="underline"></span></button>
            <form class="form form-view-users" action="manage_users.php" method="POST">
                <fieldset>
                    <legend>Select a user to edit or delete.</legend>
                    <div class="input-block">
                    <label for="select-user">Select User</label>
                    <select id="select-user" name="user_id" required onchange="handleUserSelection()">
                        <option value="">-- Select a User --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['UserID']; ?>" 
                                    data-roles="<?php echo htmlspecialchars($user['Role']); ?>" 
                                    data-is-admin="<?php echo strpos($user['Role'], 'Admin') !== false ? 'true' : 'false'; ?>">
                                <?php echo htmlspecialchars($user['Username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                    <div class="admin-input-block">
                        <label for="edit-roles">Edit Roles</label>
                        <div class="checkbox-group">
                            <div>
                                <label>
                                    <input type="checkbox" id="role-admin" name="roles[]" value="Admin"> Admin
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" id="role-professional" name="roles[]" value="Professional"> Professional
                                </label>
                            </div>
                            <div>
                                <label>
                                    <input type="checkbox" id="role-influencer" name="roles[]" value="Influencer"> Influencer
                                </label>
                            </div>
                            <div>
                                <label>
                                <input type="checkbox" id="role-member" name="roles[]" value="Member" checked disabled> Member (Default)
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="action" value="edit" class="btn-edit-user">Edit Roles</button>
                    <button type="submit" name="action" value="delete" class="btn-delete-user">Delete User</button>
                </fieldset>
            </form>
        </div>
    </div>
</section>
<?php include '../activity/footer.php'; ?>