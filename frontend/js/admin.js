import { demoUsers, sampleGames } from './data.js';
import { bind, showToast, escapeHtml } from './ui.js';

export function initAdmin(root){
  const form = root.querySelector("#user-search-form");
  const result = root.querySelector("#user-result");
  let found = null;

  form.addEventListener("submit", e => {
    e.preventDefault();
    const name = root.querySelector("#search-username").value.trim().toLowerCase();
    found = demoUsers.find(u => u.username.toLowerCase() === name) || null;
    if (!found) { result.style.display = "none"; showToast("No user with that username."); return; }
    root.querySelector("#result-username").textContent = found.username;
    root.querySelector("#result-email").textContent = found.email;
    result.style.display = "block";
  });

  root.querySelector("#delete-account-btn").addEventListener("click", () => {
    if (!found) return;
    showToast(`Account "${found.username}" deleted (simulation)`);
    result.style.display = "none";
  });
  root.querySelector("#clear-collection-btn").addEventListener("click", () => {
    if (!found) return;
    showToast(`Collection of "${found.username}" cleared (simulation)`);
  });
}

export function renderAdmin(root){
  bind(root, "user-count", demoUsers.length);
  bind(root, "catalog-count", sampleGames.length);
}
