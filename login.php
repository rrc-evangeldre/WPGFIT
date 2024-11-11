<?php 
include 'activity/header.php';

if (isset($_SESSION['login_error'])) {
    echo "<script>alert('" . $_SESSION['login_error'] . "');</script>";
    unset($_SESSION['login_error']); // Clear the error after displaying
}
?>
<section class="forms-section">
        <div class="forms">
            <!-- Login Form -->
            <div class="form-wrapper is-active">
                <button type="button" class="switcher switcher-login">Login <span class="underline"></span></button>
                <form class="form form-login" action="signin.php" method="POST">
                <fieldset>
                        <legend>Enter your email and password for login.</legend>
                        <div class="input-block">
                            <label for="login-username">Username</label>
                            <input id="login-username" name="username" type="text" required>
                        </div>
                        <div class="input-block">
                            <label for="login-password">Password</label>
                            <input id="login-password" name="password" type="password" required>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn-login">Login</button>
                </form>
            </div>

            <!-- Registration Form -->
            <div class="form-wrapper">
                <button type="button" class="switcher switcher-signup">Sign Up <span class="underline"></span></button>
                <form class="form form-signup" action="register.php" method="POST">
                    <fieldset>
                        <legend>Enter your information on the fields to sign up.</legend>
                        <div class="input-block">
                            <label for="signup-email">E-mail</label>
                            <input id="signup-email" name="email" type="email" required>
                        </div>
                        <div class="input-block">
                            <label for="signup-email">Username</label>
                            <input id="signup-username" name="username" type="username" required>
                        </div>
                        <div class="input-block">
                            <label for="signup-password">Password</label>
                            <input id="signup-password" name="password" type="password" required>
                        </div>
                        <div class="input-block">
                            <label for="signup-password-confirm">Confirm password</label>
                            <input id="signup-password-confirm" name="password_confirm" type="password" required>
                        </div>
                        <div class="input-block">
                            <label for="member-type">Membership Type</label>
                            <div class="checkbox-group">
                                <div>
                                    <label>
                                        <input type="checkbox" name="membership" value="Professional"> Professional
                                    </label>
                                </div>
                                <div>
                                    <label>
                                        <input type="checkbox" name="membership" value="Influencer"> Influencer
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn-signup">Continue</button>
                </form>
            </div>
        </div>
</section>
    <script src="js/form.js"></script>