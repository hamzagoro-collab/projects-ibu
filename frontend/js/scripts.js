/*!
* Based on Start Bootstrap - Agency v7.0.12 (https://startbootstrap.com/theme/agency)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (see LICENSE-template)
*/

// ---------- SPA: hash routing + loading each view only once ----------

// All views. Each one has a file views/<name>.html
const views = ['home', 'features', 'about', 'contact'];

// name -> promise of the fetch. Stores a view the first time we need it,
// so a second click on the same link never fetches it again.
const loading = {};

let currentView = 'home';

// Read the view name from the URL (#about -> "about"), fall back to home
function getViewName() {
    const name = window.location.hash.slice(1);
    return views.includes(name) ? name : 'home';
}

// Fetch a view's HTML and put it into #app (only runs once per view)
function loadView(name) {
    if (!loading[name]) {
        loading[name] = fetch('views/' + name + '.html')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Could not load view: ' + name);
                }
                return response.text();
            })
            .then(html => {
                const section = document.createElement('div');
                section.id = 'view-' + name;
                section.className = 'view d-none';
                section.innerHTML = html;
                document.getElementById('app').appendChild(section);
            });
    }
    return loading[name];
}

// Make sure the view is loaded, then show only that one
async function showView(name) {
    try {
        await loadView(name);
    } catch (error) {
        delete loading[name]; // allow a retry on the next click
        document.getElementById('app').innerHTML =
            '<div class="container page-section text-center text-danger">' + error.message + '</div>';
        return;
    }

    // The user may have clicked another link while we were loading
    if (getViewName() !== name) {
        return;
    }

    currentView = name;
    document.querySelectorAll('.view').forEach(view => view.classList.add('d-none'));
    document.getElementById('view-' + name).classList.remove('d-none');

    // Highlight the active link in the navbar
    document.querySelectorAll('#mainNav .nav-link').forEach(link => {
        link.classList.toggle('active', link.getAttribute('href') === '#' + name);
    });

    updateNavbar();
    window.scrollTo(0, 0);
}

// ---------- Navbar ----------

// Transparent navbar only on top of the home hero, solid everywhere else
function updateNavbar() {
    const nav = document.getElementById('mainNav');
    if (currentView !== 'home' || window.scrollY > 0) {
        nav.classList.add('navbar-shrink');
    } else {
        nav.classList.remove('navbar-shrink');
    }
}

// ---------- Start ----------

window.addEventListener('DOMContentLoaded', () => {
    // Show the view that matches the current URL (first page load)
    showView(getViewName());

    // Show a different view whenever the #hash changes (navbar links, buttons, back button)
    window.addEventListener('hashchange', () => showView(getViewName()));

    document.addEventListener('scroll', updateNavbar);

    // Collapse responsive navbar when toggler is visible
    const navbarToggler = document.body.querySelector('.navbar-toggler');
    document.querySelectorAll('#navbarResponsive .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.getComputedStyle(navbarToggler).display !== 'none') {
                navbarToggler.click();
            }
        });
    });
});

// Contact form lives in a view that is loaded later, so we listen on the document
document.addEventListener('submit', event => {
    if (event.target.id === 'contactForm') {
        event.preventDefault(); // no page reload
        document.getElementById('formMessage').classList.remove('d-none');
        event.target.reset();
    }
});
