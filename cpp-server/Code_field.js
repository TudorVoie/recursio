window.addEventListener('DOMContentLoaded', () => {

  // ---------- DRAGBOX ----------
  const dragBox = document.getElementById("dragBox");
// const header = document.getElementById("dragBoxHeader"); // optional now
let offsetX = 0, offsetY = 0, startX = 0, startY = 0;

dragBox.style.position = 'fixed';
dragBox.style.top = '100px';
dragBox.style.left = '100px';

const target = dragBox; // make the entire box draggable
target.addEventListener('mousedown', (e) => {
  const rect = dragBox.getBoundingClientRect();
  const cornerSize = 15; // bottom-right corner reserved for resize

  // check if click is inside bottom-right corner
  const onResizeCorner =
    e.clientX >= rect.right - cornerSize &&
    e.clientY >= rect.bottom - cornerSize;

  // ignore interactive elements (textarea, input, button, select)
  const tag = e.target.tagName.toLowerCase();
  const interactive = ['input', 'textarea', 'select', 'button'].includes(tag);

  if (onResizeCorner || interactive) {
    return; // let browser handle resize or text input
  }

  // otherwise, handle drag
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
  const zoomText = document.getElementById("zoomText");

  let pageZoomPercent = 100;
  const zoomStep = 10, zoomMin = 100, zoomMax = 300;

  function updateZoom() {
    const scale = pageZoomPercent / 100;
    pageContent.style.zoom = scale;                 // zoom page content
    dragBox.style.transform = `scale(${1 / scale})`; // keep dragBox same size
    dragBox.style.transformOrigin = "top left";
    zoomText.innerText = pageZoomPercent + "%";      // update text
    zoomInBtn.disabled = pageZoomPercent >= zoomMax;
    zoomOutBtn.disabled = pageZoomPercent <= zoomMin;
  }

  zoomInBtn.addEventListener('click', () => {
    if (pageZoomPercent < zoomMax) {
      pageZoomPercent += zoomStep;
      const zoomBox = document.getElementById("zoomBox");
      zoomBox.innerText = pageZoomPercent+"%";
    }

    updateZoom();
  });

  zoomOutBtn.addEventListener('click', () => {
    if (pageZoomPercent > zoomMin) {
      pageZoomPercent -= zoomStep;
      const zoomBox = document.getElementById("zoomBox");
      zoomBox.innerText = pageZoomPercent+"%";
    }
    updateZoom();
  });

  // Initial render
  updateZoom();

});
// Toggle dragBox visibility
const toggleBtn = document.getElementById("toggleDragBox");
const dragBox = document.getElementById("dragBox");

toggleBtn.addEventListener("click", () => {
  if (dragBox.style.display === "none") {
    // Show dragBox
    dragBox.style.display = "flex";   // keeps flex layout
    toggleBtn.textContent = "Hide Code";
  } else {
    // Hide dragBox
    dragBox.style.display = "none";
    toggleBtn.textContent = "Show Code";
  }
});
function toggleNav() {
    const nav = document.getElementById("mySidenav");

    if (nav.style.width === "15vw") {
        nav.style.width = "0";
    } else {
        nav.style.width = "15vw";
    }
}