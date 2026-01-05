// ---------------------------
// inventory_hud.js
// Updates the inventory HUD using data from inventory_api.php
// ---------------------------

/**
 * Fetches inventory status from the backend and updates HUD slots.
 * Expects plain text response in the format:
 *   Shield => Repel Fruit
 *   Artifact => Orb
 *   Assembler => 2
 *   Mother Flame => Activated
 */
function refreshInventoryHUD() {
  fetch("/yourGame/system/inventory_api.php?action=getInventory")
    .then(res => res.json())
    .then(data => {
      const status = {
        shield: data.shield ?? "None",
        artifact: data.artifact ?? "None"
      };

      console.log("Parsed inventory status:", status); // Debug: show parsed object

      // Update HUD slots using parsed data
      updateHUDSlot("shield", status.shield);
      updateHUDSlot("artifact", status.artifact);
    })
    .catch(err => console.error("HUD update failed:", err));
}

/**
 * Updates a specific HUD slot with icon and value.
 * Handles special display logic for Mother Flame.
 *
 * @param {string} slot - The inventory slot name (e.g., "shield", "artifact")
 * @param {string} value - The value to display in the slot
 */
function updateHUDSlot(slot, value) {
  const el = document.getElementById(`hud-${slot}`);
  if (!el) return; // Exit if element not found

  // Choose icon based on slot type
  let icon = slot === "shield" ? "🛡️" : "🪙";
  let displayValue = value;

  // Special display for Mother Flame
  if (slot === "artifact" && value?.toLowerCase() === "mother flame") {
    displayValue = "Mother Flame";
    icon = "🔥";
    // Trigger cinematic glow effect
    el.classList.add("mother-flame");
    setTimeout(() => el.classList.remove("mother-flame"), 1500);
  }

  // Update HUD text content
  el.textContent = `${icon} ${displayValue}`;
  // Trigger general update pulse animation
  el.classList.add("updated");
  setTimeout(() => el.classList.remove("updated"), 600);
}