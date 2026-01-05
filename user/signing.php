<?php
    // Normalize timezone across all scripts
    date_default_timezone_set('Asia/Kathmandu');
    
    // 1. Start session to access session variables
    include("../system/session.php"); 

// ?Block direct access via browser or GET request
    if (!isset($_SESSION['allow_signup'])|| $_SERVER['REQUEST_METHOD'] != 'POST') {
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
        unset($_SESSION['allow_signup']); 
        // Terminate script execution
        exit;
    }
    function signUp() {
        // Check if the form has been submitted using POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // ?Initialize variables
        $userId = uniqid("user_");
        $name = trim($_POST["name"] ?? '');
        $email = strtolower(trim($_POST["email"] ?? ''));
        $gender = $_POST["gender"] ?? '';
        $contact = trim($_POST["contact"] ?? '');
        $password = $_POST["password"] ?? '';
        $confirm_password = $_POST["confirm_password"] ?? '';
        $agree = isset($_POST["acceptAgreement"]) ?? ' ';
        $avatar = $_FILES['avatar'] ?? null;
        $character = $_POST['character'] ?? '';

        // Preserve form data in session for error cases
        $_SESSION['signup_data'] = [
            'name' => $name,
            'email' => $email,
            'gender' => $gender,
            'contact' => $contact,
            'character' => $character,
        ];

        // Remove delimiters and line breaks to prevent file format corruption (e.g., if someone enters | in their name)
            $name = str_replace(["|", "\n", "\r"], "", $name);
            $gender = str_replace("|", "", $gender);
            $email = str_replace("|", "", $email);
            $character = str_replace("|", "", $character);


        // ?4. Basic Validation

        // Check for missing fields
        if (!$name || !$gender || !$email || !$password || !$confirm_password || !$contact || !$character || !$agree || !$avatar) {
            throw new Exception("All fields must be filled and confirmed.");
        }

        // Validate name format: only letters and spaces allowed
        if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            throw new Exception("Name must contain only letters and spaces.");
        }            

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format.");
        }

        // Check for duplicate email in users.txt
        $existingUsers = file("../files/users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($existingUsers as $userLine) {
            $parts = explode("|", $userLine);
            if (isset($parts[3]) && strtolower($parts[3]) === $email) {
                throw new Exception("Email already exists.");
            }
        }

        // Validate password match
        if ($password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }

        // Check password strength
        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }

        // Hash the password securely prior to saving to file
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Validate avatar upload type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $imageInfo = getimagesize($avatar['tmp_name']);
            if (!in_array($imageInfo['mime'], $allowedTypes)) {
                throw new Exception("Unsupported avatar format.");
            }

            // Check for upload errors
            if ($avatar['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Avatar upload failed.");
            }        
            //Function to resize avatar image to 320x320 and save as JPEG
            function resizeAvatar($sourcePath, $destinationPath, $newWidth = 320, $newHeight = 320) {
                $imageInfo = getimagesize($sourcePath);
                $mime = $imageInfo['mime'];

                switch ($mime) {
                    // We need to enable GD extension for imagecreatefromjpeg to work.
                    /* 
                        1. Open php.ini (usually in C:\xampp\php\php.ini)
                        2. Find this line: ;extension=gd
                        3. Remove the semicolon
                        4. Save the file and restart Apache from the XAMPP control panel
                    */
                    case 'image/jpeg':
                        $srcImage = imagecreatefromjpeg($sourcePath);
                        break;
                    case 'image/png':
                        $srcImage = imagecreatefrompng($sourcePath);
                        break;
                    case 'image/gif':
                        $srcImage = imagecreatefromgif($sourcePath);
                        break;
                    default:
                        throw new Exception("Unsupported image format.");
                }
                // Create a blank canvas and copy resized image onto it
                $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($srcImage), imagesy($srcImage));
                // Save resized image as JPEG
                imagejpeg($resizedImage, $destinationPath);
                // Free memory
                imagedestroy($srcImage);
                imagedestroy($resizedImage);
            }

            // Ensure the uploads directory exists and is writable
            $uploadDir = "uploads";
            $useDefaultAvatar = false;

            // Check for upload errors
            if ($avatar['error'] !== UPLOAD_ERR_OK) {
                // Use default avatar if upload fails
                $useDefaultAvatar = true;
            }

            if ($useDefaultAvatar) {
                $avatarPath = $uploadDir . "/default_avatar.jpg";
            } else {
                // Ensure the uploads directory exists and is writable
                if (!is_dir($uploadDir)) {
                    if (!mkdir($uploadDir, 0755, true)) {
                        throw new Exception("Failed to create uploads directory.");
                    }
                } elseif (!is_writable($uploadDir)) {
                    throw new Exception("Uploads directory is not writable.");
                }

                // Generate unique filename and path to uploads folder
                $avatarName = uniqid() . ".jpg";
                $avatarPath = $uploadDir . "/" . $avatarName;
                resizeAvatar($avatar['tmp_name'], $avatarPath);
            }

            // 5. Prepare and save user data to users.txt
            $line = "$userId|$name|$gender|$email|$hashedPassword|false|$character|$avatarName\n";
            
            // Create users.txt if it doesn't exist
            if (!file_exists("../files/users.txt")) {
                file_put_contents("../files/users.txt", ""); 
            }
        }
        // 5. Prepare and save user data to users.txt
            $line = "$userId|$name|$gender|$email|$hashedPassword|false|$character|$avatarName\n";
            
            // Create users.txt if it doesn't exist
            if (!file_exists("../files/users.txt")) {
                file_put_contents("../files/users.txt", ""); 
            }
            // Append user data to users.txt
            file_put_contents("../files/users.txt", $line, FILE_APPEND);


        // 6. Generate a random 6-digit confirmation code
        $code = rand(100000, 999999);

        // Save confirmation code, email and timestamp to confirm_codes.txt
        $timestamp = date("Y-m-d H:i:s");
        $confirmLine = "$email|$code|$timestamp\n";
        file_put_contents("../files/confirm_codes.txt", $confirmLine, FILE_APPEND);


        // 7. Clear any previous signup session data
        unset($_SESSION['signup_data']);
        unset($_SESSION['signup_error']);
        unset($_SESSION['allow_signup']);

        // 8. Send confirmation email 
        //if (!mail($email, "Confirm Your Signup", "Your confirmation code is: $code")) {
        //    throw new Exception("Failed to send confirmation email.");
        //}

        // 9. Set session flags for confirmation flow
        // Store succces message to display on confirm.php
        $_SESSION['signup_success'] = "Signup successful! A confirmation code has been sent to your email.";
        $_SESSION['allow_confirm'] = true;
        unset($_SESSION['signup_data']);
        // 10. Redirect to confirmation page
        header("Location: /yourGame/user/confirm.php");
        exit;
    }

    try {
    signUp();
    }catch (Exception $e) {
    // Log error
    error_log("Signup error: " . $e->getMessage());
    
    // Store error in session and redirect back
    $_SESSION['signup_error'] = $e->getMessage();
    unset($_SESSION['allow_signup']);
    header("Location: /yourGame/index.php");
    exit;
    }
?>