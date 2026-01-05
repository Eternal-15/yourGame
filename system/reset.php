<?php
  /* Resets the player's inventory and quest state to initial defaults.
    Useful for death recovery, testing, or restarting the game.
  */
  
  // Ensure session is active before accessing $_SESSION
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }

  include_once(__DIR__ . "/realm_config.php");      // Loads realm definitions and flag requirements
  include_once(__DIR__ . "/db.php");                // Loads sql database connection
  include_once(__DIR__ . "/inventoryManager.php");  // Optional: used if you want to reinitialize inventory logic
  include_once(__DIR__ . "/questManager.php");      // Optional: used if you want to reinitialize quest logic
  
  // Get the current player ID from session
  $playerId = $_SESSION['user'] ?? null;
  // Defensive check: make sure player is logged in and database is available
  if (!$playerId || !$sql) {
      echo "Reset failed: player not authenticated.";
      exit;
  }
  
  /* -----------------------------
    STEP 1: Define default inventory
    ----------------------------- */
  $defaultInventory = [
      "shield" => null,               // No shield equipped
      "artifact" => "Galayadh",       // Starting artifact
      "shield_list" => [],            // No shields acquired
      "artifact_list" => [],          // No artifacts found
      "assembler_list" => [],         // Empty assembler
      "mother_flame" => false         // Mother Flame not activated
  ];

  /* -----------------------------
    STEP 2: Define default quest containers
    ----------------------------- */
  $defaultQuest = [
      "clue_list" => [],             // No clues found
      "hazard_list" => [],           // No hazards encountered
      "visited_list" => []           // No realms visited
  ];

  /* -----------------------------
    STEP 3: Build default realm flags
    ----------------------------- 
    This ensures every realm has a consistent flag structure.
    Flags are based on realm_config.php:
    - Required flags (e.g., clue_found, reactor_destroyed)
    - Common flags (e.g., visited, hazard_encountered)
  */
  $realmFlags = [];

  foreach ($realms as $realmKey => $config) {
      $realmFlags[$realmKey] = [];

      // Initialize required flags to false
      foreach ($config['required_flags'] ?? [] as $flag) {
          $realmFlags[$realmKey][$flag] = false;
      }

      // Add common flags used across all realms
      $realmFlags[$realmKey]['visited'] = false;
      $realmFlags[$realmKey]['clue_found'] = false;
      $realmFlags[$realmKey]['hazard_encountered'] = false;
      $realmFlags[$realmKey]['reactor_destroyed'] = false;
      $realmFlags[$realmKey]['assembly_completed'] = false;
  }

  /* -----------------------------
    STEP 4: Update database with reset values
    ----------------------------- 
    This clears all progress and restores initial state.
    All lists are encoded as JSON for storage.
  */
  $stmt = $sql->prepare("
      UPDATE player_state
      SET shield = NULL,
          artifact = ?,
          shield_acquired_list = ?,
          artifact_found_list = ?,
          assembler_list = ?,
          clue_found_list = ?,
          hazard_encountered_list = ?,
          visited_list = ?,
          realm_flags = ?
      WHERE player_id = ?
  ");

  $success = $stmt->execute([
      "Galayadh",                    // Starting artifact
      json_encode([]),               // Empty shield list
      json_encode(["Galayadh"]),     // Initial artifact list
      json_encode([]),               // Empty assembler list
      json_encode([]),               // Empty clue list
      json_encode([]),               // Empty hazard list
      json_encode([]),               // Empty visited list
      json_encode($realmFlags),      // Realm flags reset
      $playerId                      // Current player
  ]);

  if (!$success) {
      echo "Reset failed: database update error.";
      exit;
  }

  /* -----------------------------
    STEP 5: Reset session variables
    ----------------------------- 
    This ensures gameplay reflects the reset state immediately.
  */
  $_SESSION['inventory'] = $defaultInventory;
  $_SESSION['artifact_sequence'] = ["Galayadh", "Horologe", "Clepsydra", "Behester", "Chandrahas"];
  $_SESSION['quest'] = $defaultQuest;
  
  // Add realm-specific flags to session
  foreach ($realmFlags as $realmKey => $flags) {
      $_SESSION['quest'][$realmKey] = ["flags" => $flags];
  }

  /* -----------------------------
   STEP 6: Redirect
   ----------------------------- */
  header("Location: /yourGame_database/realms/portal.php");
  exit;
?>