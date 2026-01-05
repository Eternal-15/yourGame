<?php
    // Start session to access session variables
    include(__DIR__ . "/../system/session.php");
    // Load utility functions from utils.php to record all error messages
    // include_once(__DIR__ . "/../system/utils.php");

    // Step 1: If session is already set, preload player_state and redirect
    try {
        if (isset($_SESSION['user'])) {
            $playerId = $_SESSION['user'];
            $stateFile = __DIR__ . "/files/player_state.txt";
            $lines = file($stateFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $parts = explode("|", $line);
                if ($parts[0] === $playerId) {
                    $_SESSION['character'] = $parts[1] ?? '';
                    $_SESSION['avatar'] = $parts[2] ?? '';
                    $_SESSION['shield'] = $parts[3] ?? '';
                    $_SESSION['artifact'] = $parts[4] ?? '';

                    $_SESSION['shields_acquired'] = json_decode($parts[5] ?? '[]', true);
                    $_SESSION['artifacts_acquired'] = json_decode($parts[6] ?? '[]', true);
                    $_SESSION['clues_acquired'] = json_decode($parts[7] ?? '[]', true);
                    $_SESSION['hazards_encountered'] = json_decode($parts[8] ?? '[]', true);
                    $_SESSION['realm_visited'] = json_decode($parts[9] ?? '[]', true);
                    $_SESSION['assembler'] = json_decode($parts[10] ?? '[]', true);
                    break;
                }
            }

            $_SESSION['allow_logout'] = true;
            header("Location: /yourGame/realms/portal.php");
            exit;
        }
    } catch (Exception $e) {
        // logError("Session preload failed: " . $e->getMessage());
        $_SESSION['auth_error'] = $e->getMessage();
        unset($_SESSION['user']);
        header("Location: /yourGame/index.php");
        exit;
    }
    


    // Step 2: If session is not set, check for a valid cookie to restore session
    try {
        if (isset($_COOKIE['remember_token'])) {
            $token = trim($_COOKIE['remember_token']);
            $tokenFile = __DIR__ . "/files/tokens.txt";
            $lines = file($tokenFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $parts = explode("|", $line);
                if (count($parts) < 3) continue;

                list($email, $storedToken, $expiry) = $parts;

                if (trim($storedToken) === $token && time() < strtotime($expiry)) {
                    $_SESSION['email'] = $email;

                    // Restore user ID from users.txt
                    $users = file(__DIR__ . "/files/users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($users as $userLine) {
                        $userParts = explode("|", $userLine);
                        if (isset($userParts[3]) && strtolower(trim($userParts[3])) === strtolower(trim($email))) {
                            $_SESSION['user'] = $userParts[0];
                            break;
                        }
                    }

                    // Preload player_state
                    $playerId = $_SESSION['user'];
                    $stateFile = __DIR__ . "/files/player_state.txt";
                    $lines = file($stateFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                    foreach ($lines as $line) {
                        $parts = explode("|", $line);
                        if ($parts[0] === $playerId) {
                            $_SESSION['character'] = $parts[1] ?? '';
                            $_SESSION['avatar'] = $parts[2] ?? '';
                            $_SESSION['shield'] = $parts[3] ?? '';
                            $_SESSION['artifact'] = $parts[4] ?? '';

                            $_SESSION['shields_acquired'] = json_decode($parts[5] ?? '[]', true);
                            $_SESSION['artifacts_acquired'] = json_decode($parts[6] ?? '[]', true);
                            $_SESSION['clues_acquired'] = json_decode($parts[7] ?? '[]', true);
                            $_SESSION['hazards_encountered'] = json_decode($parts[8] ?? '[]', true);
                            $_SESSION['realm_visited'] = json_decode($parts[9] ?? '[]', true);
                            $_SESSION['assembler'] = json_decode($parts[10] ?? '[]', true);
                            break;
                        }
                    }

                    $_SESSION['allow_logout'] = true;
                    header("Location: /yourGame/realms/portal.php");
                    exit;
                }
            }
        }
    } catch (Exception $e) {
        // logError("Session restore from token failed: " . $e->getMessage());
        $_SESSION['auth_error'] = $e->getMessage();
        unset($_SESSION['user']);
        setcookie('remember_token', '', time() - 3600, "/");
        header("Location: /c/index.php");
        exit;
    }

    // Step 3: If session and token both fail, show forbidden message
    echo '
        <div style="text-align: center;">
            <h1>Forbidden Access Auth.</h1>
            <br>    
            <img src="/yourGame/img/forbidden.jpg" alt="Forbidden Access Auth" style="max-width: 300px; margin-bottom: 10px;">
            <br> <br>
            <a href="/yourGame/index.php">Go to Home Page</a>
        </div>
    ';
    // Terminate script execution
    exit;
?>