// Entry point: registers every SPApp route and wires the global controls.
import { state, addGame, STATUS } from './data.js';
import { setChrome, setActiveNav, setTitle, refreshChrome, openModal, closeModal, showToast } from './ui.js';
import { renderDashboard } from './dashboard.js';
import { initLibrary, renderLibrary } from './library.js';
import { renderWishlist } from './wishlist.js';
import { initProfile, renderProfile } from './profile.js';
import { initLogin, initRegister } from './auth.js';
import { initAdmin, renderAdmin } from './admin.js';

// One entry per <section> in index.html.
//   chrome: show sidebar + topbar        init: runs once after the template loads
//   render: runs every time the view is shown (the loaded markup stays in the DOM)
const views = {
  "dashboard-main": {title: "Overview",       render: renderDashboard},
  "library-main":   {title: "My library",     init: initLibrary, render: renderLibrary},
  "wishlist-main":  {title: "Wishlist",       render: renderWishlist},
  "profile-main":   {title: "Profile",        init: initProfile, render: renderProfile},
  "login-main":     {title: "Log in",         chrome: false, init: initLogin},
  "register-main":  {title: "Create account", chrome: false, init: initRegister},
  "admin-main":     {title: "Admin",          chrome: false, init: initAdmin, render: renderAdmin},
  "hero-main":      {title: "Welcome",        chrome: false}
};

const root = id => document.getElementById(id);
let currentView = null;

const app = $.spapp({
  defaultView: "#hero-main",
  templateDir: "./views/"
});

Object.entries(views).forEach(([id, view]) => {
  app.route({
    view: id,
    onCreate: () => { if (view.init) view.init(root(id)); },
    onReady: () => {
      currentView = id;
      setChrome(view.chrome !== false);
      setActiveNav(id);
      setTitle(view.title);
      refreshChrome();
      window.scrollTo(0, 0);
      if (view.render) view.render(root(id));
    }
  });
});

app.run();

/* ---------- global controls ---------- */
document.addEventListener("click", e => {
  if (e.target.closest(".js-open-add")) openModal();
});
document.getElementById("modal-close").addEventListener("click", closeModal);
document.getElementById("modal-backdrop").addEventListener("click", e => {
  if (e.target.id === "modal-backdrop") closeModal();
});
document.addEventListener("keydown", e => { if (e.key === "Escape") closeModal(); });

document.getElementById("game-form").addEventListener("submit", e => {
  e.preventDefault();
  const title = document.getElementById("game-name").value.trim();
  if (!title) return;
  const status = document.getElementById("game-status").value;
  const genre = document.getElementById("game-genre").value;
  addGame({title, genre, status});
  closeModal();

  // Same hash => no hashchange event, so re-render the visible view directly.
  const target = status === STATUS.WANT ? "wishlist-main" : "library-main";
  if (location.hash === "#" + target) { refreshChrome(); views[target].render(root(target)); }
  else location.hash = target;
  showToast(`${title} added to your collection`);
});

document.getElementById("global-search").addEventListener("input", e => {
  state.searchTerm = e.target.value;
  if (currentView === "library-main" || currentView === "wishlist-main") views[currentView].render(root(currentView));
  else if (state.searchTerm) location.hash = "library-main";
});

document.getElementById("mobile-menu").addEventListener("click", () => {
  document.getElementById("sidebar").classList.toggle("open");
});
