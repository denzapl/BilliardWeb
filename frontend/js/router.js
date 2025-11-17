
// Define SPA routes (base keys only, no query string)
const routes = {
    "home": "http://127.0.0.1:8080/flightphp_project/frontend/views/index.html",
    "login": "http://127.0.0.1:8080/flightphp_project/frontend/views/login.html",
    "profile": "http://127.0.0.1:8080/flightphp_project/frontend/views/profile.html",
    "dashboard": "http://127.0.0.1:8080/flightphp_project/frontend/views/dashboard.html",

    // Clubs CRUD
    "clubs": "http://127.0.0.1:8080/flightphp_project/frontend/views/clubs/index.html",
    "clubs-create": "http://127.0.0.1:8080/flightphp_project/frontend/views/clubs/create.html",
    "clubs-update": "http://127.0.0.1:8080/flightphp_project/frontend/views/clubs/update.html",
    "clubs-show": "http://127.0.0.1:8080/flightphp_project/frontend/views/clubs/show.html"
};

// Extract inner <body> content from a full HTML string
function extractBody(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, "text/html");
    return doc.body.innerHTML;
}

// Set active nav link based on page base (e.g. "clubs" from "clubs?id=5")
function setActiveNav(pageBase) {
    document.querySelectorAll('a[data-page]').forEach(a => {
        // anchor may carry a query part; compare only base
        const base = (a.dataset.page || '').split('?')[0];
        if (base === pageBase) {
            a.classList.add('active');
        } else {
            a.classList.remove('active');
        }
    });
}

// Load page into layout
async function loadPage(pageWithQuery, addToHistory = true) {
    // Normalize: pageWithQuery might be 'clubs' or 'clubs-update?id=5'
    const [pageBase] = pageWithQuery.split('?');

    const path = routes[pageBase] || routes["home"];
    try {
        // fetch the view (full HTML)
        const res = await fetch(path);
        if (!res.ok) throw new Error(`Failed to load ${path}: ${res.status}`);

        const html = await res.text();
        const content = extractBody(html);

        // fetch the layout (inner.html)
        const layoutRes = await fetch('http://127.0.0.1:8080/flightphp_project/frontend/views/layouts/inner.html');
        if (!layoutRes.ok) throw new Error(`Failed to load layout: ${layoutRes.status}`);
        let layout = await layoutRes.text();

        // Insert content into layout placeholder
        const out = layout.replace('<!--CONTENT-->', content);

        // Write assembled page
        document.open();
        document.write(out);
        document.close();

        // update active nav
        setActiveNav(pageBase);

        // push to history as a hash so refresh/back works
        if (addToHistory) {
            try {
                const hash = '#' + encodeURIComponent(pageWithQuery);
                history.pushState({ page: pageWithQuery }, '', hash);
            } catch (e) { /* ignore push errors */ }
        }

    } catch (err) {
        console.error(err);
        // graceful fallback: try to show a simple message in existing container
        const app = document.getElementById('app');
        if (app) app.innerText = 'Error loading page: ' + err.message;
        else alert('Error loading page: ' + err.message);
    }
}

// Handle back/forward browser buttons (reads from hash)
window.addEventListener('popstate', (e) => {
    const hash = location.hash ? decodeURIComponent(location.hash.slice(1)) : 'home';
    loadPage(hash, false);
});

// Universal click handler: any <a data-page="..."> will be SPA-handled
document.addEventListener('click', function(e) {
    // find closest anchor element (so clicks on <span> inside <a> still work)
    const a = e.target.closest && e.target.closest('a');
    if (!a) return;

    const page = a.dataset.page;
    if (page) {
        e.preventDefault();
        loadPage(page);
        return;
    }

    // If anchor points to a local view file like "create.html" we can map it
    // (helpful if a view still has plain href="create.html"). We'll try to detect:
    const href = a.getAttribute('href') || '';
    if (href.endsWith('create.html')) {
        e.preventDefault();
        loadPage('clubs-create');
        return;
    }
    if (href.includes('update.html')) {
        // try to extract id query param
        const url = new URL(href, location.href);
        const id = url.searchParams.get('id') || href.split('id=')[1] || '';
        e.preventDefault();
        loadPage(`clubs-update?id=${encodeURIComponent(id)}`);
        return;
    }
    // otherwise let the browser handle it normally
});

// On initial load, respect hash if present
const initial = location.hash ? decodeURIComponent(location.hash.slice(1)) : 'home';
loadPage(initial, false);
