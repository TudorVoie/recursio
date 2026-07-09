document.addEventListener('DOMContentLoaded', () => {

    const overlay = document.getElementById('shareModalOverlay');
    const closeBtn = document.getElementById('closeShareModal');
    const confirmBtn = document.getElementById('confirmShareModal');

    const nameInput = document.getElementById('shareNameInput');
    const titleInput = document.getElementById('shareTitleInput');

    const nameHidden = document.getElementById('shareNameHidden');
    const titleHidden = document.getElementById('shareTitleHidden');
    const form = document.getElementById('shareForm');

    // OPEN (call this from your "share" button)
    window.openShareModal = function () {
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            nameInput.focus();
        }, 50);
    };

    // CLOSE
    function closeModal() {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    window.closeShareModal = closeModal;

    // CONFIRM / SUBMIT
    function submitShare() {
        const name = nameInput.value.trim();
        const title = titleInput.value.trim();

        if (!name) {
            nameInput.focus();
            return;
        }

        if (!title) {
            titleInput.focus();
            return;
        }

        // pass values to hidden form
        nameHidden.value = name;
        titleHidden.value = title;

        form.submit();
    }

    // button events
    closeBtn.addEventListener('click', closeModal);
    confirmBtn.addEventListener('click', submitShare);

    // click outside modal closes it
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeModal();
        }
    });

    // ESC key closes modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('open')) {
            closeModal();
        }

        if (e.key === 'Enter' && overlay.classList.contains('open')) {
            submitShare();
        }
    });

});