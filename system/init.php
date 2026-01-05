<?php
    /*
        Centralized initialization for all realms and portals.
        Sets up session, inventory, quest flags, and optional rendering.
    */

    // 1. Start session if not already started
    include_once("session.php"); 

    // 2. Ensure player ID is set
    if (!isset($_SESSION['user'])) {
        // Redirect to index
        header("Location: /yourGame/index.php");
        exit;
    }

    // 3. Load core dependencies
    include_once("db.php");               // Establishes $sql
    include_once("realm_config.php");     // Realm definitions
    include_once("inventoryManager.php"); // Inventory logic
    include_once("questManager.php");     // Quest logic   

    // 4. Initialize InventoryManager
    global $inventoryManager;
    $inventoryManager = new InventoryManager($_SESSION, $sql);  // Loads default inventory upon creation
    $inventoryManager->loadInventoryFromDatabase();             // Load saved inventory from database
    
    // 5. Initialize QuestManager
    global $questManager;
    $questManager = new QuestManager($_SESSION, $sql, $realms);
    $questManager->loadRealmFlags();                            // Load saved flags or initialize defaults
        
    // 6. Realm card rendering
    include_once("realm_cards.php");    
?>