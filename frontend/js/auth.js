import { showToast } from './ui.js';

export function initLogin(root){
  root.querySelector("#login-form").addEventListener("submit", e => {
    e.preventDefault();
    showToast("Demo only — authentication is not connected yet.");
  });
}

export function initRegister(root){
  root.querySelector("#register-form").addEventListener("submit", e => {
    e.preventDefault();
    const password = root.querySelector("#reg-password").value;
    const confirm = root.querySelector("#reg-confirm").value;
    if (password !== confirm) { showToast("Passwords do not match."); return; }
    showToast("Demo only — authentication is not connected yet.");
  });
}
