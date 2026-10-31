import { state, matchesSearch, STATUS } from './data.js';
import { gameCard, slot, emptyState } from './ui.js';

export function renderWishlist(root){
  const wanted = state.games.filter(g => g.status === STATUS.WANT && matchesSearch(g, g.title + " " + g.genre));
  slot(root, "wishlist-games").innerHTML = wanted.map(gameCard).join("")
    || emptyState("Your wishlist is clear", "Add a game and set its status to “Want to play”.");
}
