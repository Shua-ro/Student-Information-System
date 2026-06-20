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

    

});
