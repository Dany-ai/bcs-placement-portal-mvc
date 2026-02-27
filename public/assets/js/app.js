// Basic client-side enhancements could go here.
// For this minimal implementation, we simply provide a small helper.

document.addEventListener('DOMContentLoaded', function () {
    // Highlight current nav link
    var links = document.querySelectorAll('.main-nav a');
    var current = window.location.pathname;
    links.forEach(function (link) {
        if (current.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
});
