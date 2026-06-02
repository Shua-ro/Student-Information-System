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

    // Search Button filter
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            var query = this.value.toLowerCase().trim();
            var rows = document.querySelectorAll('#studentTableBody tr');
            rows.forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.classList.toggle('hidden-row', query !== '' && !text.includes(query));
            });
        });
    }

});
