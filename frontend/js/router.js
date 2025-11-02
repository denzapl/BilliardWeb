// SPA routes
const routes = {
    "home": "views/home.html",
    "login": "views/login.html",
    "register": "views/register.html",
    "dashboard": "views/dashboard.html",
    "clubs": "views/clubs.html",
    "profile": "views/profile.html",
    "contact": "views/contact.html"
};

// Load page into #app
function loadPage(page) {
    const path = routes[page] || routes["home"];
    fetch(path)
        .then(res => res.text())
        .then(html => {
            document.getElementById("app").innerHTML = html;
        });
}

// Initial load
loadPage("home");

// Navigation handler
document.addEventListener("click", function(e) {
    if (e.target.tagName === "A" && e.target.dataset.page) {
        e.preventDefault();
        loadPage(e.target.dataset.page);
    }
});
