   
<?php
    /*  Modular function used by portal.php to show each realm’s card with image, name, status, and enter realm button.
           Render a realm card for the portal
           Uses centralized quest/flags system from init.php / realm_config.php
           Displays visited status, quest completion, and locks entry if quest completed    
    */

    function renderRealmCard($name, $file, $image, $realmKey) {
        global $questManager;

        $visited = $questManager->getFlag($realmKey, 'visited') ? "Yes" : "Never";
        $completed = $questManager->isQuestComplete($realmKey) ? "Completed" : "Active";
        $isLocked = $questManager->isQuestComplete($realmKey);

        echo '
        <div class="realm-card">
            <div class="card-inner">

                <!-- Front face -->
                <div class="card-front">
                    <div class="image-wrapper">
                        <img src="/yourGame/images/' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($name) . '">
                        <button class="info-btn" onclick="flipCard(this)">ℹ️</button>
                    </div>
                    <div class="realm-label">' . htmlspecialchars($name) . '</div>
                </div>

                <!-- Back face -->
                <div class="card-back">
                    <div class="realm-info">
                        <p><strong>Visited:</strong> ' . $visited . '</p>
                        <p><strong>Quest Status:</strong> ' . $completed . '</p>
                        <button class="info-btn" onclick="flipCard(this)">ℹ️</button>
                    </div>';

        if ($isLocked) {
            echo '<p class="realm-locked">🔐 Realm Completed Access Now Locked.</p>';
        } else {
            echo '<button class="enter-btn" onclick="warpTo(\'' . htmlspecialchars($file) . '\')">Enter Realm</button>';
        }

        echo '
            </div>
        </div>
        </div>';
        }
?>

