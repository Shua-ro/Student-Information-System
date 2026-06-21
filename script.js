document.addEventListener('DOMContentLoaded', function () {

    // Time alert countdown
    var alert = document.querySelector('.alert-status');
    if (alert) {
        history.replaceState(null, '', 'index.php');
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.4s';
            setTimeout(() => alert.style.display = 'none', 400);
        }, 3000);
    }

    // Bulk select / delete
    var selectAll = document.getElementById('selectAll');
    var bulkForm = document.getElementById('bulkDeleteForm');
    var bulkBar = document.getElementById('bulkActionsBar');
    var selectedCountEl = document.getElementById('selectedCount');
    var toggleBtn = document.getElementById('toggleBulkSelect');

    if (selectAll && bulkForm && bulkBar && selectedCountEl && toggleBtn) {
        var getRowCheckboxes = function () {
            return Array.prototype.slice.call(bulkForm.querySelectorAll('.row-checkbox'));
        };

        var updateBulkBar = function () {
            var rowCheckboxes = getRowCheckboxes();
            var checkedCount = rowCheckboxes.filter(function (cb) { return cb.checked; }).length;

            bulkBar.hidden = checkedCount === 0;
            selectedCountEl.textContent = checkedCount + ' selected';

            selectAll.checked = checkedCount > 0 && checkedCount === rowCheckboxes.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
        };

        toggleBtn.addEventListener('click', function () {
            var turningOn = !bulkForm.classList.contains('bulk-select-active');
            bulkForm.classList.toggle('bulk-select-active', turningOn);
            toggleBtn.classList.toggle('active', turningOn);
            toggleBtn.innerHTML = turningOn
                ? '<i class="ti ti-x"></i> Cancel'
                : '<i class="ti ti-checkbox"></i> Bulk Delete';

            if (!turningOn) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
                getRowCheckboxes().forEach(function (cb) { cb.checked = false; });
                updateBulkBar();
            }
        });

        selectAll.addEventListener('change', function () {
            getRowCheckboxes().forEach(function (cb) {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });

        getRowCheckboxes().forEach(function (cb) {
            cb.addEventListener('change', updateBulkBar);
        });

        bulkForm.addEventListener('submit', function (e) {
            var checkedCount = getRowCheckboxes().filter(function (cb) { return cb.checked; }).length;

            if (checkedCount === 0) {
                e.preventDefault();
                return;
            }

            var confirmed = confirm(
                'Delete ' + checkedCount + ' selected student(s)? This cannot be undone.'
            );
            if (!confirmed) {
                e.preventDefault();
            }
        });

        updateBulkBar();
    }

});
