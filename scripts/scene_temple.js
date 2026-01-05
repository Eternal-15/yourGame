// Define all scenes in order with image, text, and optional actions
const scenes = [
  {
    id: 'temple',
    image: "/yourGame/images/temple/temple.jpg",
    texts: [
      `The portal flickers and pulls you through, spitting you out onto a cliffside path. You stagger forward, then freeze—the sight before you steals your breath.`,
      //`The City of Gods stretches across the horizon, a panorama of towering, tiered temples rising above dense jungle. The evening sky glows in hues of blue and white, the golden temples crowned with radiant light.`,
      //`A shimmering lake reflects the city’s grandeur, as if heaven and earth have become one. The air is alive with energy, divine yet heavy, urging your feet to move forward. You spot a path right through the center leading towards the deep plam forest.`,
      //`Thinking...: The portal has brought me here for a reason. I must see what awaits beyond these grand entrace and the forest.`        
    ],

    // Trigger flag update via fetch to backend
    action: () => {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first execution          

      // Call to update quest flag for visited
      fetch("/yourGame/system/quest_api.php?action=visit&realm=temple")
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if(result !== "1" ){
            // Trigger popup directly no branching, no delayed choices, and no need for polling.
            showPopupMessage("❓ Unexpected response");
          }

          // Console log for debugging purpose
          console.log("Temple Visit Update: ", result);
          refreshInventoryHUD();
        });
    }
  },
  {
    id: 'temple_2',
    image: "/yourGame/images/temple/temple_2.jpg",
    texts: [
      `As you make your way into the forest, the grandeur fades into silence. You find yourself standing before overgrown ruins, a massive archway strangled by roots and moss.`,
      //`Crumbled stones  line the path, their stone faces worn and hidden into the lush bush, staring at you from the gaps like gods are watching your every steps.`,
      //`You can only hear a small stream trickling through the ruins but can't see the stream itself. The stone path is catching the faint green and orange glow from the sun filtering through the canopy of trees above. The place feels alive, yet abandoned.`,
      //`Behind the arch, you notice a winding path twisting forward into the foliage.`,
      //`Thinking...: My answers should lie with someone from the city, I must make my way through this entrance.`
    ]
  },
  {
    id: 'temple_3',
    image: "/yourGame/images/temple/temple_3.jpg",
    texts: [
      `After walking for nearly an hour, your legs ache as you arrive at a awe-inspiring sight which feels like your are stepping into a dream woven from centuries of devotion and artistry.`, 
      //`The sky itself seems to be paying tribute—those streaks of purple and orange melting into each other like brushstrokes on a divine canvas. And right beneath it, this sprawling temple complex… it’s breathtaking.`,
      //`You see a majestic temple at the center of the town. Its spires pierce the sky, each one carved with such intricate detail that even from here you can feel the stories etched into stone. All around it, smaller temples form a perfect symmetry, like guardians in formation.`, 
      //`Their architecture mirrors the central shrine, but each has its own subtle flair—slightly different carvings, unique rooflines, little flourishes that make you want to explore every corner of this town... there’s such life here.`,
      //`The market is glowing—lanterns casting warm light on vibrant fabrics, spices, trinkets, offerings. You can hear laughter, prayers, the soft hum of music. Families in traditional dress, elders walking slowly with reverence, children darting between stalls.`,
      //`It’s not chaotic—it’s harmonious. Like the whole place is alive, breathing with culture and spirit.`,
      //`Thinking...: I can’t just stand here. I need to go in. I feel like that central temple is calling me, like it knows I’ve come a long way and it’s ready to share something sacred.`
    ]
  },
  {
    id: 'temple_4',
    image: "/yourGame/images/temple/temple_4.jpg",
    texts: [
      `As you step into the temple’s radiant interior, your eyes adjust to the golden light spilling from the central altar. The air is thick with incense and reverence.`,
      //`This place… it’s unlike anything you have ever seen. The light, the carvings, the silence—it feels alive. A robed figure notices your awe and approaches from the column quietly,  their voice calm but warm.`,
      //`Sage: You feel it too. The temple remembers every footstep, every prayer. You’ve come with purpose.`,
      //`You: I was brought here by the portal. I seek guidance… and answers.`,
      //`Sage: Then you must speak with the Head Priest. He sees beyond paths and choices. He listens to the echoes of your spirit. Come—he should doing enchanting his daily prayers. He alone can judge whether you are worthy. I will take you to him.`,
      //`You follow the sage through the circle of quiet figures, heart pounding. Sage pauses at the archway then signals you to step forward.`,
      //`Sage: Head into the room, he should be finished with his prayers soon ... keep quiet until he opens his eyes.`  
    ]
  },
  {
    id: 'temple_5',
    image: "/yourGame/images/temple/temple_5.jpg",
    texts: [
      `You step through the archway… and it’s like the world shifted. You glance back briefly, then forward again, voice hushed with reverence of the word 'OM'.`,
      //`The room isn’t just quiet—it’s sacred. At the center, you see the head priest, seated perfectly still.`
      //`His robes shimmer faintly in the light, and behind him there’s a glowing symbol 'Om' reverbuing with each utterance from the head priest -- it feels alive, like it’s pulsing with energy.`,
      //`You take a slow breath, trying to steady your thoughts.The whole space radiates outward from the head priest, like the temple itself was built around this moment of stillness.`,
      //`The walls, the corridors, even the intricate architecture —they all seem to bow toward him. You lower your breathing even more, almost afraid to disturb the air. You glance at the monk again, still unmoving.`,
      //`Thinking...: The Sage said to wait until he opens his eyes. But, I don’t know why I’m here. Everything feels overwhelming. The temple, the light, the silence… it’s beautiful, but I don’t understand it.`,
      //`The Head Priest finally opens his eyes and smiles at you, his gaze feels warm and inviting almost as if he knew of your arrival.`,
      //`Head Priest: You are not here by accident. Confusion is the first step toward clarity. The path ahead of you is veiled, yes—but it is yours alone to walk.`,
      //`You: (fidgeting slightly) But I didn’t choose this. I was just… drawn here. Everyone seems to know something I don’t.`,
      //`Head Priest: (smiles gently) You were sent, not by chance but by purpose. You are about to start a journey for the good of the world, and the devine offers it's blessing for your journey ahead.`,
      //`You: (softly) Blessing?`,
      //`Head Priest rises slowly and gestures toward the glowing symbol behind him.`, 
      //`Head Priest: Protection. Guidance. The mark of those who walk with light even when the road is dark. You will face trials, yes—but you will not face them alone.`,
      //`You: (eyes widening) So… I’m meant to go?`,
      //`Head Priest: (places a hand over your heart) You were always meant to go. But now, you will go shielded. The divine protection will be your armor when darkeness tires to steer you away from your destiny.`,
      //`You: Thank you.`,
      //`Head Priest: Not yet, follow me you will need to obtain the divine power first… a shield that may preserve your soul.`
    ]
  },
  {
    id: 'temple_6',
    image: "/yourGame/images/temple/temple_6.jpg",
    texts: [
      `The Priest leads you deeper into the sanctum until you reach its innermost chamber. At its center stands a divine altar—a massive statue resembling a Shiva Lingam with a serene face, radiating waves of spiritual energy.`, 
      //`Head Priest: Sit cross-legged before the statue.`,
      //`You sit as the Head Priest instructed and then listen to his instructions carefully.`,
      //`Head Priest: Recite after me, the cosmic mantra. Repeat it 101 times, and you will draw the god’s protection.`,
      //`The chamber hums with resonance as you chant, each syllable vibrating through your bones. Incense burns in sconces, casting swirling shadows.`,
      //`With each repetition a vortex of cosmic light spirals upward, vanishing into infinity. When the vortex of cosmic light slowly fades, you feel a shimmering shield of divine light forming faintly around your body.`, 
      //`Head Priest: (speaks gravely) The protection is yours, but know this—it will shatter after shielding you once from harm.`, 
      //`You: Shatter?`,
      //`Head Priest: Yes, once you encounter a danger this shield will protect you, but then you will loose it. So, do not squander it. Now go the world awaits your next move.`,
      //`You: Where should I go next?`,
      //`Head Priest: Head to the river bank where you will find someone to take you to the Portal Realm. Then you should head to the Luminous Forest. Each realm has its own story and its own mysteries which is for you to explore.`,
      //`The Head Priest: (signals you toward a doorway far away) Head through that door and climb up the hill. When you reach the top you will see the river bank from the top follow the stone steps down to the riverbank, but tread carefully… for dangers lurk in forgotten corners.`,
      //`You thank the Head Priest and make your way towards the door.`
    ],

    // Trigger inventory update via fetch to backend
    action: function () {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first 

      // Wait for delayed pop-up
      this.expectingPopup = true;

      // Call to add the shield to the inventory
      fetch("/yourGame/system/inventory_api.php?action=addShield&realm=temple&item=" + encodeURIComponent("Shiva Kawaj"))
        .then(res => res.text())
        .then(data => {
          const result = data.trim();
          if (result === "Shiva Kawaj"){
            this.popupText = "🌙 Shiva Kawaj received! You feel a protective force surround you.✨";
          }else if (result.includes("already have")){
            this.popupText = result;
          }else{
            this.popupText = "❓ Unexpected response.";
          }

          // Console log for debugging purpose
          console.log("Temple shield addition: ", result); 
          refreshInventoryHUD();
        });
      }
  },
  {
    id: 'temple_7',
    image: "/yourGame/images/temple/temple_7.jpg",
    texts: [
      `Exiting the temple, you climb up the hill. When you reach the top on the other side of the hill below you see yet another temple nestled into the cliffs like it’s grown out of the mountain itself.`,
      //`It’s ancient, but alive—when you look closely… you see glowing embers drifting out from within? They’re not smoke but shimmer like fireflies. They feel warmer and brighter like pieces of light escaping from something sacred inside.`,
      //`And just below it—you see the riverbank. Calm, winding, almost hidden by the jungle. The scene is tranquil—mist rising from the river catching the golden hues of morning sunglight and the orange hues of embers as it settles on the water.`, 
      //`You follow the path along the stones and it leads right to the temple entrance. You pause, eyes locked on the glowing trail of the embers and golden light emitting from the within the temple.`,
      //`Thinking...: The riverbank .....or ...... The Golden Light?`
    ],

    // Present branching choices to the player
    choices: [
      { label: "Explore the light", nextScene: "temple_8" },
      { label: "Head to Riverbank", nextScene: "temple_9" }
    ]
  },
  {
    id: 'temple_8',
    image: "/yourGame/images/temple/temple_8.jpg",
    texts: [
      `You follow the glow to a grand temple carved into the cliffside, its waterfalls cascading like a crown. At its gate burns a glowing blue portal, beckoning you. `,
      //`But as you step forward, a majestic lion emerges, its mane adorned with golden chains and emerald jewels. The lion lowers its head, its eyes blaze with divine fury and roar rumbling like thunder.`,
      //`Without warning, the lion charges, its strike smashing against your shield. The divine protection shatters in a burst of light.`,
      //`Lion: (growling) I am the Temple Guardian. Outsider! this is a sacred place, forbidden to your kind. Were it not for the blessing bestowed upon you by the Gods, your soul would have been turned to dust.`,
      //`You: I am sorry. I didn't mean to cause any trouble.`,
      //`Temple Guardian: I can sense your intension. You shouldn't wander into unknown places as dangers lurks in every corner. I am sorry to have shattered your devine shield but this place holds secrets that can destroy our world in wrong hands.`,
      //`You: No.. No.. its my fault. I didn't heed to the warnings and strayed away. I should leave now.`,
      //`Lion: Before you go. Heed my advice sometimes curosity can lead to unfavourable outcomes. This should be a lesson to remember for you have a very harsh journey ahead.`,
      //`You: Can you tell me more of this journey that everyone know but I.`,
      //`Lion: I am sorry! but I have served my purpose in our destined encountered. You should leave now!`,
      //`Shaken, you turn back and make your way towards the riverbank.`        
    ],

    // Trigger quest and inventory update via fetch to backend
    action: function () {
      if (this.triggered) return; // Get out of the function if true
      this.triggered = true; // Set to true upon first execution

      // Wait for delayed pop-up
      this.expectingPopup = true;

      // Call to update quest flag for hazard encountered
      fetch("/yourGame/system/quest_api.php?action=hazard&realm=temple&item=" + encodeURIComponent("Lion"))
        .then(res => res.text())
        .then(data => {
          const value = data.trim();
          if (!value.includes("1")) {
            this.popupText = "❓ Unexpected response";

            // Console log for debugging purpose
            console.log("Temple hazard faced: ", value);
            refreshInventoryHUD();
          }else{
            // Remove shield only if hazard has been faced
            fetch("/yourGame/system/inventory_api.php?action=removeShield")
              .then(res => res.text())
              .then(data => {
                const result = data.trim();
                if (result.includes("None")) {
                  this.popupText = "💀 The dead do not carry protection. Your soul begins to unravel... ✨";
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
                    this.popupText =`🦁 The Temple Lion shattered your shield. Be careful! you are unprotected now. ✨`;
                } else {
                    this.popupText = "❓ Unexpected response";
                }

                // Console log for debugging purpose
                console.log("Temple shield removal: ", result);
                refreshInventoryHUD();
              });
          }
        });
    }
  },
  {
    id: 'temple_9',
    image: "/yourGame/images/temple/temple_9.jpg",
    texts: [
      `You descend the stone stairs to the riverbank. Mist swirls around the water as the boat glides closer. A mysterious figure in red steps out, cloaked in red. His presence feels heavy and almost divine.`,
      //`Mysterious Figure: So, you have come this far. The gods have tested you as I can see that you carry their mark.`,
      //`You: Who are you?`,
      //`Mysterious Figure (smiling faintly): I am the Guardian of this realm. Your journey in this realm is now complete. You may now return to the Portal Realm.`,
      //`Before you can ask any more question the figure raises a hand, and from the mist behind him the faint shimmer of a  portal begins to glow. The Mysterious Figure then signals you to enter through the portal.`
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

  const sceneId = new URLSearchParams(window.location.search).get("scene");

  // Determine which scene to load by calling the function
  if (sceneId) {
    loadSceneById(sceneId); // Direct scene load
  } else {
      loadSceneById("temple"); // First-time entry
  }
});