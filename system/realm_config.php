<?php
    /*  Central configuration that documents the “state and rules” for each realm
        Each realm defines: short key, name, image, quest flags (artifacts, clues, hazards)
    */
    // Load database connection to that provides $sql
    require_once("db.php");

    function loadRealmConfig($sql) {
        $stmt = $sql->query("SELECT * FROM realms ORDER BY sort_order ASC");
        $realms = [];

        while ($realm = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $key = $realm['realm_key'];

            // Load flags
            $flagStmt = $sql->prepare("SELECT flag_name, default_value FROM realm_flags WHERE realm_key = ?");
            $flagStmt->execute([$key]);
            $flags = [];
            foreach ($flagStmt->fetchAll() as $flag) {
                $flags[$flag['flag_name']] = (bool)$flag['default_value'];
            }

            // Load requirements
            $reqStmt = $sql->prepare("SELECT required_flag FROM realm_requirements WHERE realm_key = ?");
            $reqStmt->execute([$key]);
            $requiredFlags = array_column($reqStmt->fetchAll(), 'required_flag');

            // Assemble realm config
            $realms[$key] = [
                "name" => $realm['name'],
                "file" => $realm['file_path'],
                "image" => $realm['image_path'],
                "flags" => $flags,
                "required_flags" => $requiredFlags,
                "shield" => $realm['shield'],
                "artifact" => $realm['artifact'],
                "clue" => $realm['clue'],
                "hazard" => $realm['hazard']
            ];
        }

        return $realms;
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Load the realm configuration from database
    $realms = loadRealmConfig($sql);

    // Initialize session flags for all realms if not already set
    /* Example:
        $_SESSION['quests'] = [
            "Citadel" => [
                "visited" => false,
                "assembly_completed" => false,
                "reactor_destroyed" => false
            ],
        ];
    */
    foreach ($realms as $key => $realm) {
        if (!isset($_SESSION['quests'][$key])) {
            $_SESSION['quests'][$key] = $realm['flags'];
        }
    }
?>