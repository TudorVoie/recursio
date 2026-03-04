let nr_apelare = 0;

/* 🔧 Show circle function (SVG-compatible, numbers centered without moving) */
async function show(i, j) {
  const circle = document.getElementById(`circle-${i}-${j}`);
  if (!circle) return;

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

/* 🔧 Animate line inside SVG (forward) */
function animateLineWithEvent(ult_poz_x, ult_poz_y, poz_x, poz_y) {
  return new Promise(resolve => {
    const xs = [ult_poz_x, poz_x];
    const ys = [ult_poz_y, poz_y];
    const line = document.createElementNS("http://www.w3.org/2000/svg", "polyline");
    const pointsString = xs.map((x, i) => `${x},${ys[i]}`).join(" ");
    line.setAttribute("points", pointsString);
    line.setAttribute("fill", "none");
    line.setAttribute("stroke", "black");
    line.setAttribute("stroke-width", line_width);

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

async function animateArrow(x1, y1, x2, y2,value,fontSize, fadeDuration = 1000, holdDuration = 300) {  // 🔹 restored parameter with default
  const svg = document.getElementById("svg-lines");

  // Midpoint of the line
  const midX = (x1 + x2) / 2;
  const midY = (y1 + y2) / 2;

  // Arrow size (your flexible globals)
  const arrowLength = triunghi_latime;
  const arrowWidth  = triunghi_larg;

  const angle = Math.atan2(y2 - y1, x2 - x1);

  // Triangle points
  const tipX  = midX + arrowLength * Math.cos(angle);
  const tipY  = midY + arrowLength * Math.sin(angle);

  const baseX = midX - arrowLength * Math.cos(angle);
  const baseY = midY - arrowLength * Math.sin(angle);

  const leftX  = baseX + arrowWidth * Math.sin(angle);
  const leftY  = baseY - arrowWidth * Math.cos(angle);

  const rightX = baseX - arrowWidth * Math.sin(angle);
  const rightY = baseY + arrowWidth * Math.cos(angle);

  const points = `${tipX},${tipY} ${leftX},${leftY} ${rightX},${rightY}`;

  // Create triangle
  const triangle = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
  triangle.setAttribute("points", points);
  triangle.setAttribute("fill", "orange");
  triangle.setAttribute("stroke", "darkred");
  triangle.setAttribute("stroke-width", "2");
  triangle.setAttribute("filter", "drop-shadow(0 0 4px yellow)");
  triangle.setAttribute("opacity", 0);
  triangle.style.transition = `opacity ${fadeDuration}ms ease-in`;

  svg.appendChild(triangle);

  // Force reflow for consistent fade-in
  triangle.getBoundingClientRect();

  requestAnimationFrame(() => {
    triangle.setAttribute("opacity", 1);
  });

  // Add number label if provided
  if (value !== null) {
    const label = document.createElementNS("http://www.w3.org/2000/svg", "text");
    label.textContent = value.toString();
    label.setAttribute("font-size", fontSize);  // 🔹 use the parameter
    label.setAttribute("fill", "#A0522D");
    label.setAttribute("dominant-baseline", "middle");

    const isRight = Math.cos(angle) > 0;

    const offsetX = isRight
      ? midX - arrowLength - extraOffset
      : midX + arrowLength + extraOffset;

    label.setAttribute("x", offsetX);
    label.setAttribute("y", midY);
    label.setAttribute("text-anchor", isRight ? "end" : "start");

    label.setAttribute("opacity", 0);
    label.style.transition = `opacity ${fadeDuration}ms ease-in`;

    svg.appendChild(label);

    // Force reflow for label
    label.getBoundingClientRect();

    requestAnimationFrame(() => {
      label.setAttribute("opacity", 1);
    });
  }

  await new Promise(resolve =>
    setTimeout(resolve, fadeDuration + holdDuration)
  );
}
/* 🔧 Main animation function */
let contor=0;
async function Animarea_Liniilor() {
  let ult_poz_x = drawing_x[0][0];
  let ult_poz_y = drawing_y[0][0];
  let poz_x, poz_y;
  let i = 0, j;
  progresare_cnt--;
  anime[0][0] = 1;
  let ver_descrestere = 0;
let ultima_valoare=0;
let poz_ultima_curba_x, poz_ultima_curba_y;
let poz_x_curba, poz_y_curba;
  // Show first circle
  await show(0, 0);
  await new Promise(resolve => setTimeout(resolve, 50));

  for (let k = 0; k < progresare_cnt; k++) {
    if (progresare[k] == 1) {
      if (ver_descrestere == 1) {
        ultima_valoare=0;
        anime[i][j]++;
        ver_descrestere = 0;
      }

      i++; j = 0;

      let verificare = (anime[i][j] == -1 || anime[i][j] >= matrice_comparatie[i][j]) ? 1 : 0;
      while (verificare == 1) {
        if (anime[i][j] == -1) j++;
        else if (anime[i][j] >= matrice_comparatie[i][j]) j++;
        else verificare = 0;
      }

      anime[i][j]++;
      poz_x = drawing_x[i][j];
      poz_y = drawing_y[i][j];

      // Forward: straight line
      await animateLineWithEvent(ult_poz_x, ult_poz_y, poz_x, poz_y);

      await show(i, j);
      await new Promise(resolve => setTimeout(resolve, 50));

      ult_poz_x = poz_x;
      ult_poz_y = poz_y;

    } else {
      i--; j = 0;

      let verificare;
      if (anime[i][j] >= matrice_comparatie[i][j]) {
        verificare = 1;
      } else {
        verificare = 0;
      }
      while (verificare == 1) {
        if (anime[i][j] >= matrice_comparatie[i][j]) j++;
        else verificare = 0;
      }
      
      let var_curba=0;
      if (matrice_curbe[i][var_curba] >= matrix2[i][var_curba]) {
        verificare = 1;
      } else {
        verificare = 0;
      }
      while (verificare == 1) {
        if (matrice_curbe[i][var_curba] >= matrix2[i][var_curba]) var_curba++;
        else verificare = 0;
      }

      matrice_curbe[i][var_curba]++;

      if(ultima_valoare==0)
      {
        poz_ultima_curba_x=ult_poz_x;
        poz_ultima_curba_y=ult_poz_y;
        ultima_valoare=1;
      }

      poz_x = drawing_x[i][j];
      poz_y = drawing_y[i][j];

      
      poz_x_curba=drawing_x[i][var_curba];
      poz_y_curba=drawing_y[i][var_curba];

      await animateArrow(poz_ultima_curba_x, poz_ultima_curba_y, poz_x_curba, poz_y_curba,returnari[contor],circle_size/2);
      contor++;
      poz_ultima_curba_x=poz_x_curba;
      poz_ultima_curba_y=poz_y_curba;

      ult_poz_x = poz_x;
      ult_poz_y = poz_y;
      ver_descrestere = 1;
    }
  }
}
const containerRect = container.getBoundingClientRect();
const circleRect = circle.getBoundingClientRect();

drawing_x[i][j] = circleRect.left - containerRect.left + circleRect.width / 2;
drawing_y[i][j] = circleRect.top - containerRect.top + circleRect.height / 2;