import { state } from './data.js';
import { bind, showToast } from './ui.js';

export function initProfile(root){
  root.querySelector("#profile-form").addEventListener("submit", e => {
    e.preventDefault();
    const password = root.querySelector("#profile-password").value;
    const confirm = root.querySelector("#profile-confirm").value;
    if (password !== confirm) { showToast("Passwords do not match."); return; }
    showToast("Demo only — profile changes are not saved yet.");
  });
}

export function renderProfile(root){
  bind(root, "total", state.games.length);
}
