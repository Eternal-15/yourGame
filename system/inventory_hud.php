<div id="inventory-hud" class="inventory-hud">
    <!-- It doesn’t open or close HTML tags because it’s meant to be dropped into the layout wherever needed.-->
    <!-- This file is used for ready-only display of the player's current inventory in all realms -->
    <?php
        // Ensure session is active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get formatted inventory status
        global $inventoryManager;
        $status = $inventoryManager->getInventory();       
        
        // Define icons and labels for display
        $items = [
            'shield'   => ['icon' => '🛡️', 'label' => 'Shield'],
            'artifact' => ['icon' => '🪙', 'label' => 'Artifact'],
        ];

        // Output each item in HUD
        foreach ($items as $key => $meta) {
            $value = $status[$key] ?? 'None';
            $icon  = $meta['icon'];
            $label = $meta['label'];

            // Special display for Artifact slot when Mother Flame is active
            if ($key === 'artifact' && $value === 'Mother Flame') {
                $icon  = '🔥';
                $label = 'Mother Flame';
                $value = 'Mother Flame';
            }

            echo '<span id="hud-' . $key . '" class="icon slot-' . $key .
                '" data-tooltip="' . $label . '">' . $icon . ' ' . htmlspecialchars($value) . '</span>';
        }
    ?>
</div>
