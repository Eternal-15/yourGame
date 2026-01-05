<?php
  include(__DIR__ ."/../system/init.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ruins of Battlefield</title>
  <!-- Link to the cinematic CSS file -->
  <link rel="stylesheet" href="/yourGame/css/realm.css">
  <!-- 1. Load the inventory hud display -common for all realms -->
  <script src="/yourGame/scripts/inventory_hud.js"></script>
  <!-- 2. Load realm-specific scene data first -->
  <script src="/yourGame/scripts/scene_ruins.js"></script>
  <!-- 3. Load the reusable scene engine after -->
  <script src="/yourGame/scripts/scene_engine.js" defer></script>
</head>
<body>
  <div class="potrait">
      Please rotate your device to landscape mode.
  </div>

  <div class="landscape"> 
    <!-- Hidden Pop-up Message Box -->
    <div id="popupMessage" class="popup hidden"></div>
    
    <!-- Hud Area -->
    <div class="hud-wrapper">
      <!-- Heads Up Diplay (HUD): -->
      <?php include(__DIR__ ."/../system/inventory_hud.php"); ?>
      <?php include(__DIR__ ."/../system/user_hud.php"); ?>
    </div>

    <!-- Main container for the scene -->
    <div class="scene-container">
      <div class="image-wrapper">
        <!-- Background image that changes with each scene -->
        <img id="realmImage" alt="Realm Background" class="realm-image">

        <!-- Text box overlay for narrative and buttons -->
        <div class="text-box" id="textBox">
          <p id="textContent"></p>
          <button id="nextBtn" type="button" disabled>Next</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>