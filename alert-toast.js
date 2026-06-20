(function () {
    var alertEl = document.getElementById('saveAlert');
    if (!alertEl) return;

    var timeoutId;
    var DISMISS_MS = 6000;

    function startTimer() {
        timeoutId = setTimeout(function () {
            alertEl.style.opacity = '0';
            alertEl.style.transition = 'opacity 0.4s';
            setTimeout(function () {
                if (alertEl && alertEl.parentNode) alertEl.remove();
            }, 400);
        }, DISMISS_MS);
    }
    function stopTimer() {
        clearTimeout(timeoutId);
    }

    alertEl.addEventListener('mouseenter', stopTimer);
    alertEl.addEventListener('mouseleave', startTimer);
    startTimer();
})();
