// === Shared state — declarat la top-level ca sa fie accesibil din Animation.js ===
let matrix, rows, cols;
let matrix2, rows2, cols2;
let progresare_cnt, progresare;
let apelari, returnari;
let array, anime, drawing_x, drawing_y, matrice_curbe, matrice_comparatie;
let circle_size, triunghi_latime, triunghi_larg, extraOffset, line_width, bordare;
window.canRunAnimation = function () {
  return window.animationBlocked !== true;
};

window.generateAnimation = function () {
  if (!window.canRunAnimation()) return;
  if (typeof window.__resetMobileView === 'function') window.__resetMobileView();
  ({ matrix, rows, cols } = window.APP);
  ({ matrix2, rows2, cols2 } = window.APP2);
  ({ count: progresare_cnt, data: progresare } = window.APP3);

  apelari = window.APP_VARS.flat();
  returnari = window.REVERSED_NUMBERS.flat();

  // Pe telefon/tableta limita e mai mica: fix cat sa incapa fibonacci(6) (6x13).
  // Pe desktop ramane 10x60. Folosim window.IS_MOBILE (detectia de device din
  // <head>), nu latimea ecranului, ca sa prinda si tabletele mari.
  const isMobile = window.IS_MOBILE === true;
  const MAX_ROWS = isMobile ? 6 : 10;
  const MAX_COLS = isMobile ? 13 : 60;

  // 🚨 LIMIT CHECK
  if (rows > MAX_ROWS || cols > MAX_COLS) {

    // ✅ DELETE EVERYTHING INSIDE SVG
    const svg = document.getElementById("svg-lines");
    document.querySelectorAll(".circle_div.show").forEach(div => {
  div.style.display = "none";
});

    if (svg) {
      svg.innerHTML = "";
    }

    let modal = document.getElementById("size-warning-modal");

    if (!modal) {
      modal = document.createElement("div");
      modal.id = "size-warning-modal";

      modal.style.position = "fixed";
      modal.style.top = "0";
      modal.style.left = "0";
      modal.style.width = "100vw";
      modal.style.height = "100vh";
      modal.style.background = "rgba(0,0,0,0.6)";
      modal.style.display = "flex";
      modal.style.alignItems = "center";
      modal.style.justifyContent = "center";
      modal.style.zIndex = "99999";

      const box = document.createElement("div");
      box.style.background = "#ff4d4d";
      box.style.color = "white";
      box.style.padding = "25px 30px";
      box.style.borderRadius = "12px";
      box.style.textAlign = "center";
      box.style.minWidth = "300px";
      box.style.boxShadow = "0 10px 25px rgba(0,0,0,0.3)";
      box.style.fontSize = "16px";
      box.style.fontWeight = "bold";
      box.style.position = "relative";

      const text = document.createElement("div");
      text.textContent =
        `⚠️ Graful este prea mare!`;

      const closeBtn = document.createElement("button");
      closeBtn.textContent = "✖";

      closeBtn.style.position = "absolute";
      closeBtn.style.top = "8px";
      closeBtn.style.right = "10px";
      closeBtn.style.border = "none";
      closeBtn.style.background = "transparent";
      closeBtn.style.color = "white";
      closeBtn.style.fontSize = "18px";
      closeBtn.style.cursor = "pointer";

      closeBtn.onclick = () => modal.remove();

      box.appendChild(closeBtn);
      box.appendChild(text);
      modal.appendChild(box);

      document.body.appendChild(modal);
    }

    return;
  }


  array = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
  anime = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
  drawing_x = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
  drawing_y = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
  matrice_curbe = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
  matrice_comparatie = Array.from({ length: rows2 }, () => Array(cols2).fill(0));

  const container = document.getElementById("container");

  // ♻️ Curatam containerul (pastram doar SVG-ul, golit)
  const svg = document.getElementById("svg-lines");
  container.innerHTML = "";
  if (svg) {
    while (svg.firstChild) svg.removeChild(svg.firstChild);
    container.appendChild(svg);
  }

  const { width, height } = container.getBoundingClientRect();
  const colWidth = width / cols;
  const lineHeight = height / rows;

  for (let i = 0; i < rows; i++) {
    const rowDiv = document.createElement("div");
    rowDiv.id = `row-${i}`;
    rowDiv.className = "row";
    rowDiv.style.display = "flex";
    rowDiv.style.height = lineHeight + "px";

    for (let j = 0; j < cols; j++) {
      if (matrix[i][j] === 0) break;
      const cellDiv = document.createElement("div");
      cellDiv.id = `cell-${i}-${j}`;
      const value = matrix[i][j];
      const size = colWidth * Math.max(1, value);
      cellDiv.style.width = size + "px";
      cellDiv.style.height = "100%";
      cellDiv.style.flex = `0 0 ${size}px`;
      cellDiv.className = value === -1 ? "whitecell" : "cell";
      rowDiv.appendChild(cellDiv);
    }
    container.appendChild(rowDiv);
  }

  let j = 0;
  while (j < cols2 && matrix2[rows2 - 1][j] != 0) {
    if (matrix2[rows2 - 1][j] != -1 && matrix2[rows2 - 1][j] != 0) {
      const parentCell = document.getElementById(`cell-${rows2 - 1}-${j}`);
      if (parentCell) {
        const circle = document.createElement("div");
        circle.className = "circle_div";
        circle.id = `circle-${rows2 - 1}-${j}`;
        array[rows2 - 1][j] = parentCell.clientWidth / 2;
        circle.style.position = "absolute";
        parentCell.style.position = "relative";
        const suma = parentCell.clientWidth / 2;
        circle.style.left = (suma - 10) + "px";
        parentCell.appendChild(circle);
      }
    }
    j++;
  }

  Situarea_Punctului();

  function Situarea_Punctului() {
    for (let i = rows2 - 2; i >= 0; i--) {
      let j = 0;
      let progres = 0;
      let variabila;
      while (j < cols2 && matrix2[i][j] != 0) {
        if (matrix2[i][j] != -1) {
          if (matrix2[i][j] == -2) variabila = 1;
          else variabila = matrix2[i][j];
          let suma = 0;
          let k;
          let inceput;
          for (k = 1; k <= variabila; k++) {
            const cell = document.getElementById(`cell-${i + 1}-${progres}`);
            if (variabila == 1) suma = cell.clientWidth / 2;
            else {
              if (k == 1) {
                inceput = array[i + 1][progres];
                suma += cell.clientWidth - array[i + 1][progres];
              } else {
                suma += array[i + 1][progres];
              }
            }
            progres++;
          }
          if (variabila > 1) {
            suma = suma / 2;
            suma += inceput;
          }
          array[i][j] = suma;
          suma -= 10;
          const parentCell = document.getElementById(`cell-${i}-${j}`);
          parentCell.style.position = "relative";
          const circle = document.createElement("div");
          circle.className = "circle_div";
          circle.id = `circle-${i}-${j}`;
          circle.style.position = "absolute";
          circle.style.left = suma + "px";
          parentCell.appendChild(circle);
        } else progres++;
        j++;
      }
    }
  }

  for (let i = 0; i < rows2; i++)
    for (let j = 0; j < cols2; j++) if (matrix2[i][j] == -1) anime[i][j] = -1;

  const svgRect = document.getElementById("svg-lines").getBoundingClientRect();

  for (let i = 0; i < rows2; i++) {
    for (let j = 0; j < cols2; j++) {
      if (matrix2[i][j] != -1 && matrix2[i][j] != 0) {
        const circle_draw = document.getElementById(`circle-${i}-${j}`);
        if (circle_draw) {
          const rect = circle_draw.getBoundingClientRect();
          drawing_x[i][j] = rect.left - svgRect.left + rect.width / 2;
          drawing_y[i][j] = rect.top - svgRect.top + rect.height / 2;
        }
      }
    }
  }

  for (let i = 0; i < rows2; i++)
    for (let j = 0; j < cols2; j++)
      if (matrix2[i][j] == -2) matrice_comparatie[i][j] = 1;
      else matrice_comparatie[i][j] = matrix2[i][j];

  circle_size = Math.min(colWidth, lineHeight) * 0.5;
  if(circle_size < 20)
    circle_size = 20;
  if (circle_size > 50) circle_size = 50;
  document.documentElement.style.setProperty("--circle-target", circle_size + "px");
  triunghi_latime = (9 * circle_size) / 50;
  triunghi_larg = (6 * circle_size) / 50;
  extraOffset = (10 * circle_size) / 50;
  line_width = (3 * circle_size) / 50;
  bordare = (4 * circle_size) / 50;
  document.documentElement.style.setProperty("--circle-bordare", bordare + "px");

const safeNodes = document.querySelectorAll(".circle_div").length;

document.getElementById("nodes").textContent =
    "Nodes: " + safeNodes;

document.getElementById("levels").textContent =
    "Levels: " + rows2;
  console.log(document.querySelectorAll(".circle_div").length);
};
function fixLeftmostCircle() {
  setTimeout(() => {
    const firstCircle = document.getElementById("circle-0-0");
    if (!firstCircle) return;

    const rect = firstCircle.getBoundingClientRect();
    const container = document.getElementById("container");

    if (rect.left < 30) {
      const neededMargin = 30 - rect.left;
      const currentMargin = parseInt(container.style.marginLeft) || 0;
      container.style.marginLeft = (currentMargin + neededMargin) + "px";

      const scroller = document.querySelector('.viewport') || document.documentElement;
      scroller.scrollLeft = Math.max(0, scroller.scrollLeft - neededMargin);
    }
  }, 800);
}

window.fixLeftmostCircle = fixLeftmostCircle;