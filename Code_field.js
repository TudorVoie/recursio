window.addEventListener('DOMContentLoaded', () => {

  // ---------- DRAGBOX ----------
  const dragBox = document.getElementById("dragBox");
  let offsetX = 0, offsetY = 0, startX = 0, startY = 0;

  dragBox.style.position = 'fixed';
  dragBox.style.top = '100px';
  dragBox.style.left = '100px';

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
    pageContent.style.transform = 'none';
    pageContent.style.width = '';
    pageContent.style.height = '';
    void pageContent.offsetWidth;
    naturalW = pageContent.scrollWidth;
    naturalH = pageContent.scrollHeight;
  }

  function getScroller() {
    return document.querySelector('.custom_scroll_wrapper') 
        || document.scrollingElement;
  }

  function applyZoom() {
    const scale = pageZoomPercent / 100;

    if (scale === 1) {
      pageContent.style.transform = 'none';
      pageContent.style.width = '';
      pageContent.style.height = '';
    } else {
      if (!naturalW || !naturalH) captureNaturalSize();
      pageContent.style.transform = `scale(${scale})`;
      pageContent.style.transformOrigin = "top left";
      pageContent.style.width  = (naturalW * scale) + 'px';
      pageContent.style.height = (naturalH * scale) + 'px';
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

    const vpW = window.innerWidth;
    const vpH = window.innerHeight;

    const scroller = getScroller();
    const scrollX = scroller.scrollLeft;
    const scrollY = scroller.scrollTop;

    const unscaledCenterX = (scrollX + vpW / 2) / oldScale;
    const unscaledCenterY = (scrollY + vpH / 2) / oldScale;

    pageZoomPercent = newPercent;
    applyZoom();

    const newScrollX = unscaledCenterX * newScale - vpW / 2;
    const newScrollY = unscaledCenterY * newScale - vpH / 2;

    const contentWidth  = naturalW * newScale;
    const contentHeight = naturalH * newScale;

    const maxX = contentWidth - scroller.clientWidth;
    const maxY = contentHeight - scroller.clientHeight;

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

    const maxX = contentWidth - scroller.clientWidth;
    const maxY = contentHeight - scroller.clientHeight;

    scroller.scrollLeft = Math.max(0, Math.min(maxX, unscaledX * newScale - e.clientX));
    scroller.scrollTop  = Math.max(0, Math.min(maxY, unscaledY * newScale - e.clientY));
  }, { passive: false });

  setTimeout(() => {
    captureNaturalSize();
    applyZoom();
  }, 100);

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
    }, 200);
  });

  // ---------- DRAG PAGE WHEN ZOOMED ----------
  let isDragging = false;
  let dragStartX, dragStartY;
  let scrollLeftStart, scrollTopStart;

  document.addEventListener("mousedown", (e) => {
    if (pageZoomPercent <= 100) return;
    if (e.target.closest("#dragBox")) return;

    isDragging = true;

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

    const maxX = contentWidth - scroller.clientWidth;
    const maxY = contentHeight - scroller.clientHeight;

    let newX = scrollLeftStart - dx;
    let newY = scrollTopStart  - dy;

    newX = Math.max(0, Math.min(maxX, newX));
    newY = Math.max(0, Math.min(maxY, newY));

    scroller.scrollLeft = newX;
    scroller.scrollTop  = newY;
  });

  document.addEventListener("mouseup", () => {
    isDragging = false;
    document.body.style.cursor = "default";
  });

  pageContent.addEventListener("mouseleave", () => {
    isDragging = false;
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