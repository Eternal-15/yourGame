<?php
    // Normalize timezone across all scripts
    date_default_timezone_set('Asia/Kathmandu');

    // 1. Start session to access session variables
    include("../system/session.php"); 
    // // Load utility functions, including error logging
    // include_once("../system/utils.php");

    // 2. Block direct access via browser or GET request
    if (!isset($_SESSION["allow_confirm"])) {
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
        // Note: Do not clear the session flag yet to allow retry if needed
        // Terminate script execution
        exit;
    }

    // 3. Retrieve messages from session variables

    // Retrieve success message from signup
    $signupSuccess = $_SESSION['signup_success'] ?? '';
    // Clear the message after use to prevent repetition
    unset($_SESSION['signup_success']);

    // Retrieve error message from previous confirmation attempt
    $confirmError = $_SESSION['confirm_error'] ?? '';
    // Clear the error message after use
    unset($_SESSION['confirm_error']);

    // 4. Handle form submission: Proceed only if the form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Sanitize and validate input
            $email = strtolower(trim($_POST['email'] ?? ''));
            $code = trim($_POST['code'] ?? '');
            $action = $_POST['action'] ?? 'confirm';

            if ($action === 'resend') {
                if (!$email) {
                    throw new Exception("Email is required to resend the code.");
                }

                // Step 1: Find user ID from users.txt that matches the email address
                $users = file("files/users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $user = null;
                foreach ($users as $line) {
                    $parts = explode("|", $line);
                    if (strtolower($parts[3]) === $email) {
                        $userId = $parts[0];
                        break;
                    }
                }
                if (!$email) {
                    throw new Exception("No account found with that email.");
                }

                // Step 2: Throttle resend attempts
                $codes = file("files/confirm_codes.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($codes as $entry) {
                    list($storedEmail, $storedCode, $createdAt) = explode("|", $entry);
                    if (strtolower($storedEmail) === $email && strtotime($createdAt) > time() - 60) {
                        throw new Exception("Please wait a minute before requesting another code.");
                    }
                }

                // Step 3: Generate new code
                $newCode = rand(100000, 999999);
                $timestamp = date("Y-m-d H:i:s");

                // Step 4: Check if code already exists then update new code and delete old code
                $filtered = [];                                     // Create an empty array to hold updated entries
                foreach ($codes as $entry) {
                    $parts = explode("|", $entry);                  // Split each line into parts
                    $storedEmail = strtolower(trim($parts[0]));     // Extract and normalize the email
                    // Keep the entry only if it doesn't match the current user's email
                    if ($storedEmail !== $email) {
                        $filtered[] = $entry;
                    }
                }
                // Add the new confirmation code for the current user
                $newEntry = "$email|$newCode|$timestamp";
                $filtered[] = $newEntry;
                // Save the updated list back to the file
                $updatedContent = implode("\n", $filtered) . "\n";
                file_put_contents("../files/confirm_codes.txt", $updatedContent);
                
                // Step 5: (Optional) Send email
                // mail($email, "Your New Confirmation Code", "Your new code is: $newCode");

                // Step 6: Redirect with message
                $_SESSION['confirm_error'] = "A new confirmation code has been sent to your email.";

                header("Location: confirm.php");
                exit;

            } elseif ($action === 'confirm') {
                if (!$email || !$code) {
                    throw new Exception("Both email and confirmation code are required.");
                }

                // Step 1: Lookup userId for the entered email address in users.txt
                $users = file("../files/users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $userId = null;


                $usersFile = "../files/users.txt";
                if (!file_exists($usersFile)) {
                    throw new Exception("User database not found. Please try signing up again.");
                }
                $users = file($usersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($users === false) {
                    throw new Exception("Cannot read user database.");
                }


                foreach ($users as $line) {
                    $parts = explode("|", $line);
                    if (strtolower($parts[3]) === $email) {
                        $userId = $parts[0];
                        break;
                    }
                }
                if (!$userId) {
                    throw new Exception("No account found with that email.");
                }

                // Step 2: Validate confirmation code from confirm_codes.txt
                $codesFile = "../files/confirm_codes.txt";
                if (!file_exists($codesFile)) {
                    throw new Exception("No confirmation codes found. Please request a new code.");
                }
                $codes = file($codesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($codes === false) {
                    throw new Exception("Cannot read confirmation codes.");
                }


                $codes = file("../files/confirm_codes.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $valid = false;
                // Check each line for a matching email and code
                foreach ($codes as $entry) {
                    list($storedEmail, $storedCode) = explode("|", $entry);
                    if (strtolower(trim($storedEmail)) === $email && trim($storedCode) === $code) {
                        $valid = true;
                        break;
                    }
                }
                // If no match found, throw error
                if (!$valid) {
                    throw new Exception("Invalid email or confirmation code.");
                }

                // Step 3: Update confirmation status in users.txt
                $updatedUsers = [];
                $found = false;
                // Loop through users and update confirmation flag
                foreach ($users as $line) {
                    $parts = explode("|", $line);
                    // Match email (case-insensitive)
                    if (isset($parts[3]) && strtolower($parts[3]) === $email) {
                        $parts[5] = "true"; // Mark account as confirmed
                        $found = true;
                    }
                    $updatedUsers[] = implode("|", $parts);
                }
                // If user not found, throw error
                if (!$found) {
                    throw new Exception("User record not found in users.txt.");
                }
                // Save updated user list back to the file
                file_put_contents("../files/users.txt", implode("\n", $updatedUsers) . "\n");

                // Step 4: Remove confirmed code from confirm_codes.txt
                $remainingCodes = [];                               // Create an empty array to hold valid entries
                foreach ($codes as $entry) {
                    $parts = explode("|", $entry);                  // Split each line into parts
                    $storedEmail = strtolower(trim($parts[0]));     // Get the email from the line
                    // Keep the entry only if it doesn't match the current user's email
                    if ($storedEmail !== $email) {
                        $remainingCodes[] = $entry;
                    }
                }
                // Save the filtered list back to the file
                $updatedContent = implode("\n", $remainingCodes) . "\n";
                file_put_contents("../files/confirm_codes.txt", $updatedContent);

               
                // Step 5: Redirect to login with success message
                $_SESSION['login_success'] = "Account confirmed successfully! You can now log in.";
                // Note: Clear confirmation flag (only when confirmation code matches) to prevent reuse
                unset($_SESSION['allow_confirm']);
                // Redirect to login page
                header("Location: /yourGame/index.php");
                exit;
            } else {
                throw new Exception("Unknown action.");
            }

        } catch (Exception $e) {
            // Log the error message to error_log.txt for debugging
            // logError("Confirmation error: " .$e->getMessage());
            // Store the error message to display in confirm.php
            $_SESSION['confirm_error'] = $e->getMessage();
            // Redirect back to confirm.php to render the message
            header("Location: /yourGame/user/confirm.php");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Confirm Account</title>
  <!-- External CSS for styling -->
  <link rel="stylesheet" href="/yourGame/css/signup.css">
</head>
<body>
    <!-- Confirmation form -->
    <div class="container">
        <div class="form-section">
            <h2>Confirm Your Account</h2>

            <!-- Display success message from signup -->
            <?php if ($signupSuccess): ?>
                <div class="success-message"><?php echo htmlspecialchars($signupSuccess); ?></div>
            <?php endif; ?>

            <!-- Display error message from failed confirmation -->
            <?php if ($confirmError): ?>
                <div class="error-message"><?php echo htmlspecialchars($confirmError); ?></div>
            <?php endif; ?>

            <form method="POST">
                <!-- Email input -->
                <label>Email: <input type="email" name="email" required></label>
                
                <!-- Confirmation code input -->
                <label>Confirmation Code: <input type="text" name="code"></label>
                
                <!-- Submit button -->
                <button type="submit" name="action" value="confirm">Confirm</button>
                <button type="submit" name="action" value="resend">Resend Code</button>
            </form>
        </div>
    </div>
</body>
</html>