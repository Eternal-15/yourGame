<?php 
    include("../system/init.php");

    // Session check
    if (!isset($_SESSION['user'])) {
        // No session? Try restoring via auth.php
        header("Location: /yourGame/user/auth.php");
        exit;
    }  
    
    //Realm Modules
    include_once("realms/forest.php");
    include_once("realms/desert.php");
    include_once("realms/temple.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portal of Realms</title>
    <link rel="stylesheet" href="/yourGame/css/portal.css">
    <script src="/yourGame/scripts/inventory_hud.js"></script>
</head>

<body>
    <div class="potrait">
        Please rotate your device to landscape mode.
    </div>

    <div class="landscape">
        <!-- HUD Wrapper: Inventory + Logout -->
        <div class="hud-wrapper">
            <?php include("../system/inventory_hud.php"); ?>
            <?php include("../system/user_hud.php"); ?>
        </div>

        <!-- Portal Scene -->
        <div class="portal-container">
            <h1 class="portal-title">Choose Your Realm</h1>

            <?php if (!$questManager->getFlag('Citadel', 'reactor_destroyed')): ?>
                <div class="reactor-alert">
                    ⚠️ The Reactor is still active. Time is running out...
                </div>
            <?php endif; ?>

            <div class="realm-grid">
                <?php
                // Loop through all realms from the central config
                foreach ($realms as $key => $realm) {
                    renderRealmCard($realm['name'], $realm['file'], $realm['image'], $key);
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        refreshInventoryHUD();

        function flipCard(button) {
            const cardInner = button.closest(".card-inner");
            cardInner.classList.toggle("flipped");
        }

        function warpTo(page) {
            window.location.href = page;
        }

        function checkOrientation() {
            const portrait = document.querySelector('.potrait');
            const landscape = document.querySelector('.landscape');

            if (window.innerHeight > window.innerWidth) {
            portrait.style.display = 'flex';
            landscape.style.display = 'none';
            } else {
            portrait.style.display = 'none';
            landscape.style.display = 'block';
            }
        }

        window.addEventListener('resize', checkOrientation);
        window.addEventListener('DOMContentLoaded', checkOrientation); 
    </script>
</body>
</html>
