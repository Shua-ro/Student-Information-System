document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('deleteModalOverlay');
    if (!overlay) return;

    const titleEl = document.getElementById('deleteModalTitle');
    const descEl = document.getElementById('deleteModalDesc');
    const confirmBtn = document.getElementById('deleteModalConfirm');
    const cancelBtn = document.getElementById('deleteModalCancel');

    let pendingAction = null;


    function setDescription(leadText, subject, trailText) {
        descEl.textContent = '';
        descEl.append(leadText + ' ');
        const strong = document.createElement('strong');
        strong.textContent = subject;
        descEl.append(strong);
        descEl.append(trailText);
    }

    function openModal(title, leadText, subject, trailText, onConfirm) {
        titleEl.textContent = title;
        setDescription(leadText, subject, trailText);
        pendingAction = onConfirm;
        overlay.hidden = false;
        document.body.style.overflow = 'hidden';
        confirmBtn.focus();
    }

    function closeModal() {
        overlay.hidden = true;
        document.body.style.overflow = '';
        pendingAction = null;
    }

    // Single-row delete links
    document.querySelectorAll('.del-lnk').forEach((link) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            openModal(
                'Delete student record?',
                'Are you sure you want to delete',
                link.dataset.name || 'this student',
                '? This action cannot be undone.',
                () => { window.location.href = link.getAttribute('href'); }
            );
        });
    });

    const bulkForm = document.getElementById('bulkDeleteForm');
    const bulkBtn = document.getElementById('bulkDeleteBtn');

    if (bulkForm && bulkBtn) {
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#bulkDeleteBtn')) return;

            e.preventDefault();
            e.stopImmediatePropagation();

            const checkedCount = bulkForm.querySelectorAll('.row-checkbox:checked').length;
            if (checkedCount === 0) return;

            const subject = `${checkedCount} student${checkedCount === 1 ? '' : 's'}`;
            openModal(
                'Delete selected students?',
                'You are about to permanently delete',
                subject,
                '. This action cannot be undone and will remove all of their records.',
                () => { HTMLFormElement.prototype.submit.call(bulkForm); }
            );
        }, true);
    }

    confirmBtn.addEventListener('click', () => {
        const action = pendingAction;
        closeModal();
        if (action) action();
    });

    cancelBtn.addEventListener('click', closeModal);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !overlay.hidden) closeModal();
    });
});
