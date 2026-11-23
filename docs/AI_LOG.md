# AI Log
 
## Milestone 1
 
### Tools and tasks
- GitHub Copilot (Agent mode, default model): created the footer in index.html.
  Prompt: "Add a footer to index.html with three columns: About, Links and Contact.
  Use Bootstrap 5 classes only, no custom CSS. Do not change anything else."
  Kept the layout, rewrote the contact links myself.
 
### Where the AI was wrong
1. It also changed the navbar, which I did not ask for. I noticed it in the diff,
   clicked Undo and added "Do not change anything else" to the prompt.
2. It used the class "text-md-left", which does not exist in Bootstrap 5.
   I checked the Bootstrap documentation and replaced it with "text-md-start".


## Milestone 2: DAO layer and database

### Tools and tasks
- GitHub Copilot (Agent mode, default model): created the footer in index.html.
  Prompt: "Implement the DAO layer to interact with the database for at least five entities, with a base DAO class for reusable methods."
  It implemented the DAO layer for me and I implemented the CRUD functionality for all entities.
 
### Where the AI was wrong
1. It added completely unneeded entities alongside it which I did not ask for.
2. It excluded the class from the layer aswell.

## Milestone 3: services, routes, OpenAPI, external API

### Tools and tasks
- GitHub Copilot (Agent mode, default model): generated the service layer, FlightPHP routes, OpenAPI/Swagger documentation, and external API integration.
  Prompt: "Create a service for each database entity with application-specific validation rules, ensuring only services call the DAO. Implement FlightPHP routes for all CRUD operations returning JSON with proper HTTP status codes (200, 201, 400, 401, 403, 404, 500). Document all endpoints using OpenAPI with a Swagger UI page, and create an external API service and route with timeout/error handling and the API key stored in the config file."
  Kept the base service structure, route organization, and Swagger setup, and manually refined the validation rules, status codes, and error handling.

### Where the AI was wrong
1. In the service layer, it put Flight::json() responses and HTTP status codes directly inside the service methods instead of keeping them in the routes, and allowed one of the route files to call a DAO directly. I refactored the services to only return data or throw exceptions, and routed all DAO calls through the services.
2. For the external API integration, it hardcoded the API key directly inside the service class and omitted cURL timeout settings. I moved the key to the configuration file and added proper timeout and exception handling.

