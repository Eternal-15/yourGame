<?php
    // 1. Start session to access session variables
    include("../system/session.php"); 
    // Load utility functions from utils.php to record all error messages
    // include_once("../system/utils.php");

    // 2. Block direct access via browser or GET request
    if (!isset($_SESSION['allow_login']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
        // Display a forbidden access message with image and link
        echo '
            <div style="text-align: center;">
                <h1>Forbidden Access.</h1>
                <br>    
                <img src="/yourGame/img/forbidden.jpg" alt="Forbidden Access" style="max-width: 300px; margin-bottom: 10px;">
                <br> <br>
                <a href="/yourGame/index.php">Go to Home Page</a>
            </div>s
        ';
        // Clear the session flag to prevent reuse
        unset($_SESSION['allow_login']);
        // Terminate script execution
        exit;
    }
    
    // 3. Handle login form: Proceed only if the form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
    // Retrieve and sanitize form inputs
    $email = strtolower(trim($_POST['loginEmail'] ?? '')); // ← TEMPORARILY use 'Email' to match your form
    $password = $_POST['loginPassword'] ?? '';
    $remember = $_POST['remember'] ?? '';

    $_SESSION['login_data'] = ['email' => $email];

    // Ensure both email and password are provided
    if (!$email || !$password) {
        throw new Exception("Both email and password are required.");
    }

    // Step 1: Load user data from users.txt
    $file_path = "../files/users.txt";
    
    $users = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $found = false;

    // Loop through each user line to find matching email
    foreach ($users as $index => $line) {
        $parts = explode("|", $line);
        // Check if email matches (case-insensitive)
        if (isset($parts[3]) && strtolower(trim($parts[3])) === $email) {
            $found = true;

            // Step 3: Verify password using password_hash
            if (!password_verify($password, $parts[4])) {
                throw new Exception("Incorrect password.");
            }

            // Step 4: Check confirmation status
            if ($parts[5] !== "true") {
                $_SESSION['allow_confirm'] = true;
                $_SESSION['confirm_error'] = "Check your email for confirmation code or resend code again.";
                header("Location: /yourGame/user/confirm.php");
                exit;
            }

            // Step 5: Set session variables
            $_SESSION['user'] = $parts[0]; // player_id
            $_SESSION['allow_logout'] = true;

            // Step 6: Insert session state to player_state.txt
            $stateLine = "{$parts[0]}|{$parts[6]}|{$parts[7]}\n";
            if (!file_exists("../files/player_state.txt")) {
                file_put_contents("../files/player_state.txt", "");
            }
            file_put_contents("../files/player_state.txt", $stateLine, FILE_APPEND);

            // Step 7: Handle Remember Me token
            $tokenFile = "../files/tokens.txt";
            if ($remember === 'yes') {
                $token = bin2hex(random_bytes(32)); // 64-char token
                $expiryUnix = time() + (86400 * 7); // Unix timestamp for cookie
                $expirySQL  = date('Y-m-d H:i:s', $expiryUnix); // SQL DATETIME for DB

                setcookie('remember_token', $token, $expiryUnix, "/");
                // Remove old tokens for this email
                $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $filtered = array_filter($lines, fn($l) => strpos($l, "$email|") !== 0);
                file_put_contents($tokenFile, implode("\n", $filtered));

                // Save new token
                file_put_contents($tokenFile, "$email|$token|$expiryDate\n", FILE_APPEND);
            } else {
                setcookie('remember_token', '', time() - 3600, "/");
            }

            // Step 8: Finalise Login 

            // Optional: Set success message for display on portal
            $_SESSION['login_success'] = "Login successful!";

            unset($_SESSION['login_data']);
            // Clear login session flag before redirection           
            unset($_SESSION['allow_login']);
            // Set logout flag to allow access to logout module
            $_SESSION['allow_logout'] = true;

            // Redirect to portal on success
            header("Location: /yourGame/realms/portal.php");
            exit;
        }
    }

    if (!$found) {
        echo "<h2 style='color: red;'>NO USER FOUND WITH EMAIL: '" . htmlspecialchars($email) . "'</h2>";
        throw new Exception("No account found for this email. Please signup first!!");
    }
} 
        catch (Exception $e) {
            // Log the error message to error_log.txt for debugging
            // logError($e->getMessage());
        
            // Store the error message to display on index.php
            $_SESSION['login_error'] = $e->getMessage();

            // Clear session variables to prevent partial login
            unset($_SESSION['user']);
            unset($_SESSION['allow_logout']);
            
            // Redirect back to index.php if any exception catched by the system
            echo "Error Occurred: " . $e->getMessage();
        }
    }
?>