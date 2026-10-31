// In-memory sample data (mirrors the rows in game_collection_manager.sql).
// Changes reset on refresh until the backend API is connected.

export const sampleGames = [
  {id:1,title:"Dragon Ball FighterZ",genre:"2D Fighter",platform:"PC",status:"Playing",year:2018,cover:"dbfighterz",symbol:"D",rating:5},
  {id:2,title:"Cyberpunk 2077",genre:"RPG",platform:"PC",status:"Playing",year:2020,cover:"cyber",symbol:"C",rating:4},
  {id:3,title:"Resident Evil 4",genre:"Survival Horror",platform:"PC",status:"Completed",year:2005,cover:"re4",symbol:"R",rating:5},
  {id:4,title:"The King Of Fighters XV",genre:"2D Fighter",platform:"PC",status:"Playing",year:2022,cover:"kofxv",symbol:"K",rating:5},
  {id:5,title:"Dying Light: The Beast",genre:"Open World",platform:"PC",status:"Want to play",year:2025,cover:"dyinglight",symbol:"D",rating:0},
  {id:6,title:"Portal 2",genre:"Puzzle",platform:"PC",status:"Completed",year:2011,cover:"portal",symbol:"P",rating:5},
  {id:7,title:"Power Drill Massacre",genre:"Survival Horror",platform:"PC",status:"Want to play",year:2028,cover:"powerdrill",symbol:"P",rating:0},
  {id:8,title:"Team Fortress 2",genre:"FPS Shooter",platform:"PC",status:"Playing",year:2007,cover:"teamfortress2",symbol:"TF",rating:4}
];

export const demoUsers = [
  {user_id:1,username:"playerone",email:"player1@example.com"},
  {user_id:2,username:"player2",email:"player2@example.com"}
];

export const STATUS = {PLAYING:"Playing",COMPLETED:"Completed",WANT:"Want to play"};
const COVERS = ["dbfighterz","cyber","re4","kofxv","dyinglight","portal","powerdrill","teamfortress2"];

export const state = {
  games: sampleGames.map(game => ({...game})),
  searchTerm: ""
};

export function addGame({title, genre, status}){
  const game = {
    id: Date.now(), title, genre, platform: "PC", status,
    year: new Date().getFullYear(),
    cover: COVERS[Math.floor(Math.random() * COVERS.length)],
    symbol: title.charAt(0).toUpperCase(), rating: 0
  };
  state.games.push(game);
  return game;
}

export function countByStatus(){
  return {
    playing: state.games.filter(g => g.status === STATUS.PLAYING).length,
    completed: state.games.filter(g => g.status === STATUS.COMPLETED).length,
    wishlist: state.games.filter(g => g.status === STATUS.WANT).length
  };
}

export function countByGenre(){
  const counts = {};
  state.games.forEach(g => { counts[g.genre] = (counts[g.genre] || 0) + 1; });
  return Object.entries(counts).sort((a, b) => b[1] - a[1] || a[0].localeCompare(b[0]));
}

export function matchesSearch(game, text){
  return text.toLowerCase().includes(state.searchTerm.toLowerCase());
}
