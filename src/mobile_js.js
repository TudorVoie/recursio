const codeBtn = document.querySelector('[data-tab="code"]');
const vizBtn  = document.querySelector('[data-tab="vizualizare"]');
const dragBox2    = document.getElementById('dragBox');
const pageContent = document.getElementById('pageContent');
const timeline2   = document.getElementById('timelineContainer'); // ← adaugă

let currentPanel = "code";

codeBtn.addEventListener('click', () => {
    if (currentPanel === "code") return;
    dragBox2.style.transform    = 'translateX(0)';
    pageContent.style.transform = 'translateX(200%)';
    timeline2.style.transform    = 'translateX(200%)'; // ← adaugă
    codeBtn.classList.add('active');
    vizBtn.classList.remove('active');
    currentPanel = "code";
});

vizBtn.addEventListener('click', () => {
    if (currentPanel === "vizualizare") return;
    dragBox2.style.transform    = 'translateX(-200%)';
    pageContent.style.transform = 'translateX(0)';
    timeline2.style.transform    = 'translateX(0)'; // ← adaugă
    vizBtn.classList.add('active');
    codeBtn.classList.remove('active');
    currentPanel = "vizualizare";
});
function toggleHint(id) {
    document.querySelectorAll('.hint-overlay').forEach(overlay => {
        if (overlay.id !== id) overlay.classList.remove('open');
    });
    document.getElementById(id).classList.toggle('open');
}
document.querySelectorAll('.hint-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.classList.remove('open');
    });
});
function toggleDrawer() {
    document.getElementById('drawer').classList.toggle('open');
    document.getElementById('drawerOverlay').classList.toggle('open');
    document.getElementById('drawerFiltersMenu').classList.remove('open');
    document.querySelector('.drawer-filters-arrow').classList.remove('open');
}

document.getElementById('drawer').addEventListener('click', (e) => {
    if (!e.target.closest('.drawer-filters')) {
        document.getElementById('drawerFiltersMenu').classList.remove('open');
        document.querySelector('.drawer-filters-arrow').classList.remove('open');
    }
});
function toggleDrawerFilters() {
    const menu = document.getElementById('drawerFiltersMenu');
    const arrow = document.querySelector('.drawer-filters-arrow');
    menu.classList.toggle('open');
    arrow.classList.toggle('open');
}