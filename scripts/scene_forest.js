// Define all scenes in order with image, text, and optional actions
const scenes = [
  {
    id: 'forest',
    image: "/yourGame/images/forest/forest.jpg",
    texts: [
        "A shimmering portal hums behind you, but as you step forward, the scene before you unfolds like a vibrant dream."
    ]
  },
  {
    id: 'forest_2',
    image: "/yourGame/images/forest/forest_2.jpg",
    texts: [
      "As you move forward and explore this new realm, you find yourself at a serene crossroads.",
      "As you move forward and explore this new realm, you find yourself at a serene crossroads."
    ],

    // Present choices based on flag combinations
    action: function () {
      // Delay showing choices
      this.waitingForChoices = true;

      Promise.all([
        fetch("/yourGame/system/quest_api.php?action=get_flag&realm=forest&flag=clue_found").then(res => res.text()),
        fetch("/yourGame/system/quest_api.php?action=get_flag&realm=forest&flag=shield_acquired").then(res => res.text()),
        fetch("/yourGame/system/quest_api.php?action=get_flag&realm=forest&flag=visited").then(res => res.text())
      ]).then(([clueFlag, shieldFlag, visitedFlag]) => {
        const clue = clueFlag.trim() === "true" || clueFlag.trim() === "1";
        const shield = shieldFlag.trim() === "true" || shieldFlag.trim() === "1";
        const visited = visitedFlag.trim() === "true" || visitedFlag.trim() === "1";

        // Present choices based on flag value
        if(visited){
          if (!clue && !shield) {
            this.choices = [
              { label: "Head to the Village", nextScene: "forest_7" },
              { label: "Visit the Fairy Queen", nextScene: "forest_6" }
            ];
          }else if (clue && !shield){
            this.choices = [
              { label: "Head to the Forest", nextScene: "forest_3" }                  
            ];
          }else if (!clue && shield){
            this.choices = [
              { label: "Head to the Village", nextScene: "forest_7" }                  
            ];
          }else{
            this.choices = [
              { label: "Head to the Portal", nextScene: "forest_11" }                  
            ];
          }
        }else{
          this.choices = [
            { label: "Head to the Village", nextScene: "forest_7" },
            { label: "Head to the Forest", nextScene: "forest_3" },
          ];
        }
        this.waitingForChoices = false;
      });
    }
  },
  {
    id: 'forest_3',
    image: "/yourGame/images/forest/forest_3.jpg",
    texts: [
        "You make your way into the forest. The forest devours the light behind you, but you see a faint light in a distance as if it's trying to lead you to somewhere.",
    ]
  },
  {
    id: 'forest_4',
    image: "/yourGame/images/forest/forest_4.jpg",
    texts: [
        `A tiny fairy with shimmering wings hovers in the air, arms crossed and eyes twinkling with curiosity and challenge.`,
    ]
  },
  {
    id: 'forest_5',
    image: "/yourGame/images/forest/forest_5.jpg",
    texts: [
        `You see a magnificent, ancient tree standing at the center of the scene. Its massive trunk twisting and turning, its leaves glimmering like constellations and glowing golden light emitting from within the tree.`, 
    ]
  },
  {
    id: 'forest_6',
    image: "/yourGame/images/forest/forest_6.jpg",
    texts: [
      `You follow Lysera inside the giant tree where the air is thick with floral perfume. She leads you to a room that stretches into a forest.`,
      `Sitting upon a throne of roots and petals is the Fairy Queen. Upon seeing you she gets up from her throne and walks towards you.`          
    ],

    // Present additional narration based on flag combinations
    action: function () {
        if (this.triggered) return; // Get out of the function if true
        this.triggered = true; // Set to true upon first execution

        // Wait for additinal text before showing pop-up and choices
        this.expectingMoreText = true;
        // Wait for delayed pop-up
        this.expectingPopup = true;
        // Wait for pop-up to finish before showing choices
        this.waitingForChoices = true;
      
        Promise.all([
            fetch("/yourGame/system/quest_api.php?action=get_flag&realm=forest&flag=visited").then(res => res.text()),
            fetch("/yourGame/system/quest_api.php?action=get_flag&realm=forest&flag=clue_found").then(res => res.text())
        ])
            .then(([visitResult, clueResult]) => {
            const visit_flag = visitResult.trim();
            const clue_flag = clueResult.trim();
        
            // Check visited flag to add intro narration   
            if (visit_flag === "false" || (visit_flag === "1" && clue_flag === "1")) {
                this.texts.push(
                    `The Fairy Queen before you is a vision of gentle grace and ancient wisdom. She has a warm, motherly presence that immediately puts you at ease.`,
                    `Her skin glows softly in the moonlight, and her eyes hold a kind yet knowing gaze. She has pointed, elfin ears and delicate, shimmering wings that resemble the delicate veins of a leaf.`
                );      
            }else if(visit_flag === "1" && clue_flag === "false" ){
                this.texts.push(
                    "Queen Fairy: Welcome back my child. Have you come for the shield?",
                    "You: Yes" 
                );
            }

            // Call to add shield after narration
            fetch("/yourGame/system/inventory_api.php?action=addShield&realm=forest&item=" + encodeURIComponent("Repel Repel Fruit"))
            .then(res => res.text())
            .then(data => {
                const result = data.trim();
                if (result === "Repel Repel Fruit") {
                    this.texts.push(
                        "The Queen Fairy reaches out, offering you the Repel Repel Fruit.",
                        "Queen Fairy: Eat this fruit. It will form a shield and repel one single attack against you. It’s a reminder that even a single act of courage can turn the tide."
                    );
                    // Delayed popup message, waits for pushed texts
                    this.popupText = "🍑 You have eaten the Repel Repel Fruit...✨";
                }else if(result.includes("already have")) {
                    this.texts.push(
                        "Queen Fairy: You already possess a shield my child. I cannot give you another.",
                        "Queen Fairy: Come back when you need it's protection."
                    );
                    this.popupText = result;
                }else{
                    this.popupText = "❓ Unexpected response.";
                }

                // Clear flags
                this.expectingMoreText = false; // No new texts expected
                this.waitingForChoices = false; // No element waiting, choice can be displayed now
                
                // Console log for debugging purpose
                console.log("Forest shield addition: ", result);
                refreshInventoryHUD();
            });
      });

      // Call to upadte the quest flag for visited
      fetch("/yourGame/system/quest_api.php?action=visit&realm=forest")
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if(result !== "1" ){
            // Trigger popup directly no branching, no delayed choices, and no need for polling.
            showPopupMessage ("❓ Unexpected response");
          }

          // Console log for debugging purpose
          console.log("Forest Visit Update: ", result);
          refreshInventoryHUD();
        });
    },

    // Present branching choices to the player
    choices: [
      { label: "Explore the Realm", nextScene: "forest_2" },
      { label: "Head to the Portal", nextScene: "forest_11" }
    ]
  },
  {
    id: 'forest_7',
    image: "/yourGame/images/forest/forest_7.jpg",
    texts: [
      `You arrive at the hill that overlooks the village, the quietness of the evening settling over the rooftops. Despite the stillness, the entire settlement seems alive with a strange, gentle vibrancy.`
    ]
  },
  {
    id: 'forest_8',
    image: "/yourGame/images/forest/forest_8.jpg",
    texts: [
      `You hesitate briefly, then step into a cozy room illuminated by lantern light. Inside, a small gathering of young villagers, their faces serene and welcoming. A young boy, distinguished and calm, looks up and offers a gentle smile.`
    ]
  },
  {
    id: 'forest_9',
    image: "/yourGame/images/forest/forest_9.jpg",
    texts: [
      `You step into the dense forest surrounding the village, where the moonlight filters through thick foliage. The air is thick with the scent of blooming sakura, their petals softly falling like pink snow.`,
    ]
  },
  {
    id: 'forest_10',
    image: "/yourGame/images/forest/forest_10.jpg",
    texts: [
      `Following the trail, you step into a cave shimmering with droplets of light.`, 
      `Mist curls unnaturally thick, veiling a hidden path.`
    ],
    
    // Trigger quest update via fetch to backend
    action: function () {
        if (this.triggered) return; // Get out of the function if true
        this.triggered = true; // Set to true upon first execution

        // Wait for delayed pop-up
        this.expectingPopup = true;
        // Wait for pop-up to finish before showing choices
        this.waitingForChoices = true;

        // Call to update the quest flag as clue is found
        fetch("/yourGame/system/quest_api.php?action=clue&realm=forest&item=" + encodeURIComponent("Fox Spirit"))
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if(result !== "1" ){
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = "❓ Unexpected response";
          }else{
            // Trigger popup (typetext initiates this after scene action)
            this.popupText = "🦊 You have found the clue from the Fox Spirit.✨";
          }
          
          // Console log for debugging purpose
          console.log("Forest Clue Update: ", result);
          refreshInventoryHUD();
        });

        // Mark realm as visited
        fetch("/yourGame/system/quest_api.php?action=visit&realm=forest")
          .then(res => res.text())
          .then(data => {
            const result = data.trim();
            if(result !== "1" ){
                // Trigger popup directly no branching, no delayed choices, and no need for polling.
                showPopupMessage("❓ Unexpected response");
            }

            // Console log for debugging purpose
            console.log("Forest Visit Update: ", result);
            refreshInventoryHUD();
          });
    },

    // Present branching choices to the player
    choices: [
      { label: "Explore the Realm", nextScene: "forest_2" },
      { label: "Head to the Portal", nextScene: "forest_11" }
    ]
  },
  {
    id: 'forest_11',
    image: "/yourGame/images/forest/forest_11.jpg",
    texts: [
      `You find yourself standing before a mysterious, glowing portal embedded in a thick, ancient stone. The portal radiates an eerie blue light, flickering softly like a heartbeat, with swirling patterns that seem to shift and breathe.`
    ],
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
  fetch("/yourGame/system/quest_api.php?action=get_flag&realm=forest&flag=visited")
    .then(res => res.text())
    .then(flag => {
      console.log("Visited flag value:", flag); // Console entry for debuggin purpose
      
      const sceneId = new URLSearchParams(window.location.search).get("scene");
      const trimmedFlag = flag.trim();

      // Determine which scene to load by calling the function
      if (trimmedFlag === "true" || trimmedFlag === "1") {
        loadSceneById("forest_2"); // Revisit entry (when visited flag is true or 1)            
      } else if (sceneId) {
        loadSceneById(sceneId); // Direct scene load
      } else {
        loadSceneById("forest"); // First-time entry
      }
    });
});
