import { state, matchesSearch } from './data.js';
import { gameCard, bind, emptyState } from './ui.js';

// Runs once, when the view template has been loaded.
export function initLibrary(root){
  root.querySelector("#status-filter").addEventListener("change", () => renderLibrary(root));
  root.querySelector("#sort-filter").addEventListener("change", () => renderLibrary(root));
}

// Runs every time the view is shown.
export function renderLibrary(root){
  const status = root.querySelector("#status-filter").value;
  const sort = root.querySelector("#sort-filter").value;

  let list = state.games.filter(g => matchesSearch(g, g.title + " " + g.genre + " " + g.platform));
  if (status !== "All") list = list.filter(g => g.status === status);
  if (sort === "az") list.sort((a, b) => a.title.localeCompare(b.title));
  if (sort === "year") list.sort((a, b) => (b.year || 0) - (a.year || 0));

  root.querySelector("#library-games").innerHTML = list.map(gameCard).join("")
    || emptyState("No games found", "Try another search or filter, or add a game to your collection.");
  bind(root, "shown-count", `${list.length} ${list.length === 1 ? "game" : "games"} shown`);
}
