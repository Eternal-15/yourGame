// Define all scenes in order with image, text, and optional actions
const scenes = [
  {
    id:"desert",
    image: "/yourGame/images/desert/desert.jpg",
    texts: [
        "You stagger out of the portal, sand clinging to your boots. Ahead, a river snakes through the dunes, lined with palms that whisper against the wind.",
        "Pyramids pierce the horizon, their peaks catching the dying sun. A voice whispers, not from around you, but inside your head.",
    ],
    
    // Trigger flag update via fetch to backend
    action: function () {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first execution

      // Call to upadte the quest flag for visited
      fetch("/yourGame/system/quest_api.php?action=visit&realm=desert")
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if(result !== "1" ){
            // Trigger popup directly no branching, no delayed choices, and no need for polling.
            showPopupMessage("❓ Unexpected response");
          }

          // Console log for debugging purpose
          console.log("Desert Visit Update: ", result);
          refreshInventoryHUD();
        });
    }
  },
  {
    id:"desert_2",
    image: "/yourGame/images/desert/desert_2.jpg",
    texts: [
        "Descending into the oasis town, you are swallowed by sound. Merchants call out in a language you half-understand, spices sting your nose, and bright awnings ripple like sails.",
        "Yet the townsfolk look at you too long, their voices faltering when you pass. Far in the distance, a commotion stirs.", 
    ]
  },
  {
    id:"desert_3",
    image: "/yourGame/images/desert/desert_3.jpg",
    texts: [
        "You make your way through the crowd upfront. At the far end, a radiant figure kneels before a child.", 
        `The boy’s trembling hands touch the mask of the Pharaoh, and golden light ripples through his face.`,
    ]
  },
  {
    id:"desert_4",
    image: "/yourGame/images/desert/desert_4.jpg",
    texts: [
        "The desert swallows all sound but your own breath. Three days pass.",
        "Finally, at mid-noon, the golden pyramid rises before you, impossibly vast. You have arrived at The Beacon of Mer.",
    ]
  },
  {
    id:"desert_5",
    image: "/yourGame/images/desert/desert_5.jpg",
    texts: [
        "You enter the pyramid, your boots echoing against cold, polished stone. Light from the shifting hieroglyphs along the walls casts strange patterns across the floor. The air tastes of metal and dust, humming faintly like a heartbeat.",
        "A massive chamber opens before you, empty except for shadows and silence. Then, a figure steps from a shaft of pale light.",
    ],

    // Redirect to trail.php by sending the return scene via URL
    action: () => {
      window.location.href = "trial.php?returnScene=desert_6";
    }
  },
  {
    id: "desert_6",
    image: "/yourGame/images/desert/desert_6.jpg",
    texts: [
        `You step through the doorway Amun-Tef revealed. The chamber beyond is carved from ancient stone, its walls alive with glowing runes whispering in a language too old to understand.`,
        `At the center of the chamber, encased in a cylindrical golden frame and suspended above a pedestal carved from obsidian and gold is a scroll that radiates with an unmistakable arcane presence.`, 
    ],
    
    // Trigger flag update via fetch to backend
    action: function() {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first execution

      // Wait for delayed pop-up
      this.expectingPopup = true;

      // Call to update quest flag for clue found
      fetch("/yourGame/system/quest_api.php?action=clue&realm=desert&item="+ encodeURIComponent("Ancient Scroll"))
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if (result !== "1" ){
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = "❓ Unexpected response";
          } else{
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = "📜 A sacred clue has been revealed. Find the assembler and unite the lost artifacts. ✨";
          }

          // Console log for debugging purpose
          console.log("Desert Clue Update: ", result);
          refreshInventoryHUD();
        });
    }
  },
  {
    id:"desert_7",
    image: "/yourGame/images/desert/desert_7.jpg",
    texts: [
        "The golden pyramid looms infront of you, the Beacon of Mer piercing the heavens like a spear is a breathtaking spectacle.",
        "On your right in the distance, you see a colossal structure, ancient and alien, shaped like a gateway. You can hear a faint hum coming from it's stone frame. It looks like The Ancient Portal.",
    ],

    // Present branching choices to the player
    choices: [
      { label: "Head to the Village", nextScene: "desert_8" },
      { label: "Explore the Ancient Portal", nextScene: "desert_12" }
    ]
  },
  {
    id:"desert_8",
    image: "/yourGame/images/desert/desert_8.jpg",
    texts: [
        "You turn your steed and begin the long journey to the village across the endless dunes. The wind howls, and each night the stars burn brighter against the vast desert sky.", 
        "On the second day, as the sun dips low, you see a lone figure standing still upon the sands. Cloaked and silent, he seems to bend the very air around him. When you approach, he raises a hand as if to greet you.",
    ]
  },
  {
    id:"desert_9",
    image: "/yourGame/images/desert/desert_9.jpg",
    texts: [
        "Rising from the sands, untouched by decay, stands a cathedral of breathtaking beauty. Its spires reach into the sky, weathered yet unbroken, every arch and carving etched with sacred precision. Cacti and desert flowers bloom stubbornly around its base, as if nature itself bows in reverence to this forgotten sanctuary.",
        "The sky above churns with restless clouds, shafts of golden sunlight breaking through in sudden bursts. Each beam strikes the cathedral like divine fire, making the stone glow as though alive. From within its vast halls, grains of sands drift outward, flowing into the air like strands of hair caught in an unseen breeze.",
    ]
  },
  {
    id:"desert_10",
    image: "/yourGame/images/desert/desert_10.jpg",
    texts: [
        "Crossing the threshold, the vastness of the cathedral swallows you whole. The air inside is cool, thick with the echo of centuries, though no voice lingers here now. Rays of sunlight pierce broken windows, falling in golden pools that mark your path.",
        "At the very center, upon a table of dark wood polished smooth, rests an hourglass unlike any crafted by mortal hands. Its frame is gilded with runes that shift when you look too long, and within, liquid gold flows downward.",
    ],
    
    // Trigger inventory update via fetch to backend
    action: function() {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first execution

      // Wait for delayed pop-up
      this.expectingPopup = true;

      // Call to add the artifact to the inventory
      fetch("/yourGame/system/inventory_api.php?action=collectArtifact&realm=desert&item=" + encodeURIComponent("Clepsydra"))
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if (result === "Clepsydra") {
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = "🪙 Clepsydra has been added to your inventory. Find the assembler and unite the lost artifacts.✨";
          } else if(result.includes("cannot collect")) {
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = result;
          } else if(result.includes("cannot be added")) {
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = result;
          } else{
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = "❓ Unexpected response.";
          }

          // Console log for debugging purpose
          console.log("Desert artifact addition: ", result);
          refreshInventoryHUD();
        });
    }
  },
  {
    id:"desert_11",
    image: "/yourGame/images/desert/desert_11.jpg",
    texts: [
        `The moment your hand closes around the Clepsydra, the hourglass trembles with ancient urgency. Its sands swirl violently, glowing with a pale, spectral light — and then the winds rise.`,
        `A sudden vortex erupts around you, pulling the air into a spiral of memory and magic. The chamber — once still — begins to unravel.`, 
    ]
  },
  {
    id:"desert_12",
    image: "/yourGame/images/desert/desert_12.jpg",
    texts: [
        "Infront of you amid the sand and stone debris, rises a colossal archway — fractured, weathered, yet still alive with a faint inner glow. Its carvings pulse as though remembering a language long lost, each crack humming with the weight of forgotten ages.",
        "Jagged rocks loom around it like guardians, their sharp silhouettes cutting against a brooding sky. Here, the ruins of a great civilization stand silent, their beauty broken but still echoing with power.",
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

  // Fetch visited flag to check visited status      
  fetch("/yourGame/system/quest_api.php?action=get_flag&realm=desert&flag=visited")
    .then(res => res.text())
    .then(flag => {
      console.log("Visited flag value:", flag); // Console entry for debuggin purpose
      
      const sceneId = new URLSearchParams(window.location.search).get("scene");
      const trimmedFlag = flag.trim(); // Remove any whitespaces

      // Determine which scene to load by calling the function
      if (trimmedFlag === "true" || trimmedFlag === "1") {
        loadSceneById("desert_7"); // Revisit entry (when visited flag is true or 1)
      } else if (sceneId) {
        loadSceneById(sceneId); // Direct scene load
      } else {
        loadSceneById("desert"); // First-time entry
      }
    });
});