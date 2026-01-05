<?php
    // 1. Start session to access session variables
    include("../system/session.php"); 
    // Load utility functions from utils.php to record all error messages
    // include_once("../system/utils.php");

    // 2. Block direct access via browser or GET request
    if (!isset($_SESSION['allow_logout']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
        // Display a forbidden access message with image and link
        echo '
            <div style="text-align: center;">
                <h1>Forbidden Access.</h1>
                <br>    
                <img src="/yourGame/img/forbidden.jpg" alt="Forbidden Access" style="max-width: 300px; margin-bottom: 10px;">
                <br> <br>
                <a href="/yourGame/index.php">Go to Home Page</a>
            </div>
        ';
        // Clear the session flag to prevent reuse
        unset($_SESSION['allow_logout']);
        // Terminate script execution
        exit;
    }

    // Handle logout: Proceed only if the form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Step 1: Remove token from token.txt if cookie present
            if (isset($_COOKIE['remember_token'])) {
                $token = trim($_COOKIE['remember_token']);
                $tokenFile = __DIR__ . "/files/tokens.txt";
                $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $updated = [];

                foreach ($lines as $line) {
                    $parts = explode('|', $line);
                    if (count($parts) < 3) continue;

                    // Only keep tokens that don't match the current cookie
                    if (trim($parts[1]) !== $token) {
                        $updated[] = $line;
                    }
                }

                // Overwrite token file with valid entries
                file_put_contents($tokenFile, implode("\n", $updated));

                // Expire the cookie
                setcookie('remember_token', '', time() - 3600, "/");
            }

            // Step 2: Remove all tokens for this user (logout from all devices)
            if (isset($_SESSION['email'])) {
                $email = strtolower(trim($_SESSION['email']));
                $tokenFile = __DIR__ . "/files/tokens.txt";
                $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $remainingTokens = [];

                foreach ($lines as $line) {
                    $parts = explode('|', $line);
                    if (isset($parts[0]) && strtolower(trim($parts[0])) !== $email) {
                        $remainingTokens[] = $line;
                    }
                }

                file_put_contents($tokenFile, implode("\n", $remainingTokens) . "\n");
            }

            // Step 3: Expire session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }


            // Step 4: Clear session variables
            unset($_SESSION['allow_logout']);
            session_unset();
            session_destroy();        
            
            // Step 5: Redirect to login screen setting the logout sucesss        
            header("Location: /yourGame/index.php?logout_success=1");
            exit;
        } catch (Exception $e) {
            $logError("Logout error: " . $e->getMessage());
            header("Location: /yourGame/index.php?logout_error=1");
            exit;
        }
    }
?>