let { matrix, rows, cols } = window.APP;
let { matrix2, rows2, cols2 } = window.APP2;
let { count: progresare_cnt, data: progresare } = window.APP3; // ✅
const apelari = window.APP_VARS.flat();
const returnari = window.REVERSED_NUMBERS.flat();

let array = Array.from({ length: rows2 }, () => Array(cols2).fill(0));

let anime = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
let drawing_x = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
let drawing_y = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
let matrice_curbe = Array.from({ length: rows2 }, () => Array(cols2).fill(0));
let matrice_comparatie = Array.from({ length: rows2 }, () =>
  Array(cols2).fill(0),
);
const container = document.getElementById("container");
const width = container.clientWidth;
const height = container.clientHeight;

const colWidth = width / cols;
const lineHeight = height / rows;

let circle_size=colWidth-1;

for (let i = 0; i < rows; i++) {
  const rowDiv = document.createElement("div");
  rowDiv.id = `row-${i}`;
  rowDiv.className = "row";
  rowDiv.style.display = "flex";
  rowDiv.style.height = lineHeight + "px";

  for (let j = 0; j < cols; j++) {
    if (matrix[i][j] === 0) {
      break;
    }
    const cellDiv = document.createElement("div");
    cellDiv.id = `cell-${i}-${j}`;

    const value = matrix[i][j];
    const size = colWidth * Math.max(1, value);

    cellDiv.style.width = size + "px";
    cellDiv.style.height = "100%";
    cellDiv.style.flex = `0 0 ${size}px`;

    if (value === -1) {
      cellDiv.className = "whitecell";
    } else {
      cellDiv.className = "cell";
    }
    rowDiv.appendChild(cellDiv);
  }

  container.appendChild(rowDiv);
}

let j = 0;
while (j < cols2 && matrix2[rows2 - 1][j] != 0) {
  if (matrix2[rows2 - 1][j] == -2) {
    let suma;
    const parentCell = document.getElementById(`cell-${rows2 - 1}-${j}`);
    const circle = document.createElement("div");
    circle.className = "circle_div";
    circle.id = `circle-${rows2 - 1}-${j}`; // ✅ ID added
    array[rows2 - 1][j] = parentCell.clientWidth / 2;
    circle.style.position = "absolute";
    parentCell.style.position = "relative";
    suma = parentCell.clientWidth / 2;
    circle.style.left = suma - 10 + "px";
    parentCell.appendChild(circle);
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
        circle.id = `circle-${i}-${j}`; // ✅ ID added
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

const svg = document.getElementById("svg-lines"); // your SVG element
const svgRect = svg.getBoundingClientRect();

for (let i = 0; i < rows2; i++) {
  for (let j = 0; j < cols2; j++) {
    if (matrix2[i][j] != -1 && matrix2[i][j] != 0) {
      const circle_draw = document.getElementById(`circle-${i}-${j}`);
      const rect = circle_draw.getBoundingClientRect();

      // Coordinates relative to the SVG
      drawing_x[i][j] = rect.left - svgRect.left + rect.width / 2;
      drawing_y[i][j] = rect.top - svgRect.top + rect.height / 2;
    }
  }
}

for (let i = 0; i < rows2; i++)
  for (let j = 0; j < cols2; j++)
    if (matrix2[i][j] == -2) matrice_comparatie[i][j] = 1;
    else matrice_comparatie[i][j] = matrix2[i][j];
    if(circle_size>60)
      circle_size=60;
document.documentElement.style.setProperty('--circle-target', circle_size + 'px');
document.documentElement.style.setProperty('--circle-target2', circle_size-6 + 'px');
let triunghi_latime=(15*circle_size)/80;
let triunghi_larg=(10*circle_size)/80;
let extraOffset = (8*circle_size)/80;
let line_width=(6*circle_size)/80;