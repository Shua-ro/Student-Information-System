document.addEventListener('DOMContentLoaded', function () {
    var alert = document.querySelector('.alert-status');
    if (alert) {
        history.replaceState(null, '', 'index.php');

        setTimeout(function () {
            alert.style.display = 'none';
        }, 3000);
    }
});