// -----------------------------
// 🌟 STATE (declarat top-level, resetat de window.startAnimation)
// -----------------------------
let iconita = 0;
let nr_apelare = 0;
let contor = 0;
let queueIndex = 0;
let isPlaying = false;
let stepRunning = false;
let pauseRequested = false;
let speedMultiplier = 1;
let playGeneration = 0;
let cnt = 0;

let animationQueue = [];   // ⚠️ schimbat din const in let
function S(ms) { return ms / speedMultiplier; }

// -----------------------------
// 🔧 SHOW CIRCLE
// -----------------------------
async function show(i, j, instant = false) {
  if (!instant) stepRunning = true;

  const circle = document.getElementById(`circle-${i}-${j}`);
  if (!circle) return;

  let span = circle.querySelector(".circle_number");
  if (!span) {
    span = document.createElement("span");
    span.className = "circle_number";
    span.style.position = "absolute";
    span.style.transform = "translate(-50%, -45%)";
    span.style.pointerEvents = "none";
    circle.appendChild(span);
  }

  circle.classList.add("show");

  if (!instant) await new Promise(r => setTimeout(r, S(300)));

  const data = window.APP_VARS[cnt] || [];
  span.innerHTML = data.join(",&#8203;");
  cnt++;

  // 🔥 IMPORTANT: reset font înainte de măsurare
 const totalChars = data.join(",").length;

// 🔥 FONT SIZE DINAMIC
let fontSize;

if (totalChars === 1) {
  fontSize = circle_size * 0.55;
}
else if (totalChars === 2) {
  fontSize = circle_size * 0.48;
}
else if (totalChars === 3) {
  fontSize = circle_size * 0.37;
}
else if (totalChars === 4) {
  fontSize = circle_size * 0.32;
}
else if (totalChars === 5) {
  fontSize = circle_size * 0.24;
}
else if (totalChars === 6) {
  fontSize = circle_size * 0.23;
}
else if (totalChars === 7) {
  fontSize = circle_size * 0.22;
}
else if (totalChars === 8) {
  fontSize = circle_size * 0.21;
}
else if (totalChars === 9) {
  fontSize = circle_size * 0.20;
}
else if (totalChars === 10) {
  fontSize = circle_size * 0.19;
}
else if (totalChars === 11) {
  fontSize = circle_size * 0.16;
}
else if (totalChars === 12) {
  fontSize = circle_size * 0.15;
}
else if (totalChars === 13) {
  fontSize = circle_size * 0.14;
}
else if (totalChars === 14) {
  fontSize = circle_size * 0.13;
}
else if (totalChars === 15) {
  fontSize = circle_size * 0.13;
}
else if (totalChars === 16) {
  fontSize = circle_size * 0.12;
}
else if (totalChars === 17) {
  fontSize = circle_size * 0.11;
}
else if (totalChars === 18) {
  fontSize = circle_size * 0.10;
}
else if (totalChars === 19) {
  fontSize = circle_size * 0.09;
}
else if (totalChars === 20) {
  fontSize = circle_size * 0.08;
}
else {
  fontSize = circle_size * 0.07;
}

// safety minimum
fontSize = Math.max(fontSize, 8);
  span.style.fontSize = fontSize + "px";
  span.style.fontFamily = "Inter, sans-serif";

  span.style.display = "flex";
  span.style.flexDirection = "row";
  span.style.flexWrap = "wrap";
  span.style.justifyContent = "center";
  span.style.alignItems = "center";
  span.style.textAlign = "center";
  span.style.width = "90%";
  span.style.height = "90%";
  span.style.lineHeight = "1";
  span.style.gap = "3px";

  span.style.whiteSpace = "normal";
span.style.wordBreak = "keep-all";
span.style.overflowWrap = "normal";
  // 🔥 WAIT DOM UPDATE (foarte important pentru măsurare corectă)
  if (!instant) {
    await new Promise(r => requestAnimationFrame(r));
  }

  // 🔥 SHRINK DOAR CÂT TREBUIE (nu mai pornești mic)
  

  nr_apelare++;

  span.dataset.stepIndex = queueIndex;
  circle.dataset.stepIndex = queueIndex;

  if (!instant) stepRunning = false;
}

// -----------------------------
// 🔧 LINE
// -----------------------------
function animateLineWithEvent(x1, y1, x2, y2, instant = false) {
  if (!instant) stepRunning = true;
  return new Promise((resolve) => {
    const line = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
    line.setAttribute("points", `${x1},${y1} ${x2},${y2}`);
    line.setAttribute("fill", "none");
    line.setAttribute("stroke", "#ffffffce");
    line.setAttribute("stroke-width", line_width);
    line.dataset.stepIndex = queueIndex;

    const svg = document.getElementById("svg-lines");
    svg.appendChild(line);

    if (instant) { return resolve(); }

    const length = line.getTotalLength();
    line.style.strokeDasharray = length;
    line.style.strokeDashoffset = length;
    line.getBoundingClientRect();
    line.style.transition = `stroke-dashoffset ${0.5 / speedMultiplier}s linear`;
    line.style.strokeDashoffset = 0;

    line.addEventListener("transitionend", () => {
      stepRunning = false;
      resolve();
    }, { once: true });
  });
}

// -----------------------------
// 🔧 ARROW
// -----------------------------
async function animateArrow(x1, y1, x2, y2, value, fontSize, instant = false, fadeDuration = 700, holdDuration = 300) {
  if (!instant) stepRunning = true;
  const svg = document.getElementById("svg-lines");

  const midX = (x1 + x2) / 2;
  const midY = (y1 + y2) / 2;
  const dx = x2 - x1;
  const dy = y2 - y1;
  const angle = Math.atan2(dy, dx);

  const tipX = midX + triunghi_latime * Math.cos(angle);
  const tipY = midY + triunghi_latime * Math.sin(angle);
  const baseX = midX - triunghi_latime * Math.cos(angle);
  const baseY = midY - triunghi_latime * Math.sin(angle);
  const leftX = baseX + triunghi_larg * Math.sin(angle);
  const leftY = baseY - triunghi_larg * Math.cos(angle);
  const rightX = baseX - triunghi_larg * Math.sin(angle);
  const rightY = baseY + triunghi_larg * Math.cos(angle);

  const triangle = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
  triangle.setAttribute("points", `${tipX},${tipY} ${leftX},${leftY} ${rightX},${rightY}`);
  triangle.setAttribute("fill", "#133168");
  triangle.setAttribute("stroke", "#005cd4");
  triangle.setAttribute("stroke-width", "1");
  triangle.setAttribute("opacity", 0);
  triangle.style.transition = `opacity ${fadeDuration / speedMultiplier}ms ease`;
  triangle.dataset.stepIndex = queueIndex;
  svg.appendChild(triangle);
  triangle.getBoundingClientRect();
  triangle.setAttribute("opacity", 1);

  if (value !== null) {
    const label = document.createElementNS("http://www.w3.org/2000/svg", "text");
    label.textContent = value.toString();
    label.setAttribute("font-size", fontSize);
    label.setAttribute("fill", "#d800d8");

    const length = Math.sqrt(dx * dx + dy * dy);
    let px = -dy / length;
    let py = dx / length;

    const gap = circle_size * 0.30;
    if (dx > 0) { px *= -gap; py *= -gap; } else { px *= gap; py *= gap; }

    label.setAttribute("x", midX + px);
    label.setAttribute("y", midY + py);
    label.setAttribute("opacity", 0);
    label.style.transition = `opacity ${fadeDuration / speedMultiplier}ms ease`;
    label.dataset.stepIndex = queueIndex;
    svg.appendChild(label);
    label.getBoundingClientRect();
    label.setAttribute("opacity", 1);
  }

  if (!instant) {
    await new Promise(r => setTimeout(r, S(fadeDuration + holdDuration)));
    stepRunning = false;
  }
}

// -----------------------------
// 🔧 FINAL NUMBER
// -----------------------------
async function showFinalNumber(value, instant = false) {
  if (!instant) stepRunning = true;

  const circle = document.getElementById("circle-0-0");
  if (!circle) { if (!instant) stepRunning = false; return; }

  if (getComputedStyle(circle).position === "static") {
    circle.style.position = "relative";
  }

  const old = circle.querySelector(".final-number-label");
  if (old) old.remove();

  const label = document.createElement("div");
  label.className = "final-number-label";
  label.textContent = window.REVERSED_NUMBERS[window.REVERSED_NUMBERS.length - 1];
  label.dataset.stepIndex = queueIndex;

  label.style.position = "absolute";
  label.style.left = "50%";
  label.style.bottom = `calc(100% + ${circle_size * 0.1}px)`;
  label.style.transform = "translateX(-50%)";
  label.style.pointerEvents = "none";
  label.style.whiteSpace = "nowrap";
  label.style.fontWeight = "bold";
  label.style.color = "#d80068";
  label.style.fontSize = (circle_size * 0.5) + "px";

  circle.appendChild(label);

  if (instant) {
    label.style.opacity = 1;
    return;
  }

  label.style.opacity = 0;
  label.style.transition = `opacity ${500 / speedMultiplier}ms ease`;
  label.getBoundingClientRect();
  label.style.opacity = 1;

  await new Promise(r => setTimeout(r, S(500 + 200)));
  stepRunning = false;
}

// -----------------------------
// ⏱ TIMELINE CORE
// -----------------------------
function pushShow(i, j) { animationQueue.push({ type: "show", params: { i, j } }); }
function pushLine(x1, y1, x2, y2) { animationQueue.push({ type: "line", params: { x1, y1, x2, y2 } }); }
function pushArrow(x1, y1, x2, y2, value, fontSize) { animationQueue.push({ type: "arrow", params: { x1, y1, x2, y2, value, fontSize } }); }
function pushFinalNumber(value) { animationQueue.push({ type: "finalNumber", params: { value } }); }

async function executeStep(step, instant = false) {
  const p = step.params;
  switch (step.type) {
    case "show": await show(p.i, p.j, instant); break;
    case "line": await animateLineWithEvent(p.x1, p.y1, p.x2, p.y2, instant); break;
    case "arrow": await animateArrow(p.x1, p.y1, p.x2, p.y2, p.value, p.fontSize, instant); break;
    case "finalNumber": await showFinalNumber(p.value, instant); break;
  }
}

// -----------------------------
// 🔙 UNDO STEP
// -----------------------------
function undoStep(step) {
  const svg = document.getElementById("svg-lines");
  switch (step.type) {
    case "show":
      const circle = document.getElementById(`circle-${step.params.i}-${step.params.j}`);
      if (circle) {
        circle.classList.remove("show");
        const span = circle.querySelector(".circle_number");
        if (span && span.dataset.stepIndex == queueIndex) span.remove();
        if (circle.dataset.stepIndex == queueIndex) delete circle.dataset.stepIndex;
      }
      nr_apelare--;
      cnt--;
      break;
    case "line":
    case "arrow":
      const elements = svg.querySelectorAll(`[data-step-index="${queueIndex}"]`);
      elements.forEach(el => el.remove());
      if (step.type === "arrow") contor--;
      break;
    case "finalNumber":
      const firstCircle = document.getElementById("circle-0-0");
      if (firstCircle) {
        const fn = firstCircle.querySelector(`.final-number-label[data-step-index="${queueIndex}"]`);
        if (fn) fn.remove();
      }
      break;
  }
}

// -----------------------------
// 🎮 CONTROLS
// -----------------------------
async function nextStep() {
  if (stepRunning) await new Promise(r => {
    const check = () => (!stepRunning ? r() : requestAnimationFrame(check));
    check();
  });

  if (queueIndex >= animationQueue.length) return;

  await executeStep(animationQueue[queueIndex]);
  queueIndex++;
  updateTimelineUI();

  if (pauseRequested) {
    isPlaying = false;
    pauseRequested = false;
  }
}

const icon = document.getElementById("icon");

async function play() {
  icon.classList.remove("pause-icon");
  icon.classList.add("play-icon");
  if (isPlaying) return;
  isPlaying = true;

  const myGeneration = playGeneration;  // 🔑 capturam generatia curenta

  while (queueIndex < animationQueue.length && isPlaying) {
    if (myGeneration !== playGeneration) return;  // 🔑 oprire daca s-a schimbat generatia
    await nextStep();
  }
  isPlaying = false;
}

async function pause() {
  icon.classList.remove("play-icon");
  icon.classList.add("pause-icon");
  if (stepRunning) {
    pauseRequested = true;
    await new Promise(r => {
      const check = () => (!stepRunning ? r() : requestAnimationFrame(check));
      check();
    });
  }
  isPlaying = false;
}

// -----------------------------
// 🎯 JUMP
// -----------------------------
async function jumpToIndex(target) {
  if (stepRunning) await new Promise(r => {
    const check = () => (!stepRunning ? r() : requestAnimationFrame(check));
    check();
  });

  if (target > queueIndex) {
    // === TELEPORTARE RAPIDĂ (ca la backward) ===
    while (queueIndex < target) {
      executeStep(animationQueue[queueIndex], true);   // ← fără await !
      queueIndex++;
    }
  } else {
    while (queueIndex > target) {
      queueIndex--;
      undoStep(animationQueue[queueIndex]);
    }
  }
  updateTimelineUI();
}
async function jumpToCircle(circleNumber) {
  await pause();
  let count = 0;
  let targetIndex = 0;
  for (let i = 0; i < animationQueue.length; i++) {
    if (animationQueue[i].type === "show") count++;
    if (count === circleNumber) {
      targetIndex = i + 1;
      break;
    }
  }
  await jumpToIndex(targetIndex);
}

// -----------------------------
// 🎞 TIMELINE BAR
// -----------------------------
const timelineBar = document.getElementById("timelineBar");
const timelineLabel = document.getElementById("timelineLabel");

function setTimelineFill() {
  const max = parseFloat(timelineBar.max) || 0;
  const val = parseFloat(timelineBar.value) || 0;
  const pct = max > 0 ? (val / max) * 100 : 0;
  timelineBar.style.setProperty("--tl-fill", pct + "%");
}
function updateTimelineMax() { timelineBar.max = animationQueue.length; setTimelineFill(); }
function updateTimelineUI() { timelineBar.value = queueIndex; timelineLabel.innerText = `${queueIndex} / ${animationQueue.length}`; setTimelineFill(); }

setTimelineFill();

timelineBar.addEventListener("input", async () => {
  setTimelineFill();
  const target = parseInt(timelineBar.value);
  if (isPlaying) await pause();
  if (stepRunning) {
    await new Promise(r => {
      const check = () => (!stepRunning ? r() : requestAnimationFrame(check));
      check();
    });
  }
  isPlaying = false;
  pauseRequested = false;
  stepRunning = false;
  await jumpToIndex(target);
});

// -----------------------------
// 🛠 PREPARE TIMELINE
// -----------------------------
function prepareTimeline() {
  let ult_poz_x = drawing_x[0][0];
  let ult_poz_y = drawing_y[0][0];
  let i = 0, j;

  progresare_cnt--;
  anime[0][0] = 1;

  let ver_descrestere = 0;
  let ultima_valoare = 0;
  let poz_ultima_curba_x, poz_ultima_curba_y;

  pushShow(0, 0);

  for (let k = 0; k < progresare_cnt; k++) {
    if (progresare[k] == 1) {
      if (ver_descrestere) {
        ultima_valoare = 0;
        anime[i][j]++;
        ver_descrestere = 0;
      }
      i++;
      j = 0;
      while (anime[i][j] == -1 || anime[i][j] >= matrice_comparatie[i][j]) j++;
      anime[i][j]++;
      const x = drawing_x[i][j];
      const y = drawing_y[i][j];
      pushLine(ult_poz_x, ult_poz_y, x, y);
      pushShow(i, j);
      ult_poz_x = x;
      ult_poz_y = y;
    } else {
      i--;
      j = 0;
      while (anime[i][j] >= matrice_comparatie[i][j]) j++;
      let var_curba = 0;
      while (matrice_curbe[i][var_curba] >= matrix2[i][var_curba]) var_curba++;
      matrice_curbe[i][var_curba]++;
      if (!ultima_valoare) {
        poz_ultima_curba_x = ult_poz_x;
        poz_ultima_curba_y = ult_poz_y;
        ultima_valoare = 1;
      }
      const x = drawing_x[i][var_curba];
      const y = drawing_y[i][var_curba];

      const isVoid = (typeof returnari === "undefined" || !returnari || returnari.length === 0);
      if (!isVoid) {
        pushArrow(poz_ultima_curba_x, poz_ultima_curba_y, x, y, returnari[contor], circle_size / 2);
        contor++;
      }

      poz_ultima_curba_x = x;
      poz_ultima_curba_y = y;
      ult_poz_x = drawing_x[i][j];
      ult_poz_y = drawing_y[i][j];
      ver_descrestere = 1;
    }
  }
  pushFinalNumber(progresare[progresare.length - 1]);
}

// -----------------------------
// ⌨️ KEYBOARD CONTROLS (atasat o singura data)
// -----------------------------
document.addEventListener("keydown", async (e) => {
  const active = document.activeElement;
  const tag = active?.tagName;
  const type = active?.type;

  // Permitem Space și pe timeline (range) și pe butoane
  const isTextInput = (tag === "TEXTAREA") || 
                      (tag === "INPUT" && type !== "range" && type !== "button");

  if (isTextInput || active?.isContentEditable) return;

  if (e.code === "Space") {
    e.preventDefault();
    
    // Scoatem focus-ul de pe slider/butoane ca să nu mai apară probleme
    if (active && active !== document.body) {
      active.blur();
    }

    if (isPlaying) await pause();
    else await play();
  } 
  else if (e.code === "ArrowRight") {
    e.preventDefault();
    await pause();
    if (stepRunning) await new Promise(r => { 
      const check = () => (!stepRunning ? r() : requestAnimationFrame(check)); 
      check(); 
    });
    if (queueIndex >= animationQueue.length) return;
    await executeStep(animationQueue[queueIndex], true);
    queueIndex++;
    updateTimelineUI();
  } 
  else if (e.code === "ArrowLeft") {
    e.preventDefault();
    await pause();
    if (queueIndex > 0) {
      queueIndex--;
      undoStep(animationQueue[queueIndex]);
      updateTimelineUI();
    }
  }
});

// -----------------------------
// 🎛 BUTTONS (atasati o singura data)
// -----------------------------
const btnPrev = document.getElementById("prevStep");
const btnPlayPause = document.getElementById("playPause");
const btnNext = document.getElementById("nextStep");

btnPrev.addEventListener("click", async () => {
  await pause();
  if (queueIndex > 0) {
    queueIndex--;
    undoStep(animationQueue[queueIndex]);
    updateTimelineUI();
  }
});

btnPlayPause.addEventListener("click", async () => {
  if (isPlaying) await pause();
  else await play();
});

btnNext.addEventListener("click", async () => {
  await pause();
  if (stepRunning) await new Promise(r => { const check = () => (!stepRunning ? r() : requestAnimationFrame(check)); check(); });
  if (queueIndex >= animationQueue.length) return;
  await executeStep(animationQueue[queueIndex], true);
  queueIndex++;
  updateTimelineUI();
});

function verifyPause() { if (isPlaying) icon.classList.className = "pause-icon"; }
function verifyPlay() { if (!isPlaying) icon.classList.className = "play-icon"; }

const timeline = document.getElementById("timelineContainer");
const btnHideTimeline = document.getElementById("toggleTimelineVisibility");
const btnShrinkTimeline = document.getElementById("toggleTimeline");

let timelineVisible = true;
let timelineShrunk = false;

function pressButtonEffect(button) {
  if (!button) return;
  button.classList.add("button-pressed");
  setTimeout(() => button.classList.remove("button-pressed"), 120);
}

function toggleTimelineVisibility() {
  pressButtonEffect(btnHideTimeline);
  setTimeout(() => {
    timelineVisible = !timelineVisible;
    if (timelineVisible) { timeline.style.display = "flex"; btnHideTimeline.innerText = "Hide Timeline"; }
    else { timeline.style.display = "none"; btnHideTimeline.innerText = "Show Timeline"; }
  }, 120);
}

function toggleTimeline() {
  pressButtonEffect(btnShrinkTimeline);
  setTimeout(() => {
    timelineShrunk = !timelineShrunk;
    if (timelineShrunk) { timeline.classList.add("collapsed"); btnShrinkTimeline.innerText = "Expand Timeline"; }
    else { timeline.classList.remove("collapsed"); btnShrinkTimeline.innerText = "Shrink Timeline"; }
  }, 120);
}

// Ascunde/afiseaza butoanele de zoom (checkbox din filtrul de navbar)
function toggleZoomControls(cb) {
  const zc = document.getElementById("zoomControls");
  if (!zc) return;
  zc.style.display = cb.checked ? "none" : "";
}

let TimeSpeedupArray = [1, 2, 3, 4, 5];
let speedIdx = 1;

function plus_button() {
  if (speedIdx < 5) speedIdx++;
  else speedIdx = 1;
  document.getElementById("speedup_text").textContent = TimeSpeedupArray[speedIdx - 1];
  speedMultiplier = TimeSpeedupArray[speedIdx - 1];
}

function minus_button() {
  if (speedIdx > 1) speedIdx--;
  else speedIdx = 5;
  document.getElementById("speedup_text").textContent = TimeSpeedupArray[speedIdx - 1];
  speedMultiplier = TimeSpeedupArray[speedIdx - 1];
}

// -----------------------------
// ✅ START ANIMATION (apelat la load + dupa fiecare Execute AJAX)
// -----------------------------
window.startAnimation = function () {
  playGeneration++;   // 🔑 invalideaza orice play() vechi

  // Reset state
  nr_apelare = 0;
  contor = 0;
  queueIndex = 0;
  cnt = 0;
  iconita = 0;
  isPlaying = false;
  stepRunning = false;
  pauseRequested = false;
  animationQueue = [];

  if (icon) {
    icon.classList.remove("pause-icon");
    icon.classList.add("play-icon");
  }

  prepareTimeline();
  updateTimelineMax();
  updateTimelineUI();
  play();
};