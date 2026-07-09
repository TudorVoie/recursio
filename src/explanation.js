document.addEventListener("DOMContentLoaded", () => {
const sidebar = document.getElementById("legendSidebar");
const btn = document.getElementById("legendBtn");

let open = false;

btn.addEventListener("click", () => {
    open = !open;

    // toggle sidebar
    sidebar.classList.toggle("active", open);

    // change arrow direction
    btn.innerHTML = open ? "&#x21D0;" : "&#x21D2;";
});

});
