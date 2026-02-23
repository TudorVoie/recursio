async function Animarea_Liniilor() {
  let ult_poz_x = drawing_x[0][0];
  let ult_poz_y = drawing_y[0][0];
  let poz_x, poz_y;
  let i = 0;
  progresare_cnt--;
  anime[0][0] = 1;
  let ver_descrestere = 0;
  let j;

  // Show first circle and wait
  await show(0, 0);
  await new Promise((resolve) => setTimeout(resolve, 50));

  for (let k = 0; k < progresare_cnt; k++) {
    if (progresare[k] == 1) {
      if (ver_descrestere == 1) {
        anime[i][j]++;
        ver_descrestere = 0;
      }
      i++;
      j = 0;

      let verificare;
      if (anime[i][j] == -1) verificare = 1;
      else if (anime[i][j] >= matrice_comparatie[i][j]) verificare = 1;
      else verificare = 0;

      while (verificare == 1) {
        if (anime[i][j] == -1) j++;
        else if (anime[i][j] >= matrice_comparatie[i][j]) j++;
        else verificare = 0;
      }

      anime[i][j]++;
      poz_x = drawing_x[i][j];
      poz_y = drawing_y[i][j];

      await animateLineWithEvent(ult_poz_x, ult_poz_y, poz_x, poz_y);

      await show(i, j);
      await new Promise((resolve) => setTimeout(resolve, 50));

      ult_poz_x = poz_x;
      ult_poz_y = poz_y;
    } else {
      i--;
      j = 0;

      let verificare;
      if (anime[i][j] == -1) verificare = 1;
      else if (anime[i][j] >= matrice_comparatie[i][j]) verificare = 1;
      else verificare = 0;

      while (verificare == 1) {
        if (anime[i][j] == -1) j++;
        else if (anime[i][j] >= matrice_comparatie[i][j]) j++;
        else verificare = 0;
      }

      ult_poz_x = drawing_x[i][j];
      ult_poz_y = drawing_y[i][j];
      ver_descrestere = 1;
    }
  }
}
let nr_apelare = 0;

/* 🔧 Show circle function (SVG-compatible, numbers centered without moving) */
async function show(i, j) {
  const circle = document.getElementById(`circle-${i}-${j}`);
  if (!circle) return;

  // span for number
  let span = circle.querySelector('.circle_number');
  if (!span) {
    span = document.createElement('span');
    span.className = 'circle_number';
    span.style.position = 'absolute';
    span.style.top = '50%';
    span.style.left = '50%';
    span.style.transform = 'translate(-50%, -50%)';
    span.style.pointerEvents = 'none';
    circle.appendChild(span);
  }

  circle.classList.add("show");
  await new Promise(resolve => setTimeout(resolve, 300));
  span.textContent = apelari[nr_apelare];
  nr_apelare++;
}

/* 🔧 Animate line inside SVG */
function animateLineWithEvent(ult_poz_x, ult_poz_y, poz_x, poz_y) {
  return new Promise(resolve => {
    const xs = [ult_poz_x, poz_x];
    const ys = [ult_poz_y, poz_y];
    const line = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
    const pointsString = xs.map((x,i) => `${x},${ys[i]}`).join(" ");
    line.setAttribute("points", pointsString);
    line.setAttribute("fill", "none");
    line.setAttribute("stroke", "black");
    line.setAttribute("stroke-width", "0.20vw");

    const svg = document.getElementById("svg-lines");
    svg.appendChild(line);

    const length = line.getTotalLength();
    line.style.strokeDasharray = length;
    line.style.strokeDashoffset = length;

    line.getBoundingClientRect();
    line.style.transition = "stroke-dashoffset 0.5s linear";
    line.style.strokeDashoffset = 0;
    line.addEventListener("transitionend", resolve, { once: true });
  });
}

/* 🔧 Recursive animation function for your tree */
async function Animarea_Liniilor() {
  let ult_poz_x = drawing_x[0][0];
  let ult_poz_y = drawing_y[0][0];
  let poz_x, poz_y;
  let i=0, j;
  progresare_cnt--;
  anime[0][0] = 1;
  let ver_descrestere = 0;

  await show(0,0);
  await new Promise(resolve => setTimeout(resolve, 50));

  for (let k = 0; k < progresare_cnt; k++) {
    if (progresare[k] == 1) {
      if (ver_descrestere == 1) {
        anime[i][j]++;
        ver_descrestere = 0;
      }
      i++; j=0;
      let verificare = (anime[i][j] == -1 || anime[i][j] >= matrice_comparatie[i][j]) ? 1 : 0;
      while (verificare == 1) {
        if (anime[i][j] == -1) j++;
        else if (anime[i][j] >= matrice_comparatie[i][j]) j++;
        else verificare = 0;
      }
      anime[i][j]++;
      poz_x = drawing_x[i][j];
      poz_y = drawing_y[i][j];
      await animateLineWithEvent(ult_poz_x, ult_poz_y, poz_x, poz_y);
      await show(i,j);
      await new Promise(resolve => setTimeout(resolve,50));
      ult_poz_x = poz_x; ult_poz_y = poz_y;
    } else {
      i--; j=0;
      let verificare = (anime[i][j] == -1 || anime[i][j] >= matrice_comparatie[i][j]) ? 1 : 0;
      while (verificare == 1) {
        if (anime[i][j] == -1) j++;
        else if (anime[i][j] >= matrice_comparatie[i][j]) j++;
        else verificare = 0;
      }
      ult_poz_x = drawing_x[i][j];
      ult_poz_y = drawing_y[i][j];
      ver_descrestere = 1;
    }
  }
}

