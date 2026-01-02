
    const { matrix, rows, cols } = window.APP;

    const container = document.getElementById("container");

    const width = container.clientWidth;
    let height = window.innerHeight;

    let colWidth = Math.floor(width / cols);
    let lineHeight = Math.floor(height / rows);


    for (let i = 0; i < rows; i++) {

        // ROW
        const rowDiv = document.createElement("div");
        rowDiv.id = `row-${i + 1}`;
        rowDiv.className = "row";
        rowDiv.style.display = "flex";
        rowDiv.style.height = lineHeight + "px";
        for(let j=0; j<cols; j++)
        {
            if(matrix[i][j]==0)
                j=cols+1;
            else if(matrix[i][j]==-1)
            {
            const cellDiv = document.createElement("div");
            cellDiv.id = `cell-${i + 1}-${j + 1}`;
            cellDiv.className = "whitecell";
            const size = colWidth;
            cellDiv.style.width = size + "px";
            cellDiv.style.height = "100%";
            cellDiv.style.flex = `0 0 ${size}px`;
            rowDiv.appendChild(cellDiv);
            }
            else if(matrix[i][j]==-2)
            {
            const cellDiv = document.createElement("div");
            cellDiv.id = `cell-${i + 1}-${j + 1}`;
            cellDiv.className = "cell";

            const size = colWidth * 1;
            cellDiv.style.width = size + "px";
            cellDiv.style.height = "100%";
            cellDiv.style.flex = `0 0 ${size}px`;
            cellDiv.textContent = size;
                

            rowDiv.appendChild(cellDiv);
            }
            else
            {
            // CELL
            const cellDiv = document.createElement("div");
            cellDiv.id = `cell-${i + 1}-${j + 1}`;
            cellDiv.className = "cell";

            const size = colWidth * matrix[i][j];
            cellDiv.style.width = size + "px";
            cellDiv.style.height = "100%";
            cellDiv.style.flex = `0 0 ${size}px`;
            cellDiv.textContent = size;
                

            rowDiv.appendChild(cellDiv);
            }
            
            
        }

        container.appendChild(rowDiv);
    }