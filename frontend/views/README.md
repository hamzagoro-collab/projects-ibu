# Views

HTML templates for the GameVault single-page app. The app uses **jQuery SPApp**: every `<section>`
in `index.html` has a `data-load` attribute, and the first time its hash (e.g. `#library-main`) is
visited, SPApp fetches that file from this folder into the section.

| Template         | Section / hash     | Purpose                                   |
| ---------------- | ------------------ | ----------------------------------------- |
| `hero.html`      | `#hero-main`       | Landing page (default view)               |
| `dashboard.html` | `#dashboard-main`  | Overview: stats, recent games, genre chart |
| `library.html`   | `#library-main`    | Full collection with status/sort filters  |
| `wishlist.html`  | `#wishlist-main`   | Games with status "Want to play"          |
| `profile.html`   | `#profile-main`    | Account settings form                     |
| `login.html`     | `#login-main`      | Log in form (demo only)                   |
| `register.html`  | `#register-main`   | Registration form (demo only)             |
| `admin.html`     | `#admin-main`      | Simulated user management (not linked in the menu) |

Templates hold static markup only. Dynamic parts are marked with hooks that the matching module in
`js/` fills in each time the view is shown: `data-bind="name"` (text), `data-slot="name"` (generated
lists) and `data-progress="Status"` (progress bars). Clickable "add game" buttons use the
`js-open-add` class.

The templates are loaded with AJAX and the scripts are ES modules, so serve the `frontend/` folder over
HTTP (e.g. `python -m http.server`) instead of opening `index.html` from disk.
