// ============================
// GLOBAL DATA CHECK
// ============================
document.addEventListener("DOMContentLoaded", () => {

    // ============================
    // ANIMATION
    // ============================
    if (window.RUN_ANIMATION) {
        const waitForAnim = () => {
            if (typeof Animarea_Liniilor === "function") {
                Animarea_Liniilor();
            } else {
                setTimeout(waitForAnim, 100);
            }
        };
        waitForAnim();
    }

    // ============================
    // CHATGPT CODE SYNC
    // ============================
    function syncChatgptCode() {
        const code = document.getElementById("textarea1");
        const hidden = document.getElementById("chatgptCodeHidden");
        if (code && hidden) hidden.value = code.value || "";
    }

    const chatgptForm = document.getElementById("chatgptForm");
    if (chatgptForm) {
        chatgptForm.addEventListener("submit", syncChatgptCode);
    }

    // ============================
    // SHARE MODAL
    // ============================
    const overlay = document.getElementById("shareModalOverlay");
    const closeBtn = document.getElementById("closeShareModal");
    const confirmBtn = document.getElementById("confirmShareModal");

    function openShareModal() {
        if (!overlay) return;
        overlay.classList.add("open");
        overlay.setAttribute("aria-hidden", "false");
    }

    function closeShareModal() {
        if (!overlay) return;
        overlay.classList.remove("open");
        overlay.setAttribute("aria-hidden", "true");
    }

    function submitShareForm() {
        const name = document.getElementById("shareNameInput");
        const title = document.getElementById("shareTitleInput");
        const nHidden = document.getElementById("shareNameHidden");
        const tHidden = document.getElementById("shareTitleHidden");
        const form = document.getElementById("shareForm");

        if (!name || !title) return;

        if (!name.value.trim()) return name.focus();
        if (!title.value.trim()) return title.focus();

        nHidden.value = name.value.trim();
        tHidden.value = title.value.trim();
        form.submit();
    }

    if (closeBtn) closeBtn.onclick = closeShareModal;
    if (confirmBtn) confirmBtn.onclick = submitShareForm;

    if (overlay) {
        overlay.addEventListener("click", e => {
            if (e.target === overlay) closeShareModal();
        });
    }

    document.addEventListener("keydown", e => {
        if (!overlay || !overlay.classList.contains("open")) return;

        if (e.key === "Escape") closeShareModal();
        if (e.key === "Enter") submitShareForm();
    });

    // expose for PHP inline calls
    window.openShareModal = openShareModal;
    window.closeShareModal = closeShareModal;

    // ============================
    // TURNSTILE
    // ============================
    window.onTurnstileSuccess = function (token) {
        const input = document.getElementById("turnstileToken");
        const btn = document.getElementById("executeButton");
        const text = document.getElementById("executeText");

        if (input) input.value = token;

        if (btn && text) {
            btn.disabled = false;
            text.innerText = "Execute";
        }
    };

    window.onTurnstileExpired = function () {
        const input = document.getElementById("turnstileToken");
        const btn = document.getElementById("executeButton");
        const text = document.getElementById("executeText");

        if (input) input.value = "";

        if (btn && text) {
            btn.disabled = true;
            text.innerText = "Checking security...";
        }
    };

    window.onTurnstileError = window.onTurnstileExpired;

    // ============================
    // EXECUTE FORM (IMPORTANT FIX)
    // ============================
    const executeForm = document.getElementById("myForm");

    if (executeForm) {
        executeForm.addEventListener("submit", function (e) {

            const token = document.getElementById("turnstileToken")?.value;

            const btn = document.getElementById("executeButton");
            const text = document.getElementById("executeText");

            if (!token) {
                if (text) text.innerText = "Wait for the security check...";
                return;
            }

            if (btn && text) {
                btn.disabled = true;
                text.innerText = "Executing...";
            }

            const formData = new FormData(executeForm);

            fetch(window.location.href, {
                method: "POST",
                body: formData
            })
                .then(r => r.text())
                .catch(() => {
                    if (text) text.innerText = "Error!";
                })
                .finally(() => {
                    if (btn && text) {
                        btn.disabled = false;
                        text.innerText = "Execute";
                    }
                });

            e.preventDefault(); // IMPORTANT
        });
    }

});