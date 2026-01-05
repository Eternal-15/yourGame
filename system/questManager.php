<?php
    /*  Utility functions for quests that are called by quest_api 
        Handles quest initialization and helper functions 
    */

    include_once(__DIR__ . "/realm_config.php"); // Loads realm-specific settings
    include_once(__DIR__ . "/db.php");           // Loads database connection ($sql)

    /*  This class handles all quest-related logic for a player.
        It manages realm flags, quest progression, and hazard interactions.
        Flags are stored in the database (`player_state.realm_flags`) and mirrored in session for gameplay. 
    */
    
    class QuestManager {
        private $session;  // Reference to the session array ($_SESSION)
        private $sql;      // sql database connection
        private $realms;   // Realm configuration array (from realm_config.php)

        /*
            Constructor initializes session, database, and realm config
        */
        public function __construct(&$session, $sql, $realms) {
            $this->session = &$session;
            $this->sql = $sql;
            $this->realms = $realms;
        }    
        
        /*
            Initializes the quest session structure.
                - Loads default flags from the realm_flags table
                - Sets up per-realm containers for flags, clues, hazards, and visited status
                - Sets up global containers for summary tracking
            This ensures every realm has a consistent flag structure, even for new players
        */
        private function initialize() {
               
        }

    }  
?>

