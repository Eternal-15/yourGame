<?php
// 1. Start session to access session variables
include("system/session.php");
// 2. Start connection with init.php(main files)
include("system/init.php");

// Initialize session flags if not set
if (!isset($_SESSION['allow_signup'])) {
    $_SESSION['allow_signup'] = true;
}
if (!isset($_SESSION['allow_login'])) {
    $_SESSION['allow_login'] = true;
}

// Check if the form has been submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If 'name' is set in $_POST, assign its value to $name else ""
    $name = isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : "";
    $gender = isset($_POST["gender"]) ? htmlspecialchars($_POST["gender"]) : "";
    $email = isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : "";
    $contact = isset($_POST["contact"]) ? htmlspecialchars($_POST["contact"]) : "";
    $password = isset($_POST["password"]) ? htmlspecialchars($_POST["password"]) : "";
    $password_confirmation = isset($_POST["confirm_password"]) ? htmlspecialchars($_POST["confirm_password"]) : "";
}

// Retrieve messages from session
$loginError = $_SESSION['login_error'] ?? '';
$signUpError = $_SESSION['signup_error'] ?? '';
$loginSuccess = $_SESSION['login_success'] ?? '';
$signUpSuccess = $_SESSION['signup_success'] ?? '';

$preservedSignup = $_SESSION['signup_data'] ?? [];
$preservedLogin = $_SESSION['login_data'] ?? [];


// Clear messages after retrieving
unset($_SESSION['login_error']);
unset($_SESSION['signup_error']);
unset($_SESSION['login_success']);
unset($_SESSION['signup_success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
    <link rel="stylesheet" href="/yourGame/css/design.css">
</head>

<body>
    <div class="left-image">
        <img src="/yourGame/img/OnePiece.jpg" alt="Banner">
    </div>

    <div class="right-side">
        <div class="toggle-button">
            <button onclick="showForm('signup')"> Sign Up</button>
            <button onclick="showForm('login')"> Login </button>
        </div>

        <div class="displayMessage">

            <!-- Logout success message -->
            <?php if (isset($_GET['logout_success'])): ?>
                <div class="logoutSuccess">You have been successfully logged out.</div>
            <?php endif; ?>

            <!-- Logout error message -->
            <?php if (isset($_GET['logout_error'])): ?>
                <div class="logoutError">There was an error during logout. Please try again.</div>
            <?php endif; ?>

            <!-- To display success message  -->
            <?php if ($loginSuccess): ?>
                <div class="loginSuccess"> <?php echo htmlspecialchars($loginSuccess); ?> </div>
            <?php endif; ?>

            <!-- To display error message if any  -->
            <?php if ($loginError): ?>
                <div class="loginError"> <?php echo htmlspecialchars($loginError); ?> </div>
            <?php endif; ?>

            <!-- To display error message if any  -->
            <?php if (isset($_GET['logout_error'])): ?>
                <div class="displayError" id="logout-msg"> <?php echo htmlspecialchars($logOutError); ?> </div>
            <?php endif; ?>


            <!-- To display success message  -->
            <?php if ($signUpSuccess): ?>
                <div class="signUpSuccess"> <?php echo htmlspecialchars($signUpSuccess); ?> </div>
            <?php endif; ?>

            <!-- To display failure message  -->
            <?php if ($signUpError): ?>
                <div class="signUpError"> <?php echo htmlspecialchars($signUpError); ?> </div>
            <?php endif; ?>
        </div>

        <!-- ##################################  Sign Form  #################################### -->
        <div id="signup-form">
            <p>Sign Up Form</p>

            <!-- Begin of Signup Form -->
            <form action="user/signing.php" method="POST" enctype="multipart/form-data">

                <!-- Name field -->
                <label for="name">Name: </label>
                <input type="text" id="name" name="name"
                    value="<?php echo htmlspecialchars($preservedSignup['name'] ?? ''); ?>">
                <br>

                <!-- Gender field -->
                <div class="radioOption">
                    <label>Gender:</label><br>
                    <input type="radio" name="gender" value="male" <?php echo (($preservedSignup['gender'] ?? '') === 'male') ? 'checked' : ''; ?>> Male<br>
                    <input type="radio" name="gender" value="female" <?php echo (($preservedSignup['gender'] ?? '') === 'female') ? 'checked' : ''; ?>> Female<br>
                    <input type="radio" name="gender" value="others" <?php echo (($preservedSignup['gender'] ?? '') === 'others') ? 'checked' : ''; ?>> Other<br><br>
                </div>

                <!-- *Email field -->
                <label for="email">Email: </label>
                <input type="email" id="email" name="email"
                    value="<?php echo htmlspecialchars($preservedSignup['email'] ?? ''); ?>"> <br> <br>

                <!-- Contact field -->
                <label for="contact">Contact: </label>
                <input type="contact" id="contact" name="contact"
                    value="<?php echo htmlspecialchars($preservedSignup['contact'] ?? ''); ?>"> <br> <br>

                <!-- Password field -->
                <label for="password">Password: </label>
                <input type="password" name="password" id="password"> <br> <br>

                <!-- Confirm password field -->
                <label for="confirm-password">Confirm Password: </label>
                <input type="password" name="confirm_password" id="confirm_password"> <br> <br>

                <!-- ==== ADD PROFILE PICTURE UPLOAD HERE ==== -->
                <label for="avatar">Avatar: </label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
                <small>(Optional: JPG, PNG, GIF - Max 2MB)</small> <br><br>

                <label for="character">Character: </label>
                <select id="character" name="character" required>
                    <option value="">Select Character</option>
                    <option value="warrior" <?php echo (($preservedSignup['character'] ?? '') === 'warrior') ? 'selected' : ''; ?>>Warrior</option>
                    <option value="mage" <?php echo (($preservedSignup['character'] ?? '') === 'mage') ? 'selected' : ''; ?>>Mage</option>
                    <option value="archer" <?php echo (($preservedSignup['character'] ?? '') === 'archer') ? 'selected' : ''; ?>>Archer</option>
                    <option value="fighter" <?php echo (($preservedSignup['character'] ?? '') === 'fighter') ? 'selected' : ''; ?>>Fighter</option>
                </select><br><br>

                <div class="checkbox-container">
                    <input type="checkbox" name="acceptAgreement" value="acceptAgreement" required id="acceptAgreement">
                    <label for="acceptAgreement">I agree to accept the agreement.</label>
                </div> <br>
                <!-- Submit button -->
                <input type="submit" value="Sign Up">
            </form>
        </div>

        <!-- ################################## Login Form  #################################### -->
        <div id="login-form" class="hidden">
            <p>Login Form</p>

            <form action="user/login.php" method="POST">

                <!-- Email field -->
                <label for="loginEmail">Email: </label>
                <input type="email" id="loginEmail" name="loginEmail"
                    value="<?php echo htmlspecialchars($preservedLogin['loginEmail'] ?? ''); ?>"> <br> <br>

                <!-- Password field -->
                <label for="loginPassword">Password: </label>
                <input type="password" name="loginPassword" id="loginPassword"> <br> <br>

                <div class="checkbox-container">
                    <input type="checkbox" name="acceptAgreement" value="acceptAgreement" required id="acceptAgreement">
                    <label for="acceptAgreement">I agree to accept the agreement.</label>
                </div> <br><br>

                <!-- Submit button -->
                <input type="submit" value="Login">
            </form>
        </div>
    </div>

    <script>
        function showForm(formType) {
            // Hide both forms
            document.getElementById('signup-form').classList.add('hidden');
            document.getElementById('login-form').classList.add('hidden');
            //Show selected forms
            document.getElementById(formType + '-form').classList.remove('hidden');
        }
    </script>
</body>

</html>