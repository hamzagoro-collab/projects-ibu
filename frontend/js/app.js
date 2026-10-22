const sampleGames = [
  {id:1,title:"Dragon Ball FighterZ",genre:"2D Fighter",platform:"PC",status:"Playing",year:2018,cover:"dbfighterz",symbol:"D",rating:5},
  {id:2,title:"Cyberpunk 2077",genre:"RPG",platform:"PC",status:"Playing",year:2020,cover:"cyber",symbol:"C",rating:4},
  {id:3,title:"Resident Evil 4",genre:"Survival Horror",platform:"PC",status:"Completed",year:2005,cover:"re4",symbol:"R",rating:5},
  {id:4,title:"The King Of Fighters XV",genre:"2D Fighter",platform:"PC",status:"Playing",year:2022,cover:"kofxv",symbol:"K",rating:5},
  {id:5,title:"Dying Light: The Beast",genre:"Open World",platform:"PC",status:"Want to play",year:2025,cover:"dyinglight",symbol:"D",rating:0},
  {id:6,title:"Portal 2",genre:"Puzzle",platform:"PC",status:"Completed",year:2011,cover:"portal",symbol:"P",rating:5},
  {id:7,title:"Power Drill Massacre",genre:"Survival Horror",platform:"PC",status:"Want to play",year:2028,cover:"powerdrill",symbol:"P",rating:0},
  {id:8,title:"Team Fortress 2",genre:"FPS Shooter",platform:"PC",status:"Playing",year:2007,cover:"teamfortress2",symbol:"TF",rating:4}
];
let games = sampleGames.map(game => ({...game}));
let activeView = "dashboard";
let searchTerm = "";
const content = document.getElementById("app-content");
const titleEl = document.getElementById("page-title");
const modal = document.getElementById("modal-backdrop");
const pageNames = {dashboard:"Overview",library:"My library",wishlist:"Wishlist",profile:"Profile",login:"Log in",register:"Create account"};

function statusClass(status){return status==="Playing"?"status-playing":status==="Completed"?"status-completed":"status-want";}
function gameCard(game){
  return `<article class="game-card">
    <div class="game-cover cover-${game.cover || "hollow"}"><span class="cover-symbol">${escapeHtml(game.symbol || game.title.charAt(0))}</span><span class="cover-label">${escapeHtml(game.title)}</span></div>
    <div class="game-info"><div class="game-title" title="${escapeHtml(game.title)}">${escapeHtml(game.title)}</div>
      <div class="game-meta"><span>${escapeHtml(game.genre)}</span><span>${game.year || "New"}</span></div>
      <span class="status ${statusClass(game.status)}">${escapeHtml(game.status)}</span>
    </div>
  </article>`;
}
function escapeHtml(value){return String(value).replace(/[&<>"']/g, char=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;"}[char]));}
function statCard(label,value,note,icon){return `<div class="stat-card"><div class="stat-top"><span>${label}</span><span class="stat-icon">${icon}</span></div><div class="stat-value">${value}</div><div class="stat-note">${note}</div></div>`;}
function pageHeading(kicker,heading,description,button=""){return `<div class="page-heading"><div><div class="eyebrow">${kicker}</div><h1>${heading}</h1><p>${description}</p></div>${button}</div>`;}
function addButton(){return `<button class="primary-button" id="open-add">＋ Add a game</button>`;}
function dashboard(){
  const playing=games.filter(g=>g.status==="Playing").length;
  const completed=games.filter(g=>g.status==="Completed").length;
  const wishlist=games.filter(g=>g.status==="Want to play").length;
  return `${pageHeading("YOUR PERSONAL GAME SPACE","Your library, your adventure.","A little progress every day adds up to a great collection.",addButton())}
    <section class="stats-grid">
      ${statCard("Games in library",games.length,"Across your collection","▦")}
      ${statCard("Currently playing",playing,"Games in progress","◷")}
      ${statCard("Completed",completed,"Ready for the next quest","✓")}
      ${statCard("On your wishlist",wishlist,"Next up to explore","♡")}
    </section>
    <section><div class="section-head"><h2>Recently added</h2><button class="quiet-link" data-view="library">View all games →</button></div>
      <div class="game-grid">${games.slice(-4).reverse().map(gameCard).join("")}</div>
    </section>
    <section class="content-two">
      <div class="panel"><h2>Recently in your collection</h2><div class="activity-list">
        ${games.slice(0,4).map((g,i)=>`<div class="activity-item"><div class="activity-thumb cover-${g.cover}">${g.symbol}</div><div class="activity-copy"><strong>${escapeHtml(g.title)}</strong><span>${g.status==="Completed"?"Marked as completed":g.status==="Playing"?"Currently playing":"Added to wishlist"}</span></div><span class="activity-time">${i===0?"Today":i===1?"Yesterday":`${i+2} days ago`}</span></div>`).join("")}
      </div></div>
      <div class="panel"><h2>Collection progress</h2>
        ${progressRow("Completed",completed,games.length,"#")}
        ${progressRow("Playing",playing,games.length,"#")}
        ${progressRow("Want to play",wishlist,games.length,"#")}
        <div class="api-note"><span class="spark">✦</span><div><strong>Discover more games</strong><p>A game database API is planned for a later milestone to help you find titles and cover art.</p></div></div>
      </div>
    </section>`;
}
function progressRow(label,value,total){const percent=total?Math.round(value/total*100):0;return `<div class="progress-row"><div class="progress-label"><span>${label}</span><span>${value} · ${percent}%</span></div><div class="progress-track"><div class="progress-fill" style="width:${percent}%"></div></div></div>`;}
function library(){
  const filtered=games.filter(g=>(g.title+" "+g.genre+" "+g.platform).toLowerCase().includes(searchTerm.toLowerCase()));
  return `${pageHeading("THE COLLECTION","My game library","All your games in one place. Keep track of what you're playing.",addButton())}
    <div class="toolbar"><select class="filter-select" id="status-filter" aria-label="Filter by status"><option value="All">All statuses</option><option>Playing</option><option>Completed</option><option>Want to play</option></select><select class="filter-select" id="sort-filter" aria-label="Sort games"><option value="recent">Recently added</option><option value="az">Title A–Z</option><option value="year">Release year</option></select><span class="toolbar-spacer"></span><span class="muted">${filtered.length} games shown</span></div>
    <div class="game-grid library-grid" id="library-games">${filtered.map(gameCard).join("") || `<div class="empty-state"><strong>No games found</strong>Try another search, or add a game to your collection.</div>`}</div>`;
}
function wishlist(){
 const wanted=games.filter(g=>g.status==="Want to play"&&(g.title+" "+g.genre).toLowerCase().includes(searchTerm.toLowerCase()));
 return `${pageHeading("YOUR NEXT ADVENTURE","Wishlist","Games you're excited to play next.",addButton())}
   <div class="wishlist-intro"><div class="mini-tag">Up next</div><h2>Every great adventure starts somewhere.</h2><p>Save games here so you always have an idea of what to play next.</p></div>
   <div class="game-grid">${wanted.map(gameCard).join("") || `<div class="empty-state"><strong>Your wishlist is clear</strong>Add a game and set its status to “Want to play”.</div>`}</div>`;
}
function profile(){
 return `${pageHeading("PLAYER SETTINGS","Your profile","A quick look at your GameVault account.")}<div class="toolbar"><button class="secondary-button" data-view="login">Log in page preview</button><button class="secondary-button" data-view="register">Register page preview</button></div><div class="profile-layout">
   <div class="panel profile-card"><div class="profile-avatar">P</div><h2>Player One</h2><p>Game enthusiast · Member since today</p><span class="profile-badge">✦ Free account</span><div style="margin-top:22px">${statCard("Games collected",games.length,"Your personal library","▦")}</div></div>
   <div class="panel"><h2>Account overview</h2><div class="detail-row"><span>Display name</span><strong>Player One</strong></div><div class="detail-row"><span>Email</span><strong>player@example.com</strong></div><div class="detail-row"><span>Account type</span><strong>Free prototype</strong></div><div class="detail-row"><span>Library visibility</span><strong>Private (planned)</strong></div><div class="api-note"><span class="spark">ⓘ</span><div><strong>Prototype account</strong><p>Profile details are sample content for Milestone 1. Registration and login will be connected to the backend in a later milestone.</p></div></div></div>
 </div>`;
}
function authPage(mode){
 const isRegister=mode==="register";
 return `<div class="auth-wrap"><div class="auth-card panel">
   <a class="brand auth-brand" href="#dashboard" data-view="dashboard"><span class="brand-mark">G</span><span>Game<span class="brand-light">Vault</span></span></a>
   <div class="eyebrow">${isRegister?"JOIN THE COMMUNITY":"WELCOME BACK"}</div>
   <h1>${isRegister?"Create your account":"Log in to GameVault"}</h1>
   <p class="auth-description">${isRegister?"Start building your personal game collection.":"Pick up where your next adventure begins."}</p>
   <form id="auth-form">
     ${isRegister?'<label>Display name<input required name="display-name" placeholder="Player One" maxlength="40"></label>':""}
     <label>Email address<input type="email" required name="email" placeholder="you@example.com"></label>
     <label>Password<input type="password" required name="password" minlength="6" placeholder="At least 6 characters"></label>
     ${isRegister?'<label>Confirm password<input type="password" required name="confirm-password" minlength="6" placeholder="Repeat your password"></label>':""}
     <button class="primary-button full-button" type="submit">${isRegister?"Create account":"Log in"} <span>→</span></button>
   </form>
   <p class="auth-switch">${isRegister?'Already have an account? <button class="quiet-link" data-view="login">Log in</button>':'New to GameVault? <button class="quiet-link" data-view="register">Create an account</button>'}</p>
   <p class="auth-disclaimer">Milestone 1 demo only — this form does not create an account or send data.</p>
   <button class="quiet-link" data-view="dashboard">← Back to overview</button>
 </div></div>`;
}
function render(){
 titleEl.textContent=pageNames[activeView]||"Overview";
 document.querySelectorAll("[data-view]").forEach(el=>el.classList.toggle("active",el.dataset.view===activeView&&el.classList.contains("nav-link")));
 document.getElementById("library-count").textContent=games.length;
 content.innerHTML=activeView==="dashboard"?dashboard():activeView==="library"?library():activeView==="wishlist"?wishlist():activeView==="profile"?profile():authPage(activeView);
 bindPageControls();
 const authForm=document.getElementById("auth-form");
 if(authForm)authForm.addEventListener("submit",e=>{e.preventDefault();showToast("Demo only — authentication is not connected yet.");});
}
function navigate(view){
 if(!pageNames[view])return;
 activeView=view;
 document.getElementById("sidebar").classList.remove("open");
 render();
}
function bindPageControls(){
 document.querySelectorAll("[data-view]").forEach(el=>el.addEventListener("click",()=>navigate(el.dataset.view)));
 const add=document.getElementById("open-add");
 if(add)add.addEventListener("click",openModal);
 const status=document.getElementById("status-filter");
 const sort=document.getElementById("sort-filter");
 if(status)status.addEventListener("change",()=>filterLibrary(status.value,sort.value));
 if(sort)sort.addEventListener("change",()=>filterLibrary(status.value,sort.value));
}
function filterLibrary(status,sort){
 let list=games.filter(g=>(g.title+" "+g.genre+" "+g.platform).toLowerCase().includes(searchTerm.toLowerCase()));
 if(status!=="All")list=list.filter(g=>g.status===status);
 if(sort==="az")list.sort((a,b)=>a.title.localeCompare(b.title));
 if(sort==="year")list.sort((a,b)=>(b.year||0)-(a.year||0));
 const target=document.getElementById("library-games");
 if(target)target.innerHTML=list.map(gameCard).join("")||`<div class="empty-state"><strong>No matching games</strong>Try another filter.</div>`;
 const count=document.querySelector(".toolbar .muted");
 if(count)count.textContent=`${list.length} games shown`;
}
function openModal(){modal.classList.remove("hidden");document.getElementById("game-name").focus();}
function closeModal(){modal.classList.add("hidden");document.getElementById("game-form").reset();}
document.getElementById("modal-close").addEventListener("click",closeModal);
modal.addEventListener("click",e=>{if(e.target===modal)closeModal();});
document.addEventListener("keydown",e=>{if(e.key==="Escape")closeModal();});
document.getElementById("game-form").addEventListener("submit",e=>{
 e.preventDefault();
 const name=document.getElementById("game-name").value.trim();
 if(!name)return;
 const status=document.getElementById("game-status").value;
 const genre=document.getElementById("game-genre").value;
 const cover=["dbfighterz","cyber","re4","kofxv","dyinglight","portal","powerdrill","teamfortress2"][Math.floor(Math.random()*8)];
 games.push({id:Date.now(),title:name,genre,platform:"PC",status,year:new Date().getFullYear(),cover,symbol:name.charAt(0).toUpperCase(),rating:0});
 closeModal();
 activeView=status==="Want to play"?"wishlist":"library";
 render();
 showToast(`${name} added to your collection`);
});
function showToast(message){
 const toast=document.createElement("div");toast.className="toast";toast.textContent=message;document.body.appendChild(toast);
 setTimeout(()=>toast.remove(),2600);
}
document.getElementById("global-search").addEventListener("input",e=>{
 searchTerm=e.target.value;
 if(activeView==="library")render();
 else if(activeView==="wishlist")render();
 else if(searchTerm){activeView="library";render();}
});
document.getElementById("mobile-menu").addEventListener("click",()=>document.getElementById("sidebar").classList.toggle("open"));
document.querySelectorAll("[data-view]").forEach(el=>el.addEventListener("click",()=>navigate(el.dataset.view)));
render();
