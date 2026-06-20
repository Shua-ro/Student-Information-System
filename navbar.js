
(function () {
    var hamburger = document.getElementById('navHamburger');
    var navbarRight = document.getElementById('navbarRight');
    var navbar = document.querySelector('.navbar');
    if (!hamburger || !navbarRight || !navbar) return;

    function closeMenu() {
        navbar.classList.remove('nav-open');
        hamburger.setAttribute('aria-expanded', 'false');
    }

    hamburger.addEventListener('click', function () {
        var isOpen = navbar.classList.toggle('nav-open');
        hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    navbarRight.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('click', function (e) {
        if (navbar.classList.contains('nav-open') && !navbar.contains(e.target)) {
            closeMenu();
        }
    });
})();
