<?php
    /*  Processes quest updates triggered by JavaScript fetch() calls.
        All realms use this to update thier quest state without reloading the page via fetch().
    */
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include_once(__DIR__ . "/realm_config.php");   // Loads realm-specific settings
    include_once(__DIR__ . "/db.php");             // Loads database connection ($pdo)
    include_once(__DIR__ . "/questManager.php");   // Loads the QuestManager class

    // Instantiate QuestManager
    

    // Get parameters from POST or GET

   
    // Route actions
   
?>