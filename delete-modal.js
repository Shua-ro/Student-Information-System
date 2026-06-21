document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('deleteModalOverlay');
    if (!overlay) return;

    const nameEl = document.getElementById('deleteModalName');
    const confirmBtn = document.getElementById('deleteModalConfirm');
    const cancelBtn = document.getElementById('deleteModalCancel');

    function openModal(url, name) {
        nameEl.textContent = name || 'this student';
        confirmBtn.setAttribute('href', url);
        overlay.hidden = false;
        document.body.style.overflow = 'hidden';
        confirmBtn.focus();
    }

    function closeModal() {
        overlay.hidden = true;
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.del-lnk').forEach((link) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(link.getAttribute('href'), link.dataset.name);
        });
    });

    cancelBtn.addEventListener('click', closeModal);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !overlay.hidden) closeModal();
    });
});
