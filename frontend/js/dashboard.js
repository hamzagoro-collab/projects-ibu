import { state, countByStatus, countByGenre } from './data.js';
import { gameCard, activityItem, bind, slot, escapeHtml } from './ui.js';

const GENRE_COLORS = ["#a78bfa","#72e0ba","#f5b66b","#f18d9b","#7aa7ff","#c5a3ff","#5fd0e0"];

export function renderDashboard(root){
  const {playing, completed, wishlist} = countByStatus();
  const games = state.games;

  bind(root, "total", games.length);
  bind(root, "playing", playing);
  bind(root, "completed", completed);
  bind(root, "wishlist", wishlist);

  slot(root, "recent-games").innerHTML = games.slice(-4).reverse().map(gameCard).join("");
  slot(root, "activity").innerHTML = games.slice(0, 4).map(activityItem).join("");

  const counts = {"Completed": completed, "Playing": playing, "Want to play": wishlist};
  root.querySelectorAll("[data-progress]").forEach(row => {
    const value = counts[row.dataset.progress] || 0;
    const percent = games.length ? Math.round(value / games.length * 100) : 0;
    row.querySelector("[data-progress-text]").textContent = `${value} · ${percent}%`;
    row.querySelector("[data-progress-fill]").style.width = `${percent}%`;
  });

  renderGenreBreakdown(root);
}

// Text legend always renders; the Chart.js doughnut is an enhancement if the CDN script loaded.
function renderGenreBreakdown(root){
  const genres = countByGenre();
  slot(root, "genre-list").innerHTML = genres.map(([name, total], i) =>
    `<div class="genre-row"><span class="genre-dot" style="background:${GENRE_COLORS[i % GENRE_COLORS.length]}"></span><span>${escapeHtml(name)}</span><strong>${total}</strong></div>`
  ).join("");

  const canvas = root.querySelector("[data-genre-chart]");
  const hasChart = canvas && typeof Chart !== "undefined";
  root.querySelector(".genre-layout").classList.toggle("no-chart", !hasChart);
  if (!hasChart) return;
  const existing = Chart.getChart(canvas);
  if (existing) existing.destroy();
  new Chart(canvas, {
    type: "doughnut",
    data: {
      labels: genres.map(g => g[0]),
      datasets: [{
        data: genres.map(g => g[1]),
        backgroundColor: genres.map((g, i) => GENRE_COLORS[i % GENRE_COLORS.length]),
        borderColor: "#121925"
      }]
    },
    options: {plugins: {legend: {display: false}}}
  });
}
