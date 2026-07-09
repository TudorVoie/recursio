window.addEventListener('DOMContentLoaded', () => {

  // ---------- DRAGBOX ----------
  const dragBox = document.getElementById("dragBox");
  let offsetX = 0, offsetY = 0, startX = 0, startY = 0;

  dragBox.style.position = 'fixed';

if (!window.IS_MOBILE) {
    dragBox.style.top = '100px';
    dragBox.style.left = '100px';
    dragBox.style.transform = 'none';
}
  

  const target = dragBox;

  target.addEventListener('mousedown', (e) => {
    e.stopPropagation();

    const rect = dragBox.getBoundingClientRect();
    const cornerSize = 15;

    const onResizeCorner =
      e.clientX >= rect.right - cornerSize &&
      e.clientY >= rect.bottom - cornerSize;

    const tag = e.target.tagName.toLowerCase();
    const interactive = ['input', 'textarea', 'select', 'button'].includes(tag);

    if (onResizeCorner || interactive) return;

    e.preventDefault();

    startX = e.clientX;
    startY = e.clientY;

    offsetX = parseFloat(dragBox.style.left);
    offsetY = parseFloat(dragBox.style.top);

    document.onmousemove = dragMove;
    document.onmouseup = dragEnd;
  });

  function dragMove(e) {
    const dx = e.clientX - startX;
    const dy = e.clientY - startY;

    dragBox.style.left = offsetX + dx + "px";
    dragBox.style.top = offsetY + dy + "px";
  }

  function dragEnd() {
    document.onmousemove = null;
    document.onmouseup = null;
  }

  // ---------- PAGE ZOOM ----------
  const pageContent = document.getElementById("pageContent");
  const zoomInBtn = document.getElementById("zoomIn");
  const zoomOutBtn = document.getElementById("zoomOut");
  const zoomBox = document.getElementById("zoomBox");

  let pageZoomPercent = 100;
  const zoomStep = 15, zoomMin = 100, zoomMax = 300;

  let naturalW = 0, naturalH = 0;

  function captureNaturalSize() {
    pageContent.style.zoom = '1';
    pageContent.style.width = '';
    pageContent.style.height = '';
    void pageContent.offsetWidth;
    naturalW = pageContent.scrollWidth;
    naturalH = pageContent.scrollHeight;
  }

  function getScroller() {
    return document.querySelector('.viewport') 
        || document.querySelector('#pageContent')?.parentElement
        || document.scrollingElement 
        || document.documentElement;
  }

  let isPanning = false;

  function clampScroll() {
    if (isPanning) return;

    const scroller = getScroller();
    if (!scroller || !naturalW || !naturalH) return;

    const scale = pageZoomPercent / 100;
    const contentW = naturalW * scale;
    const contentH = naturalH * scale;

    const maxX = Math.max(0, Math.floor(contentW) + 25 - scroller.clientWidth);
    const maxY = Math.max(0, Math.floor(contentH) + 25 - scroller.clientHeight);

    scroller.scrollLeft = Math.max(0, Math.min(maxX, scroller.scrollLeft));
    scroller.scrollTop  = Math.max(0, Math.min(maxY, scroller.scrollTop));
  }

  function applyZoom() {
    const scale = pageZoomPercent / 100;

    if (scale === 1) {
      pageContent.style.zoom = '1';
      pageContent.style.width = '';
      pageContent.style.height = '';
    } else {
      if (!naturalW || !naturalH) captureNaturalSize();
      pageContent.style.zoom = scale;
      pageContent.style.width = '';
      pageContent.style.height = '';
    }

    if (zoomBox) zoomBox.innerText = pageZoomPercent + "%";
    zoomInBtn.disabled = pageZoomPercent >= zoomMax;
    zoomOutBtn.disabled = pageZoomPercent <= zoomMin;
  }

  function zoomTo(newPercent) {
    newPercent = Math.max(zoomMin, Math.min(zoomMax, newPercent));
    if (newPercent === pageZoomPercent) return;

    const oldScale = pageZoomPercent / 100;
    const newScale = newPercent / 100;

    const scroller = getScroller();
    const vpW = scroller.clientWidth;
    const vpH = scroller.clientHeight;

    const scrollX = scroller.scrollLeft;
    const scrollY = scroller.scrollTop;

    const unscaledCenterX = (scrollX + vpW / 2) / oldScale;
    const unscaledCenterY = (scrollY + vpH / 2) / oldScale;

    pageZoomPercent = newPercent;
    applyZoom();
    clampScroll();

    const newScrollX = unscaledCenterX * newScale - vpW / 2;
    const newScrollY = unscaledCenterY * newScale - vpH / 2;

    const contentWidth  = naturalW * newScale;
    const contentHeight = naturalH * newScale;

    const maxX = Math.max(0, Math.floor(contentWidth) + 25 - scroller.clientWidth);
    const maxY = Math.max(0, Math.floor(contentHeight) + 25 - scroller.clientHeight);

    scroller.scrollLeft = Math.max(0, Math.min(maxX, newScrollX));
    scroller.scrollTop  = Math.max(0, Math.min(maxY, newScrollY));
  }

  zoomInBtn.addEventListener('click', () => zoomTo(pageZoomPercent + zoomStep));
  zoomOutBtn.addEventListener('click', () => zoomTo(pageZoomPercent - zoomStep));

  pageContent.addEventListener('wheel', (e) => {
    if (!e.ctrlKey) return;
    e.preventDefault();

    const oldScale = pageZoomPercent / 100;
    const delta = e.deltaY < 0 ? zoomStep : -zoomStep;
    const newPercent = Math.max(zoomMin, Math.min(zoomMax, pageZoomPercent + delta));
    if (newPercent === pageZoomPercent) return;
    const newScale = newPercent / 100;

    const scroller = getScroller();
    const unscaledX = (scroller.scrollLeft + e.clientX) / oldScale;
    const unscaledY = (scroller.scrollTop + e.clientY) / oldScale;

    pageZoomPercent = newPercent;
    applyZoom();

    const contentWidth  = naturalW * newScale;
    const contentHeight = naturalH * newScale;

    const maxX = Math.max(0, Math.floor(contentWidth) + 25 - scroller.clientWidth);
    const maxY = Math.max(0, Math.floor(contentHeight) + 25 - scroller.clientHeight);

    scroller.scrollLeft = Math.max(0, Math.min(maxX, unscaledX * newScale - e.clientX));
    scroller.scrollTop  = Math.max(0, Math.min(maxY, unscaledY * newScale - e.clientY));

    clampScroll();
  }, { passive: false });

  setTimeout(() => {
    captureNaturalSize();
    applyZoom();
    clampScroll();
  }, 100);

  // Clamp la scroll (dar nu când facem pan cu mouse-ul)
  const scrollerEl = getScroller();
  if (scrollerEl) {
    scrollerEl.addEventListener('scroll', () => {
      if (!isPanning) clampScroll();
    }, { passive: true });
  }

  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      const cur = pageZoomPercent;
      pageZoomPercent = 100;
      applyZoom();
      naturalW = naturalH = 0;
      captureNaturalSize();
      pageZoomPercent = cur;
      applyZoom();
      clampScroll();
    }, 200);
  });

  // ================= MOBIL: PINCH-ZOOM + PAN (orice direcție) =================
  if (window.IS_MOBILE) {
    const viewport = pageContent;                             // cadrul fix (overflow:auto) = scroller-ul real
    const content = document.getElementById('container');    // scalăm DOAR conținutul, nu cadrul fix

    const M_MIN = zoomMin;   // 100 — aceeași limită ca la desktop
    const M_MAX = zoomMax;   // 300
    let mZoom = 100;         // zoom curent pe mobil (%), independent de logica desktop

    function mScale() { return mZoom / 100; }

    function applyMobileZoom() {
      // `zoom` pe #container creează overflow real în #pageContent => scroll nativ + calcul corect al limitelor
      content.style.zoom = mScale();
    }

    function maxScroll() {
      return {
        x: Math.max(0, viewport.scrollWidth - viewport.clientWidth),
        y: Math.max(0, viewport.scrollHeight - viewport.clientHeight)
      };
    }

    function clampMobileScroll() {
      const m = maxScroll();
      viewport.scrollLeft = Math.max(0, Math.min(m.x, viewport.scrollLeft));
      viewport.scrollTop = Math.max(0, Math.min(m.y, viewport.scrollTop));
    }

    function touchDist(t) {
      return Math.hypot(t[0].clientX - t[1].clientX, t[0].clientY - t[1].clientY);
    }
    function touchMid(t) {
      return { x: (t[0].clientX + t[1].clientX) / 2, y: (t[0].clientY + t[1].clientY) / 2 };
    }

    // ---- stare gesturi ----
    let mode = null;                       // 'pan' | 'pinch'
    let panStart = null;                   // {x, y}
    let scrollStart = { x: 0, y: 0 };
    let pinchStartDist = 0;
    let pinchStartZoom = 100;
    let anchor = { x: 0, y: 0 };           // punct din conținut ancorat sub mijlocul degetelor (necalibrat)

    viewport.addEventListener('touchstart', (e) => {
      if (e.touches.length === 2) {
        e.preventDefault();
        mode = 'pinch';
        const rect = viewport.getBoundingClientRect();
        const m = touchMid(e.touches);
        pinchStartDist = touchDist(e.touches);
        pinchStartZoom = mZoom;
        // punctul din conținut aflat sub mijlocul degetelor, în coordonate necalibrate
        anchor.x = (viewport.scrollLeft + (m.x - rect.left)) / mScale();
        anchor.y = (viewport.scrollTop + (m.y - rect.top)) / mScale();
      } else if (e.touches.length === 1) {
        mode = 'pan';
        panStart = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        scrollStart = { x: viewport.scrollLeft, y: viewport.scrollTop };
      }
    }, { passive: false });

    viewport.addEventListener('touchmove', (e) => {
      if (mode === 'pinch' && e.touches.length === 2) {
        e.preventDefault();
        const rect = viewport.getBoundingClientRect();
        const m = touchMid(e.touches);

        const ratio = touchDist(e.touches) / (pinchStartDist || 1);
        mZoom = Math.max(M_MIN, Math.min(M_MAX, pinchStartZoom * ratio));
        applyMobileZoom();

        // menținem punctul ancoră fix sub mijlocul (curent) al degetelor => zoom centrat pe degete
        const s = mScale();
        viewport.scrollLeft = anchor.x * s - (m.x - rect.left);
        viewport.scrollTop = anchor.y * s - (m.y - rect.top);
        clampMobileScroll();
      } else if (mode === 'pan' && e.touches.length === 1) {
        // pan în ORICE direcție (dx, dy independente => stânga-dreapta-sus-jos-diagonală)
        const m = maxScroll();
        if (m.x === 0 && m.y === 0) return;   // la 100% conținutul încape, nu e nimic de mișcat
        e.preventDefault();
        const dx = e.touches[0].clientX - panStart.x;
        const dy = e.touches[0].clientY - panStart.y;
        viewport.scrollLeft = Math.max(0, Math.min(m.x, scrollStart.x - dx));
        viewport.scrollTop = Math.max(0, Math.min(m.y, scrollStart.y - dy));
      }
    }, { passive: false });

    function endGesture(e) {
      if (e.touches.length === 0) {
        mode = null;
        panStart = null;
      } else if (e.touches.length === 1) {
        // trecere lină din pinch în pan când ridici un deget
        mode = 'pan';
        panStart = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        scrollStart = { x: viewport.scrollLeft, y: viewport.scrollTop };
      }
    }
    viewport.addEventListener('touchend', endGesture, { passive: false });
    viewport.addEventListener('touchcancel', endGesture, { passive: false });

    // resetăm zoom-ul/pan-ul la 100% înainte de fiecare (re)generare a arborelui,
    // ca measure-urile din generateAnimation să se facă pe conținut nescalat
    window.__resetMobileView = () => {
      mZoom = 100;
      applyMobileZoom();
      viewport.scrollLeft = 0;
      viewport.scrollTop = 0;
    };
  }

  // ---------- DRAG PAGE WHEN ZOOMED (PAN CU MOUSE) ----------
  let isDragging = false;
  let dragStartX, dragStartY;
  let scrollLeftStart, scrollTopStart;

  document.addEventListener("mousedown", (e) => {
    if (pageZoomPercent <= 100) return;
    if (e.target.closest("#dragBox")) return;

    isDragging = true;
    isPanning = true;

    dragStartX = e.clientX;
    dragStartY = e.clientY;

    const scroller = getScroller();
    scrollLeftStart = scroller.scrollLeft;
    scrollTopStart  = scroller.scrollTop;

    document.body.style.cursor = "grabbing";
    e.preventDefault();
  });

  document.addEventListener("mousemove", (e) => {
    if (!isDragging) return;

    const dx = e.clientX - dragStartX;
    const dy = e.clientY - dragStartY;

    const scroller = getScroller();
    const scale = pageZoomPercent / 100;

    const contentWidth  = naturalW * scale;
    const contentHeight = naturalH * scale;

    // Aceeași marjă de +25px ca la scroll normal
    const maxX = Math.max(0, Math.floor(contentWidth) + 25 - scroller.clientWidth);
    const maxY = Math.max(0, Math.floor(contentHeight) + 25 - scroller.clientHeight);

    let newX = scrollLeftStart - dx;
    let newY = scrollTopStart  - dy;

    newX = Math.max(0, Math.min(maxX, newX));
    newY = Math.max(0, Math.min(maxY, newY));

    scroller.scrollLeft = newX;
    scroller.scrollTop  = newY;
  });

  document.addEventListener("mouseup", () => {
    isDragging = false;
    isPanning = false;
    document.body.style.cursor = "default";
  });

  pageContent.addEventListener("mouseleave", () => {
    isDragging = false;
    isPanning = false;
    pageContent.style.cursor = "grab";
  });

});

// ---------- TOGGLE DRAGBOX ----------
const toggleBtn = document.getElementById("toggleDragBox");
const dragBox = document.getElementById("dragBox");

toggleBtn.addEventListener("click", () => {
  if (dragBox.style.display === "none") {
    dragBox.style.display = "flex";
  } else {
    dragBox.style.display = "none";
  }
});

// ---------- SIDENAV ----------
function toggleNav() {
  const nav = document.getElementById("mySidenav");

  if (nav.style.width === "15vw") {
    nav.style.width = "0";
  } else {
    nav.style.width = "15vw";
  }
}