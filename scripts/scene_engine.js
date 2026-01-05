
// Track current scene and text index
let sceneIndex = 0;
let textIndex = 0;

// Track popup 
let popupLock = false;

// DOM elements for text and image
const textContent = document.getElementById("textContent");
const nextBtn = document.getElementById("nextBtn");
const realmImage = document.getElementById("realmImage");

/* ---------------------------------------------------------------------------------------------------
    4. TYPEWRITER EFFECT: Called by RENDER SCENE for immersive storytelling
------------------------------------------------------------------------------------------------------*/
function typeText(text, element, button, callback) {
    // If text is empty or invalid, clear the display and re-enables the button immediately.
    if (!text || typeof text !== "string") {
        element.textContent = "";
        button.disabled = false;
        if (callback) callback();
        return;
    }

    // Resets the text content and disable "Next" button to prevent skipping while typing
    let i = 0;
    element.textContent = "";
    button.disabled = true;

    // Create the immersive typewriter effect.
    const interval = setInterval(() => {
        element.textContent += text.charAt(i);
        i++;

        // Once the full line is typed, clear the interval and begin post-typing logic.
        if (i >= text.length) {
            clearInterval(interval);
            const currentScene = scenes[sceneIndex];
            
            // Check if its last line of the scene only the proceed to action and popup handling
            if (textIndex === currentScene.texts.length - 1){ 
                // Set flag to access it to show popup directly
                currentScene.narrationComplete = true;
                
                // Trigger scene action logic after the last line is typed (if any action() present)
                if (currentScene.action && !currentScene.actionTriggered) {
                    currentScene.action();
                    // Ensure it only runs once
                    currentScene.actionTriggered = true;
                }
                
                // If popupText is already set and no async popup expected, show it now
                if (!currentScene.expectingPopup && currentScene.popupText) {
                    showPopupMessage(currentScene.popupText);
                }

                // If the scene is waiting for async popup (e.g. from a fetch), it polls until popupText is set.
                if (currentScene.expectingPopup) {
                    const waitForPopupText = setInterval(() => {
                        if (currentScene.popupText) {
                            clearInterval(waitForPopupText);
                            const nextBtn = document.getElementById("nextBtn");
                            if (nextBtn) {
                                nextBtn.disabled = false;
                                nextBtn.style.display = "";
                                // Simulate "Next" click to re-enter the engine flow and trigger the popup.
                                nextBtn.click();
                            }
                        }
                    }, 100);
                }
            }

            // Prevents the player from clicking "Next" while a popup is active.
            const waitForPopup = () => {
                // Once popup clears, it re-enables the button and calls the optional callback
                if (!popupLock) {
                    button.disabled = false;
                    if (callback) callback();
                } else {
                    setTimeout(waitForPopup, 100); // Poll until popup finishes
                }
            };
            waitForPopup();  
        }
    }, 30);
}

/* ---------------------------------------------------------------------------------------------------
    3. RENDER SCENE: Called by SCENE LOAD that sets image, text, and handles branching or progression
------------------------------------------------------------------------------------------------------*/
function renderScene(scene) {
    // Track current scene and reset text index
    sceneIndex = scenes.findIndex(s => s.id === scene.id);
    
    scene.actionTriggered = false;

    // Reset text index to start narration from beginning
    textIndex = 0;

    // Clear the text box and setup the Next button (elements manipualted by typeText function)
    document.getElementById("textBox").innerHTML = `
    <p id="textContent"></p>
    <button id="nextBtn" disabled>Next</button>
    `;

    // Rebind DOM elements after reset
    const textContent = document.getElementById("textContent");
    const nextBtn = document.getElementById("nextBtn");

    // Handle "Next" button clicks
    nextBtn.onclick = null;
    // Each click advances textIndex and types the next line from scene.texts
    nextBtn.addEventListener("click", () => {
        const scene = scenes[sceneIndex];
        if (textIndex < scene.texts.length - 1) {
            textIndex++;
            typeText(scene.texts[textIndex], textContent, nextBtn);

        } else if (scene.expectingMoreText) {
            // Wait for more narration to be pushed
            nextBtn.disabled = true;
            const waitForText = setInterval(() => {
                if (textIndex < scene.texts.length - 1) {
                    clearInterval(waitForText);
                    textIndex++;
                    typeText(scene.texts[textIndex], textContent, nextBtn);
                } else if (!scene.expectingMoreText) {
                    clearInterval(waitForText);
                    nextBtn.disabled = false;
                }
            }, 100);  

        } else if (scene.expectingPopup) {
            // Wait for any delayed popup to be displayed
            nextBtn.disabled = true;
            let attempts = 0;
            const waitForPopup = setInterval(() => {
                if (scene.popupText) {
                    clearInterval(waitForPopup);
                    showPopupMessage(scene.popupText);
                    scene.expectingPopup = false;
                } else if (!scene.expectingPopup || attempts > 100) { // ~10 seconds max
                    clearInterval(waitForPopup);
                    nextBtn.disabled = false;
                    console.warn("Popup timeout or flag cleared.");
                }
                attempts++;
            }, 100);

        } else if(scene.waitingForChoices){
            // Hide Next button while waiting for popup or async logic to finish
            nextBtn.style.display = "none";
            console.log("Waiting for choices:", scene.waitingForChoices);
            // Delay until choices are ready
            const waitForChoices = setInterval(() => {
                if (!scene.waitingForChoices) {
                    clearInterval(waitForChoices);
                    nextBtn.style.display = "";
                    nextBtn.click();
                }
            }, 100);

        } else if (scene.choices) {
            // Skip showing choices if a redirect is pending
            if (scene.popupOnComplete && typeof scene.popupOnComplete === "function") {
                console.log("Skipping choices due to redirect.");
                return;
            }

            // Branching logic: show choice buttons
            document.getElementById("textBox").innerHTML = scene.choices.map(choice =>
            `<button>${choice.label}</button>`
            ).join("");

            // Bind each button to its target scene
            document.querySelectorAll("#textBox button").forEach((btn, index) => {
                btn.addEventListener("click", () => {

                // Run logic before transitioning (for pulling the lever for gate opening)
                const choice = scene.choices[index];
                if (typeof choice.onSelect === "function") {
                    choice.onSelect();
                }

                // Transition to next scene
                const targetScene = scenes.find(s => s.id === scene.choices[index].nextScene);
                if (targetScene) {
                    renderScene(targetScene);
                } else {
                    console.warn("Scene not found:", scene.choices[index].nextScene);
                }
            });
            });

        } else if (scene.redirect) {
            // Redirect to another page
            window.location.href = scene.redirect;

        } else {
            // Linear progression to next scene
            const nextScene = scenes[sceneIndex + 1];
            if (nextScene) {
                renderScene(nextScene);
            } else {
                // End of realm fallback
                window.location.href = "/yourGame/realms/portal.php";
            }
        }
    });

    // Set background image and start narration
    document.getElementById("realmImage").src = scene.image;
    // Start typing first line and re-enable next button after typing finishes 
    typeText(scene.texts[0], textContent, nextBtn);
}

/* ---------------------------------------------------------------------------------------------------
  POPUP MESSAGE SYSTEM
------------------------------------------------------------------------------------------------------*/
function showPopupMessage(message, duration = 6000, onComplete = null) {
    // Prevent player from clicking Next (checked by typetext function)
    popupLock = true;

    const popup = document.getElementById("popupMessage");
    popup.textContent = message;
    popup.classList.remove("hidden");

    const currentScene = scenes[sceneIndex];
    const finalDuration = duration ?? currentScene?.popupDuration ?? 6000;
    const finalOnComplete = onComplete ?? currentScene?.popupOnComplete ?? null;

    setTimeout(() => {
        popup.classList.add("hidden");
        // Clear flag to enable the Next button (achieved through typetext)
        popupLock = false;

        // Automatically clear waiting flag if scene is active
        if (currentScene) {
            currentScene.popupText = null;
            currentScene.waitingForChoices = false;

            // Trigger nextBtn click to resume flow
            const nextBtn = document.getElementById("nextBtn");
            if (nextBtn) {
                nextBtn.disabled = false;
                nextBtn.style.display = "";
                nextBtn.click(); // Re-enter renderScene flow
            }
        }
        
        // Run redirect or other logic after popup finishes (when onComplete is provided)
        if (typeof finalOnComplete === "function") {
            finalOnComplete();
        }
    }, finalDuration);
}

/* -------------------------------------------------------------------------------------------------------
    2. SCENE LOAD: Gets called by PAGE LOAD that loads scene by ID from URL or flag
    --------------------------------------------------------------------------------------------------------*/
function loadSceneById(sceneId) {
    const scene = scenes.find(s => s.id === sceneId);
    if (!scene) {
        console.warn("Scene not found:", sceneId);
        renderScene(scenes[0]); // fallback
    } else {
        renderScene(scene);
    }
}