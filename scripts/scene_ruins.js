// Define all scenes in order with image, text, and optional actions
const scenes = [
  {
    id: 'ruins',
    image: "/yourGame/images/ruins/ruins.jpg",
    texts: [
      "The portal brings you to the outskirts of the City of Gods.",
      //"You see tall, tiered stone structures rising above lush vegetation, with a reflective body of water in the foreground.",
      //"The sky glows with hues of orange and purple, creating a dramatic and mystical atmosphere.",
      //"You make your way toward the city."
    ],

    // Mark realm as visited
    action: function() {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first execution          

      // Call to upadte the quest flag for visited
      fetch("/yourGame/system/quest_api.php?action=visit&realm=ruins")
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if(result !== "1" ){
            // Trigger popup directly no branching, no delayed choices, and no need for polling.
            showPopupMessage("❓ Unexpected response");
          }

          // Console log for debugging purpose
          console.log("Ruins Visit Update: ", result);
          refreshInventoryHUD();
        });
    }
  },
  {
    id: 'ruins_2',
    image: "/yourGame/images/ruins/ruins_2.jpg",
    texts: [
      "You arrive at the overgrown jungle ruins.",
      //"A large stone archway stands at the centre, flanked by crumbling statues and moss-covered structures.",
    ]
  },
  {
    id: 'ruins_3',
    image: "/yourGame/images/ruins/ruins_3.jpg",
    texts: [
      "After climbing 100 winding steps, you reach the Sanctum entrance.",
      //"A solitary figure stands on a moss-covered bridge, gazing at a massive ruins nestled deep within an enchanted forest.",
    ]
  },
  {
    id: 'ruins_4',
    image: "/yourGame/images/ruins/ruins_4.jpg",
    texts: [
      "Inside the ruins, the architecture becomes more intimate and layered.",
      //"Twisted tree roots snake through the structure, suggesting centuries of abandonment.",
    ]
  },
  {
    id: 'ruins_5',
    image: "/yourGame/images/ruins/ruins_5.jpg",
    texts: [
      "The priest leads you to the innermost sanctum.",
      "At the heart of the ruins lies a divine statue, radiating spiritual energy.",          
    ],

    // Trigger scene action (typeText initates this first after completing narration)
    action: function () {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first execution

      // Wait for delayed pop-up
      this.expectingPopup = true;
      // Wait for pop-up to finish before showing choices
      this.waitingForChoices = true;

      // Call to update quest flag for hazard encountered
      fetch("/yourGame/system/quest_api.php?action=hazard&realm=ruins&item=" + encodeURIComponent("Soul Sucker"))
        .then(res => res.text())
        .then(data => {
          const value = data.trim();
          if (!value.includes("1")) {
            this.popupText = "❓ Unexpected response";

            // Console log for debugging purpose
            console.log("Ruins hazard faced: ", value);
            refreshInventoryHUD();
          }else{
            // Remove shield only if hazard has been faced
            fetch("/yourGame/system/inventory_api.php?action=removeShield")
              .then(res => res.text())
              .then(data => {
                const result = data.trim();
                if (result.includes("None")) {
                    this.popupText = "💀 The dead do not carry protection. Your soul begins to unravel... ✨"; // Message
                    this.popupDuration = 6000;  // Duration
                    this.popupOnComplete = () => {
                      window.location.href = "/yourGame/system/reset.php";   // onComplete()
                    };
                    // If narration is complete, show popup immediately
                    if (this.narrationComplete) {
                        showPopupMessage(this.popupText, this.popupDuration, this.popupOnComplete);
                        this.expectingPopup = false; // Prevent engine from re-triggering popup flow
                    }
                } else if (result.includes("Shield Lost")) {
                    this.popupText =`🩻 The Soul Sucker shattered your shield. Be careful! you are unprotected now. ✨`;
                } else {
                    this.popupText = "❓ Unexpected response";
                }

                // Console log for debugging purpose
                console.log("Ruins shield removal: ", result);
                refreshInventoryHUD();
              });
          }     
        });
    },

    // Present branching choices to the player
    choices: [
        { label: "Head Back", nextScene: "ruins_6" }
    ]
  },
  {
    id: 'ruins_6',
    image: "/yourGame/images/ruins/ruins_6.jpg",
    texts: [
      "You arrive at a tranquil ruins with a waterfall cascading behind it.",
      //"A golden light glows behind the ruins."
    ]
  },
  {
    id: 'ruins_7',
    image: "/yourGame/images/ruins/ruins_7.jpg",
    texts: [
      "A majestic lion adorned in gold and emeralds leaps onto the path.",
      //"Lion: 'You were spared only by divine grace. Leave now.'",
    ]
  },
  {
    id: 'ruins_8',
    image: "/yourGame/images/ruins/ruins_8.jpg",
    texts: [
      "A majestic lion adorned in gold and emeralds leaps onto the path.",
      //"Lion: 'You were spared only by divine grace. Leave now.'",
    ]
  },
  {
    id: 'ruins_9',
    image: "/yourGame/images/ruins/ruins_9.jpg",
    texts: [
      "At the riverbank, a hooded figure greets you.",
      //"He reveals himself as the guardian of this realm.",
    ],
    
    // Trigger scene action (typeText initates this first after completing narration)
    action: function() {
        if (this.triggered) return; // Get out of the function if true
        this.triggered = true; // Set to true upon first execution

        // Wait for delayed pop-up
        this.expectingPopup = true;
      
        // Call to update the quest flag as clue is found  
        fetch("/yourGame/system/quest_api.php?action=clue&realm=ruins&item=" + encodeURIComponent("Ancient Guardian"))
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if(result !== "1" ){
             this.popupText = "❓ Unexpected response";
          }else{
            // Pop-up box for the msg
             this.popupText = "🔅 You have found the clue from the Ancient Guardian.✨";
          }
        
          // Console log for debugging purpose
          console.log("Ruins Clue Update: ", result);
          refreshInventoryHUD();
        });
    }
  },
  {
    id: 'ruins_10',
    image: "/yourGame/images/ruins/ruins_10.jpg",
    texts: [
      "A majestic lion adorned in gold and emeralds leaps onto the path.",
      //"Lion: 'You were spared only by divine grace. Leave now.'",
    ]
  }
  
];

/* -------------------------------------------------------------------------------------------------------
    1. PAGE LOAD: First block that runs when the page finishes loading
    --------------------------------------------------------------------------------------------------------*/
document.addEventListener("DOMContentLoaded", () => {
  // Refresh Inventory HUD to display latest values for shield and artifact.
  refreshInventoryHUD();
  
  // Orientation check
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

  checkOrientation(); // Runs once on load calling the above function
  window.addEventListener('resize', checkOrientation); // Re-check on rotate

  // Determine which scene to load by calling the function 
  const sceneId = new URLSearchParams(window.location.search).get("scene");
  if (sceneId) {
    loadSceneById(sceneId); // Direct scene load
  } else {
      loadSceneById("ruins"); // First-time entry
  }
});
