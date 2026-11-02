// Shared helpers used by every view.
import { state } from './data.js';

export function escapeHtml(value){
  return String(value).replace(/[&<>"']/g, char => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[char]));
}

export function statusClass(status){
  return status === "Playing" ? "status-playing" : status === "Completed" ? "status-completed" : "status-want";
}

export function gameCard(game){
  return `<article class="game-card">
    <div class="game-cover cover-${escapeHtml(game.cover || "hollow")}"><span class="cover-symbol">${escapeHtml(game.symbol || game.title.charAt(0))}</span><span class="cover-label">${escapeHtml(game.title)}</span></div>
    <div class="game-info"><div class="game-title" title="${escapeHtml(game.title)}">${escapeHtml(game.title)}</div>
      <div class="game-meta"><span>${escapeHtml(game.genre)}</span><span>${game.year || "New"}</span></div>
      <span class="status ${statusClass(game.status)}">${escapeHtml(game.status)}</span>
    </div>
  </article>`;
}

export function activityItem(game, index){
  const text = game.status === "Completed" ? "Marked as completed" : game.status === "Playing" ? "Currently playing" : "Added to wishlist";
  const when = index === 0 ? "Today" : index === 1 ? "Yesterday" : `${index + 2} days ago`;
  return `<div class="activity-item"><div class="activity-thumb cover-${escapeHtml(game.cover)}">${escapeHtml(game.symbol)}</div><div class="activity-copy"><strong>${escapeHtml(game.title)}</strong><span>${text}</span></div><span class="activity-time">${when}</span></div>`;
}

export function emptyState(title, text){
  return `<div class="empty-state"><strong>${title}</strong>${text}</div>`;
}

// Scoped lookups: the same data-bind / data-slot names are reused across views.
export function bind(root, name, value){
  root.querySelectorAll(`[data-bind="${name}"]`).forEach(el => { el.textContent = value; });
}
export function slot(root, name){
  return root.querySelector(`[data-slot="${name}"]`);
}

export function showToast(message){
  const toast = document.createElement("div");
  toast.className = "toast";
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 2600);
}

/* ---------- page chrome (sidebar, topbar, breadcrumb) ---------- */
export function setChrome(visible){
  document.querySelector(".app-shell").classList.toggle("bare", !visible);
}
export function setActiveNav(viewId){
  document.querySelectorAll(".main-nav .nav-link").forEach(link => {
    link.classList.toggle("active", link.getAttribute("href") === "#" + viewId);
  });
}
export function setTitle(title){
  document.getElementById("page-title").textContent = title;
}
export function refreshChrome(){
  document.getElementById("library-count").textContent = state.games.length;
  document.getElementById("sidebar").classList.remove("open");
}

/* ---------- add-game modal ---------- */
const modal = () => document.getElementById("modal-backdrop");
export function openModal(){
  modal().classList.remove("hidden");
  document.getElementById("game-name").focus();
}
export function closeModal(){
  modal().classList.add("hidden");
  document.getElementById("game-form").reset();
}
